<?php

namespace App\Http\Middleware;

use App\Models\AppModuleManagement\AppModuleRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ManagementMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return api_response([], 'Unauthenticated', 401);
        }

        $roleId = $user->user_role_id ?? 0;

        // Step 1: Check if super admin (role_id = -1)
        if ($roleId == -1) {
            return $next($request);
        }

        // Step 2: Check if unauthorized role (role_id = 0)
        if ($roleId == 0) {
            return api_response([], 'Unauthorized', 403);
        }

        // Step 3: Get permissions from cache or database
        $permissions = $this->getUserRolePermissions($roleId);

        // Step 4: Check if user has permission for the current URI
        $currentUri = $this->getCurrentUri($request);
        
        if (!$this->hasPermission($permissions, $currentUri)) {
            return api_response([
                'requested_uri' => $currentUri,
                'role_id' => $roleId,
            ], 'Unauthorized - You do not have permission to access this resource', 403);
        }

        return $next($request);
    }

    /**
     * Get user role permissions from cache or database
     *
     * @param int $roleId
     * @return array
     */
    private function getUserRolePermissions($roleId)
    {
        $cacheKey = "user_{$roleId}_permissions";

        // Try to get from cache first
        $permissions = Cache::get($cacheKey);

        if ($permissions !== null) {
            return $permissions;
        }

        // If not in cache, fetch from database
        $permissions = AppModuleRole::where('user_role_id', $roleId)
            ->where('status', 1)
            ->with([
                'appModule:id,title,slug',
                'appModuleSubModule:id,title,slug',
                'appModuleSubModuleEndpoint:id,app_module_id,app_module_sub_module_id,uri,action_key,title'
            ])
            ->get()
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'app_module_id' => $permission->app_module_id,
                    'app_module_sub_module_id' => $permission->app_module_sub_module_id,
                    'app_module_sub_module_endpoint_id' => $permission->app_module_sub_module_endpoint_id,
                    'user_role_id' => $permission->user_role_id,
                    'app_module' => $permission->appModule ? [
                        'id' => $permission->appModule->id,
                        'title' => $permission->appModule->title,
                        'slug' => $permission->appModule->slug,
                    ] : null,
                    'app_module_sub_module' => $permission->appModuleSubModule ? [
                        'id' => $permission->appModuleSubModule->id,
                        'title' => $permission->appModuleSubModule->title,
                        'slug' => $permission->appModuleSubModule->slug,
                    ] : null,
                    'endpoint' => $permission->appModuleSubModuleEndpoint ? [
                        'id' => $permission->appModuleSubModuleEndpoint->id,
                        'uri' => $permission->appModuleSubModuleEndpoint->uri,
                        'action_key' => $permission->appModuleSubModuleEndpoint->action_key,
                        'title' => $permission->appModuleSubModuleEndpoint->title,
                    ] : null,
                ];
            })
            ->toArray();

        // Cache for 24 hours
        Cache::put($cacheKey, $permissions, now()->addHours(24));

        return $permissions;
    }

    /**
     * Get current request URI in the format stored in permissions
     *
     * @param Request $request
     * @return string
     */
    private function getCurrentUri(Request $request)
    {
        // Get the full path including prefix (e.g., /api/v1/management/user-management/user-roles)
        $fullPath = $request->getPathInfo();
        
        // Remove /api prefix if present
        $path = preg_replace('/^\/api/', '', $fullPath);
        
        // Ensure leading slash
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        // Handle route parameters - replace actual values with placeholders
        $route = $request->route();
        if ($route && $route->parameters()) {
            $uri = $path;
            $parameters = $route->parameters();
            
            // Replace parameter values with placeholders
            foreach ($parameters as $key => $value) {
                // Replace the parameter value with {key} format
                $uri = str_replace('/' . $value, '/{' . $key . '}', $uri);
                $uri = str_replace($value, '{' . $key . '}', $uri);
            }
            
            $path = $uri;
        }

        // Normalize: remove trailing slash unless it's root
        $path = rtrim($path, '/');
        if (empty($path)) {
            $path = '/';
        }

        return $path;
    }

    /**
     * Check if user has permission for the given URI
     *
     * @param array $permissions
     * @param string $requestedUri
     * @return bool
     */
    private function hasPermission(array $permissions, string $requestedUri)
    {
        if (empty($permissions)) {
            return false;
        }

        // Normalize requested URI
        $requestedUri = rtrim($requestedUri, '/');
        if (empty($requestedUri)) {
            $requestedUri = '/';
        }

        foreach ($permissions as $permission) {
            if (!isset($permission['endpoint']['uri'])) {
                continue;
            }

            $permissionUri = $permission['endpoint']['uri'];
            
            // Normalize permission URI
            $permissionUri = rtrim($permissionUri, '/');
            if (empty($permissionUri)) {
                $permissionUri = '/';
            }

            // Exact match
            if ($permissionUri === $requestedUri) {
                return true;
            }

            // Check if requested URI matches with parameter placeholders
            // e.g., /v1/management/user-roles/123 matches /v1/management/user-roles/{id}
            $pattern = preg_replace('/\{[^}]+\}/', '[^/]+', preg_quote($permissionUri, '#'));
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $requestedUri)) {
                return true;
            }

            // Check if permission URI is a prefix of requested URI (with trailing slash)
            // e.g., /v1/management/user-management/user-roles/ matches /v1/management/user-management/user-roles/store
            if (str_starts_with($requestedUri, $permissionUri . '/')) {
                return true;
            }

            // Also check if requested URI matches permission URI with trailing slash
            // e.g., /v1/management/user-management/user-roles matches /v1/management/user-management/user-roles/
            if ($requestedUri . '/' === $permissionUri || $permissionUri . '/' === $requestedUri) {
                return true;
            }
        }

        return false;
    }
}

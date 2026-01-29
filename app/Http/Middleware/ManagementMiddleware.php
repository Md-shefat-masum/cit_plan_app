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
     * Returns array of URIs that the role has permission to access
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

        // If not in cache, fetch URIs directly from app_module_roles table
        $permissions = AppModuleRole::where('user_role_id', $roleId)
            ->where('status', 1)
            ->whereNotNull('uri')
            ->pluck('uri')
            ->toArray();

        // Cache for 24 hours
        Cache::put($cacheKey, $permissions, now()->addHours(24));

        return $permissions;
    }

    /**
     * Get current request path (no parameter normalization).
     * Used for matching against permission URI patterns.
     *
     * @param Request $request
     * @return string
     */
    private function getCurrentUri(Request $request): string
    {
        $path = $request->getPathInfo();

        // Remove /api prefix if present
        $path = preg_replace('#^/api#', '', $path);

        if ($path === '' || !str_starts_with($path, '/')) {
            $path = '/' . ltrim($path, '/');
        }

        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    }

    /**
     * Check if user has permission for the given URI.
     * Matches actual request path against permission URIs.
     * Permission URIs may contain placeholders like {id}, {user_id}, {blog_id}, etc.
     * Each {param} matches exactly one path segment.
     *
     * @param array  $permissions  Array of permission URIs (e.g. /v1/management/user-roles/{id})
     * @param string $requestPath  Actual request path (e.g. /v1/management/user-roles/42)
     * @return bool
     */
    private function hasPermission(array $permissions, string $requestPath): bool
    {
        if (empty($permissions)) {
            return false;
        }

        $requestPath = $this->normalizePath($requestPath);

        foreach ($permissions as $permissionUri) {
            if ($permissionUri === null || $permissionUri === '') {
                continue;
            }

            $permissionUri = $this->normalizePath($permissionUri);

            // Exact match (static routes)
            if ($permissionUri === $requestPath) {
                return true;
            }

            // Pattern match: permission has placeholders e.g. {id}, {user_id}, {blog_id}
            // Build regex from permission URI, replace each {param} with [^/]+
            if ($this->uriPatternMatches($permissionUri, $requestPath)) {
                return true;
            }
        }

        return false;
    }

    private function normalizePath(string $path): string
    {
        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    }

    /**
     * Check if permission URI pattern matches request path.
     * Placeholders {param} match exactly one segment each.
     *
     * @param string $permissionUri e.g. /v1/management/user-roles/{id} or user/{user_id}/blogs/{blog_id}/{comment_id}
     * @param string $requestPath   e.g. /v1/management/user-roles/42 or user/1/blogs/2/3
     * @return bool
     */
    private function uriPatternMatches(string $permissionUri, string $requestPath): bool
    {
        // Replace each {...} with a token before quoting (preg_quote escapes braces)
        $token = '___SEGMENT___';
        $pattern = preg_replace('/\{[^}]+\}/', $token, $permissionUri);

        if ($pattern === null) {
            return false;
        }

        $pattern = preg_quote($pattern, '#');
        $pattern = str_replace($token, '[^/]+', $pattern);
        $pattern = '#^' . $pattern . '$#';

        return (bool) preg_match($pattern, $requestPath);
    }
}

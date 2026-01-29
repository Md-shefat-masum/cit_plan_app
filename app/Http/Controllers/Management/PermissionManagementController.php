<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\AppModuleManagement\AppModule;
use App\Models\AppModuleManagement\AppModuleSubModule;
use App\Models\AppModuleManagement\AppModuleSubModuleEndpoint;
use App\Models\UserManagement\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class PermissionManagementController extends Controller
{
    /**
     * Show permission management page
     * Only accessible if auth=admin (super admin role_id = -1)
     */
    public function showPermissionPage(Request $request)
    {
        // Check if auth=admin query parameter exists
        $authParam = $request->query('auth');
        
        if ($authParam !== 'admin') {
            abort(404);
        }

        // Read token from token.txt file
        $tokenPath = base_path('token.txt');
        $token = '';
        if (file_exists($tokenPath)) {
            $token = trim(file_get_contents($tokenPath));
        }

        return view('management.permissions', ['token' => $token]);
    }

    /**
     * Validate token and return user info
     */
    public function validateToken(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token is required'
            ], 400);
        }

        try {
            // Set token and authenticate
            $user = JWTAuth::setToken($token)->authenticate();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token'
                ], 401);
            }

            // Check if user is super admin (role_id = -1)
            if ($user->user_role_id != -1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - Super admin access required'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Token validated successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id' => $user->user_role_id,
                ]
            ], 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token expired'
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token validation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all modules
     */
    public function getModules(Request $request)
    {
        try {
            // Optional: Validate token if provided
            $this->validateTokenIfProvided($request);

            $modules = AppModule::where('status', 1)
                ->orderBy('title', 'asc')
                ->get(['id', 'title', 'slug']);

            return response()->json([
                'success' => true,
                'data' => $modules
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch modules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sub-modules by module ID
     */
    public function getSubModules(Request $request, $moduleId = null)
    {
        try {
            // Optional: Validate token if provided
            $this->validateTokenIfProvided($request);

            $query = AppModuleSubModule::where('status', 1);

            if ($moduleId) {
                $query->where('app_module_id', $moduleId);
            }

            $subModules = $query->orderBy('title', 'asc')
                ->get(['id', 'app_module_id', 'title', 'slug']);

            return response()->json([
                'success' => true,
                'data' => $subModules
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch sub-modules',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get endpoints by module and sub-module
     */
    public function getEndpoints(Request $request, $moduleId = null, $subModuleId = null)
    {
        try {
            // Optional: Validate token if provided
            $this->validateTokenIfProvided($request);

            $query = AppModuleSubModuleEndpoint::where('status', 1);

            if ($moduleId) {
                $query->where('app_module_id', $moduleId);
            }

            if ($subModuleId) {
                $query->where('app_module_sub_module_id', $subModuleId);
            }

            $endpoints = $query->orderBy('uri', 'asc')
                ->get(['id', 'app_module_id', 'app_module_sub_module_id', 'uri', 'action_key', 'title']);

            return response()->json([
                'success' => true,
                'data' => $endpoints
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch endpoints',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all endpoints grouped by module and sub-module
     */
    public function getAllEndpointsGrouped(Request $request)
    {
        try {
            // Optional: Validate token if provided
            $this->validateTokenIfProvided($request);

            $endpoints = AppModuleSubModuleEndpoint::where('status', 1)
                ->with([
                    'appModule:id,title,slug',
                    'appModuleSubModule:id,title,slug'
                ])
                ->orderBy('uri', 'asc')
                ->get(['id', 'app_module_id', 'app_module_sub_module_id', 'uri', 'action_key', 'title']);

            // Group by module and sub-module
            $grouped = [];
            foreach ($endpoints as $endpoint) {
                $moduleId = $endpoint->app_module_id ?? 'unknown';
                $subModuleId = $endpoint->app_module_sub_module_id ?? 'unknown';
                
                if (!isset($grouped[$moduleId])) {
                    $grouped[$moduleId] = [
                        'id' => $endpoint->appModule ? $endpoint->appModule->id : null,
                        'title' => $endpoint->appModule ? $endpoint->appModule->title : 'Unknown',
                        'slug' => $endpoint->appModule ? $endpoint->appModule->slug : null,
                        'sub_modules' => []
                    ];
                }

                if (!isset($grouped[$moduleId]['sub_modules'][$subModuleId])) {
                    $grouped[$moduleId]['sub_modules'][$subModuleId] = [
                        'id' => $endpoint->appModuleSubModule ? $endpoint->appModuleSubModule->id : null,
                        'title' => $endpoint->appModuleSubModule ? $endpoint->appModuleSubModule->title : 'Unknown',
                        'slug' => $endpoint->appModuleSubModule ? $endpoint->appModuleSubModule->slug : null,
                        'endpoints' => []
                    ];
                }

                $grouped[$moduleId]['sub_modules'][$subModuleId]['endpoints'][] = [
                    'id' => $endpoint->id,
                    'uri' => $endpoint->uri,
                    'action_key' => $endpoint->action_key,
                    'title' => $endpoint->title,
                ];
            }

            // Convert to array format
            $result = [];
            foreach ($grouped as $module) {
                $module['sub_modules'] = array_values($module['sub_modules']);
                $result[] = $module;
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch grouped endpoints',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all roles
     */
    public function getRoles(Request $request)
    {
        try {
            // Optional: Validate token if provided
            $this->validateTokenIfProvided($request);

            $roles = UserRole::where('status', 1)
                ->orderBy('title', 'asc')
                ->get(['id', 'title', 'slug']);

            return response()->json([
                'success' => true,
                'data' => $roles
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get permissions for a role
     */
    public function getRolePermissions(Request $request, $roleId)
    {
        try {
            // Optional: Validate token if provided
            $this->validateTokenIfProvided($request);

            $permissions = \App\Models\AppModuleManagement\AppModuleRole::where('user_role_id', $roleId)
                ->where('status', 1)
                ->whereNotNull('uri')
                ->with([
                    'appModule:id,title',
                    'appModuleSubModule:id,title',
                    'appModuleSubModuleEndpoint:id,uri,action_key,title'
                ])
                ->get();

            $permissionsData = $permissions->map(function($p) {
                return [
                    'app_module_sub_module_endpoint_id' => $p->app_module_sub_module_endpoint_id,
                    'uri' => $p->uri,
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => $permissionsData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch role permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate token if provided in request header
     */
    private function validateTokenIfProvided(Request $request)
    {
        $token = $request->bearerToken() ?? $request->header('Authorization');
        
        if ($token) {
            // Remove 'Bearer ' prefix if present
            $token = str_replace('Bearer ', '', $token);
            
            try {
                $user = JWTAuth::setToken($token)->authenticate();
                
                if (!$user || $user->user_role_id != -1) {
                    throw new \Exception('Unauthorized - Super admin access required');
                }
            } catch (\Exception $e) {
                throw new \Exception('Invalid or unauthorized token');
            }
        }
    }
}

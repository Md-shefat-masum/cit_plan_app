<?php

namespace App\Http\Controllers\Management\V1\AppModuleManagement;

use App\Http\Controllers\Controller;
use App\Models\AppModuleManagement\AppModuleRole;
use App\Models\AppModuleManagement\AppModuleSubModuleEndpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AppModulesPermissionSetController extends Controller
{
    /**
     * Add permissions to a role
     */
    public function add_permission_to_role(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer|exists:user_roles,id',
            'scopes' => 'required|array|min:1',
            'scopes.*.app_module_id' => 'nullable|integer|exists:app_modules,id',
            'scopes.*.app_module_sub_module_id' => 'nullable|integer|exists:app_module_sub_modules,id',
            'scopes.*.app_module_sub_module_endpoint_id' => 'required|integer|exists:app_module_sub_module_endpoints,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            DB::beginTransaction();

            $roleId = $request->input('role_id');
            $scopes = $request->input('scopes', []);

            // Step 1: Collect all endpoint IDs from request
            $endpointIdsFromRequest = [];
            
            foreach ($scopes as $scope) {
                if (!empty($scope['app_module_sub_module_endpoint_id'])) {
                    $endpointIdsFromRequest[] = $scope['app_module_sub_module_endpoint_id'];
                }
            }

            // Step 2: Find all endpoints by IDs in a single query (optimized)
            $foundEndpoints = AppModuleSubModuleEndpoint::whereIn('id', $endpointIdsFromRequest)
                ->get()
                ->keyBy('id');

            // Step 3: Build endpoint data map from request scopes
            $endpointIds = [];
            $endpointDataMap = [];

            foreach ($scopes as $scope) {
                $endpointId = $scope['app_module_sub_module_endpoint_id'] ?? null;
                
                if ($endpointId) {
                    $endpoint = $foundEndpoints->get($endpointId);
                    
                    if ($endpoint) {
                        $endpointIds[] = $endpointId;
                        $endpointDataMap[$endpointId] = [
                            'app_module_id' => $scope['app_module_id'] ?? $endpoint->app_module_id,
                            'app_module_sub_module_id' => $scope['app_module_sub_module_id'] ?? $endpoint->app_module_sub_module_id,
                            'app_module_sub_module_endpoint_id' => $endpointId,
                        ];
                    }
                }
            }

            // Remove duplicates
            $endpointIds = array_unique($endpointIds);

            if (empty($endpointIds)) {
                DB::rollBack();
                return api_response([], 'No valid endpoints found in scopes', 400);
            }

            // Step 4: Get all existing permissions for this role in a single query (optimized)
            $allExistingForRole = AppModuleRole::where('user_role_id', $roleId)
                ->pluck('app_module_sub_module_endpoint_id')
                ->toArray();

            // Step 5: Find which endpoints are missing (need to be added)
            $endpointsToAdd = array_diff($endpointIds, $allExistingForRole);

            // Step 6: Find which endpoints should be removed (exist in DB but not in request)
            $endpointsToRemove = array_diff($allExistingForRole, $endpointIds);

            // Step 7: Delete permissions that are not in the request (batch delete for performance)
            if (!empty($endpointsToRemove)) {
                AppModuleRole::where('user_role_id', $roleId)
                    ->whereIn('app_module_sub_module_endpoint_id', $endpointsToRemove)
                    ->delete();
            }

            // Step 8: Insert new permissions (only missing ones, bulk insert for better performance)
            if (!empty($endpointsToAdd)) {
                $permissionsToInsert = [];
                $creator = auth('api')->id() ?? 0;
                $now = now();

                foreach ($endpointsToAdd as $endpointId) {
                    if (isset($endpointDataMap[$endpointId])) {
                        $permissionsToInsert[] = [
                            'app_module_id' => $endpointDataMap[$endpointId]['app_module_id'],
                            'app_module_sub_module_id' => $endpointDataMap[$endpointId]['app_module_sub_module_id'],
                            'app_module_sub_module_endpoint_id' => $endpointId,
                            'user_role_id' => $roleId,
                            'status' => 1,
                            'slug' => uniqid(),
                            'creator' => $creator,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                // Bulk insert in chunks for better performance with large datasets
                if (!empty($permissionsToInsert)) {
                    $chunks = array_chunk($permissionsToInsert, 500); // Insert in chunks of 500
                    foreach ($chunks as $chunk) {
                        AppModuleRole::insert($chunk);
                    }
                }
            }
            
            DB::commit();

            // Step 9: Cache user role permissions after all steps complete
            $this->cacheUserRolePermissions($roleId);

            return api_response([
                'role_id' => $roleId,
                'added_count' => count($endpointsToAdd),
                'removed_count' => count($endpointsToRemove),
                'total_permissions' => count($endpointIds),
            ], 'Permissions updated successfully', 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while setting permissions', 500);
        }
    }

    /**
     * Cache user role permissions
     * 
     * @param int $roleId
     * @return void
     */
    private function cacheUserRolePermissions($roleId)
    {
        try {
            // Fetch all permissions for this role with relationships
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

            // Cache key format: user_{role_id}_permissions
            $cacheKey = "user_{$roleId}_permissions";
            
            // Cache for 24 hours (86400 seconds) or until manually cleared
            Cache::put($cacheKey, $permissions, now()->addHours(24));

        } catch (\Exception $e) {
            // Log error but don't fail the request if caching fails
            Log::error('Failed to cache user role permissions', [
                'role_id' => $roleId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

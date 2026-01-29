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
            'scopes' => 'required|array',
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
                            'uri' => $endpoint->uri, // Store URI for permission checking
                        ];
                    }
                }
            }

            // Remove duplicates
            $endpointIds = array_unique($endpointIds);

            if (empty($endpointIds)) {
                // Revoke all: delete existing permissions and cache empty
                AppModuleRole::where('user_role_id', $roleId)->delete();
                $cacheKey = "user_{$roleId}_permissions";
                Cache::put($cacheKey, [], now()->addDays(7));
                DB::commit();
                return api_response([
                    'role_id' => $roleId,
                    'added_count' => 0,
                    'removed_count' => 0,
                ], 'All permissions revoked for role', 200);
            }

            // Step 4: Get all existing permissions for this role by URI (optimized)
            // Track by URI instead of endpoint_id since IDs can change randomly
            $existingUris = AppModuleRole::where('user_role_id', $roleId)
                ->whereNotNull('uri')
                ->pluck('uri')
                ->toArray();
            
            // Build URI list from request endpoints
            $requestUris = [];
            foreach ($endpointIds as $endpointId) {
                if (isset($endpointDataMap[$endpointId]['uri'])) {
                    $requestUris[] = $endpointDataMap[$endpointId]['uri'];
                }
            }

            // Step 5: Find which URIs are missing (need to be added)
            $urisToAdd = [];
            foreach ($endpointIds as $endpointId) {
                if (isset($endpointDataMap[$endpointId]['uri'])) {
                    $uri = $endpointDataMap[$endpointId]['uri'];
                    if (!in_array($uri, $existingUris)) {
                        $urisToAdd[] = $endpointId;
                    }
                }
            }

            // Step 6: Find which URIs should be removed (exist in DB but not in request)
            $urisToRemove = array_diff($existingUris, $requestUris);

            // Step 7: Delete permissions that are not in the request (batch delete by URI for performance)
            if (!empty($urisToRemove)) {
                AppModuleRole::where('user_role_id', $roleId)
                    ->whereIn('uri', $urisToRemove)
                    ->delete();
            }

            // Step 8: Insert new permissions (only missing URIs, bulk insert for better performance)
            if (!empty($urisToAdd)) {
                $permissionsToInsert = [];
                $creator = auth('api')->id() ?? 0;
                $now = now();

                foreach ($urisToAdd as $endpointId) {
                    if (isset($endpointDataMap[$endpointId])) {
                        $permissionsToInsert[] = [
                            'app_module_id' => $endpointDataMap[$endpointId]['app_module_id'],
                            'app_module_sub_module_id' => $endpointDataMap[$endpointId]['app_module_sub_module_id'],
                            'app_module_sub_module_endpoint_id' => $endpointId,
                            'uri' => $endpointDataMap[$endpointId]['uri'], // Store URI for permission checking
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
                'added_count' => count($urisToAdd),
                'removed_count' => count($urisToRemove),
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
            // Fetch all permissions for this role - get URIs directly from app_module_roles table
            $permissions = AppModuleRole::where('user_role_id', $roleId)
                ->where('status', 1)
                ->whereNotNull('uri')
                ->pluck('uri')
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

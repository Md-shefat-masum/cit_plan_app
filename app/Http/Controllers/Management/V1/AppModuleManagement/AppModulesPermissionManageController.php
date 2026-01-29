<?php

namespace App\Http\Controllers\Management\V1\AppModuleManagement;

use App\Http\Controllers\Controller;
use App\Models\AppModuleManagement\AppModule;
use App\Models\AppModuleManagement\AppModuleSubModule;
use App\Models\AppModuleManagement\AppModuleSubModuleEndpoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppModulesPermissionManageController extends Controller
{
    /**
     * Register all management routes into database
     */
    public function register_managments_into_db()
    {
        try {
            $routesFile = base_path('routes/api.php');
            $content = file_get_contents($routesFile);

            // Parse management groups and their sub-modules
            $managementGroups = $this->parseManagementGroups($content);

            if (empty($managementGroups)) {
                return api_response([], 'No management groups found', 404);
            }

            // Disable foreign key checks to allow truncating tables with foreign key constraints
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Truncate tables in reverse order (child tables first, then parent tables)
            DB::table('app_module_sub_module_endpoints')->truncate();
            DB::table('app_module_sub_modules')->truncate();
            DB::table('app_modules')->truncate();
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Step 1: Save app_modules
            $appModules = [];
            foreach ($managementGroups as $groupName => $groupData) {
                $appModules[] = [
                    'title' => $this->formatTitle($groupName),
                    'slug' => uniqid(),
                    'status' => 1,
                    'creator' => auth('api')->id() ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'original_name' => $groupName, // Store original name for mapping
                ];
            }
            
            // Sort alphabetically by title
            usort($appModules, function($a, $b) {
                return strcmp($a['title'], $b['title']);
            });

            // Insert app_modules and map IDs
            $groupNameToModuleId = [];
            foreach ($appModules as $module) {
                $appModule = AppModule::create([
                    'title' => $module['title'],
                    'slug' => $module['slug'],
                    'status' => $module['status'],
                    'creator' => $module['creator'],
                    'created_at' => $module['created_at'],
                    'updated_at' => $module['updated_at'],
                ]);
                $groupNameToModuleId[$module['original_name']] = $appModule->id;
            }

            // Step 2: Save app_module_sub_modules
            $allSubModules = [];
            
            foreach ($managementGroups as $groupName => $groupData) {
                $appModuleId = $groupNameToModuleId[$groupName] ?? null;
                foreach ($groupData['sub_modules'] as $subModuleName => $endpoints) {
                    $allSubModules[] = [
                        'app_module_id' => $appModuleId,
                        'title' => $this->formatTitle($subModuleName),
                        'slug' => uniqid(),
                        'status' => 1,
                        'creator' => auth('api')->id() ?? 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'sub_module_name' => $subModuleName,
                        'management_group_name' => $groupName, // Store for URI building
                        'endpoints' => $endpoints,
                    ];
                }
            }

            // Sort alphabetically by title
            usort($allSubModules, function($a, $b) {
                return strcmp($a['title'], $b['title']);
            });

            // Insert app_module_sub_modules
            $subModuleNameToId = [];
            foreach ($allSubModules as $subModule) {
                $subModuleData = [
                    'app_module_id' => $subModule['app_module_id'],
                    'title' => $subModule['title'],
                    'slug' => $subModule['slug'],
                    'status' => $subModule['status'],
                    'creator' => $subModule['creator'],
                    'created_at' => $subModule['created_at'],
                    'updated_at' => $subModule['updated_at'],
                ];
                $appModuleSubModule = AppModuleSubModule::create($subModuleData);
                $subModuleNameToId[$subModule['sub_module_name']] = $appModuleSubModule->id;
            }

            // Step 3: Save app_module_sub_module_endpoints
            $allEndpoints = [];

            foreach ($allSubModules as $subModule) {
                $appModuleId = $subModule['app_module_id'] ?? null;
                $appModuleSubModuleId = $subModuleNameToId[$subModule['sub_module_name']] ?? null;
                $managementGroupName = $subModule['management_group_name'] ?? '';
                $subModuleName = $subModule['sub_module_name'] ?? '';
                
                foreach ($subModule['endpoints'] as $endpoint) {
                    // Parse endpoint: "GET /" -> full uri: "/v1/management/user-management/user-roles/", action_key: "index"
                    $endpointParts = $this->parseEndpoint($endpoint, $managementGroupName, $subModuleName);
                    
                    $allEndpoints[] = [
                        'app_module_id' => $appModuleId,
                        'app_module_sub_module_id' => $appModuleSubModuleId,
                        'uri' => $endpointParts['uri'],
                        'action_key' => $endpointParts['action_key'],
                        'title' => $endpoint,
                        'slug' => uniqid(),
                        'status' => 1,
                        'creator' => auth('api')->id() ?? 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Sort alphabetically by title
            usort($allEndpoints, function($a, $b) {
                return strcmp($a['title'], $b['title']);
            });

            // Insert app_module_sub_module_endpoints
            if (!empty($allEndpoints)) {
                AppModuleSubModuleEndpoint::insert($allEndpoints);
            }

            return api_response([
                'app_modules_count' => count($appModules),
                'app_module_sub_modules_count' => count($allSubModules),
                'app_module_sub_module_endpoints_count' => count($allEndpoints),
                'app_modules' => array_map(function($m) {
                    return ['title' => $m['title'], 'original_name' => $m['original_name']];
                }, $appModules),
            ], 'Management routes registered successfully', 200);

        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ], 'An error occurred while registering management routes', 500);
        }
    }

    /**
     * Parse management groups from routes file content
     */
    private function parseManagementGroups($content)
    {
        $managementGroups = [];
        
        // Find the management route group section
        // Pattern: Route::group(['prefix' => 'management'], function () { ... });
        $pattern = '/Route::group\s*\(\s*\[[^\]]*?[\'"]prefix[\'"]\s*=>\s*[\'"]management[\'"][^\]]*?\],\s*function\s*\(\)\s*\{/';
        
        if (!preg_match($pattern, $content, $match, PREG_OFFSET_CAPTURE)) {
            return $managementGroups;
        }

        $startPos = $match[0][1] + strlen($match[0][0]);
        
        // Extract the content inside the management group by tracking braces
        $depth = 1;
        $managementContent = '';
        for ($i = $startPos; $i < strlen($content); $i++) {
            $char = $content[$i];
            if ($char === '{') {
                $depth++;
            } elseif ($char === '}') {
                $depth--;
                if ($depth === 0) {
                    break;
                }
            }
            $managementContent .= $char;
        }

        // Extract all direct child groups (management groups)
        $this->extractManagementGroups($managementContent, $managementGroups);

        return $managementGroups;
    }

    /**
     * Extract management groups from content
     */
    private function extractManagementGroups($content, &$result)
    {
        $pattern = '/Route::group\s*\(\s*\[([^\]]+)\],\s*function\s*\(\)\s*\{/';
        
        preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
        
        foreach ($matches[0] as $index => $match) {
            $fullMatch = $match[0];
            $attributes = $matches[1][$index][0];
            $startPos = $match[1] + strlen($fullMatch);
            
            // Extract prefix name
            if (preg_match('/[\'"]prefix[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]/', $attributes, $prefixMatch)) {
                $prefixName = $prefixMatch[1];
                
                // Only process groups that contain 'management' in their name
                if (strpos($prefixName, 'management') === false) {
                    continue;
                }
                
                // Extract the content inside this group by tracking braces
                $depth = 1;
                $groupContent = '';
                for ($i = $startPos; $i < strlen($content); $i++) {
                    $char = $content[$i];
                    if ($char === '{') {
                        $depth++;
                    } elseif ($char === '}') {
                        $depth--;
                        if ($depth === 0) {
                            break;
                        }
                    }
                    $groupContent .= $char;
                }
                
                // Extract sub-modules from this management group
                $subModules = $this->extractSubModules($groupContent);
                $result[$prefixName] = [
                    'sub_modules' => $subModules
                ];
            }
        }
    }

    /**
     * Extract sub-modules from a management group content
     */
    private function extractSubModules($groupContent)
    {
        $subModules = [];
        
        // Find all Route::group calls in the content
        $pattern = '/Route::group\s*\(\s*\[([^\]]+)\],\s*function\s*\(\)\s*\{/';
        preg_match_all($pattern, $groupContent, $matches, PREG_OFFSET_CAPTURE);
        
        foreach ($matches[0] as $index => $match) {
            $fullMatch = $match[0];
            $attributes = $matches[1][$index][0];
            $startPos = $match[1] + strlen($fullMatch);
            
            // Extract prefix name
            if (preg_match('/[\'"]prefix[\'"]\s*=>\s*[\'"]([^\'"]+)[\'"]/', $attributes, $prefixMatch)) {
                $subModuleName = $prefixMatch[1];
                
                // Skip if this is another management group (nested management groups)
                if (strpos($subModuleName, 'management') !== false && $subModuleName !== 'management') {
                    continue;
                }
                
                // Extract the content inside this sub-module group by tracking braces
                $depth = 1;
                $subModuleContent = '';
                for ($i = $startPos; $i < strlen($groupContent); $i++) {
                    $char = $groupContent[$i];
                    if ($char === '{') {
                        $depth++;
                    } elseif ($char === '}') {
                        $depth--;
                        if ($depth === 0) {
                            break;
                        }
                    }
                    $subModuleContent .= $char;
                }
                
                // Extract endpoints from common_routes or direct routes
                $endpoints = $this->parseEndpoints($subModuleContent);
                $subModules[$subModuleName] = $endpoints;
            }
        }
        
        return $subModules;
    }

    /**
     * Parse endpoints from sub-module content
     */
    private function parseEndpoints($subModuleContent)
    {
        $endpoints = [];

        // Check if common_routes is used
        if (preg_match('/common_routes\s*\([^,]+,\s*[\'"]([^\'"]+)[\'"]\s*\)/', $subModuleContent, $controllerMatch)) {
            // Extract routes from common_routes function (defined in routes/api.php)
            $endpoints = [
                'GET /',
                'POST /store',
                'POST /update',
                'POST /soft-delete',
                'POST /restore',
                'POST /destroy',
                'POST /import',
                'GET /analytics',
                'GET /{id}',
            ];
        } else {
            // Parse individual routes if any
            preg_match_all('/Route::(get|post|put|patch|delete)\s*\(\s*[\'"]([^\'"]+)[\'"]/', $subModuleContent, $routeMatches, PREG_SET_ORDER);
            
            foreach ($routeMatches as $routeMatch) {
                $method = strtoupper($routeMatch[1]);
                $path = $routeMatch[2];
                $endpoints[] = "$method $path";
            }
        }

        return $endpoints;
    }

    /**
     * Format title from kebab-case or snake_case to Title Case
     */
    private function formatTitle($name)
    {
        return Str::title(str_replace(['-', '_'], ' ', $name));
    }

    /**
     * Parse endpoint string to extract full URI and action key
     * Examples:
     * "GET /" with group="user-management", subModule="user-roles" 
     *   -> uri: "/v1/management/user-management/user-roles/", action_key: "index"
     * "POST /store" with group="user-management", subModule="user-roles"
     *   -> uri: "/v1/management/user-management/user-roles/store", action_key: "store"
     * "GET /{id}" with group="user-management", subModule="user-roles"
     *   -> uri: "/v1/management/user-management/user-roles/{id}", action_key: "show"
     */
    private function parseEndpoint($endpoint, $managementGroupName = '', $subModuleName = '')
    {
        // Split by space: "GET /" -> ["GET", "/"]
        $parts = explode(' ', trim($endpoint), 2);
        $method = $parts[0] ?? '';
        $path = $parts[1] ?? '/';
        
        // Build full URI path: /v1/management/{management-group}/{sub-module}/{endpoint-path}
        $basePath = '/v1/management';
        $fullPath = $basePath;
        
        if (!empty($managementGroupName)) {
            $fullPath .= '/' . $managementGroupName;
        }
        
        if (!empty($subModuleName)) {
            $fullPath .= '/' . $subModuleName;
        }
        
        // Append endpoint path (remove leading slash if present to avoid double slashes)
        $endpointPath = ltrim($path, '/');
        if (!empty($endpointPath)) {
            $fullPath .= '/' . $endpointPath;
        } else {
            // If path is just "/", ensure trailing slash
            $fullPath .= '/';
        }
        
        $uri = $fullPath;
        
        // Map common routes to action keys
        $actionKeyMap = [
            'GET /' => 'index',
            'POST /store' => 'store',
            'POST /update' => 'update',
            'POST /soft-delete' => 'soft_delete',
            'POST /restore' => 'restore',
            'POST /destroy' => 'destroy',
            'POST /import' => 'import',
            'GET /analytics' => 'analytics',
            'GET /{id}' => 'show',
        ];
        
        $actionKey = $actionKeyMap[$endpoint] ?? strtolower(str_replace(['/', '{', '}'], '', $path));
        
        // If action_key is empty or just "/", use method-based default
        if (empty($actionKey) || $actionKey === '/') {
            $methodActionMap = [
                'GET' => 'index',
                'POST' => 'store',
                'PUT' => 'update',
                'PATCH' => 'update',
                'DELETE' => 'destroy',
            ];
            $actionKey = $methodActionMap[$method] ?? 'index';
        }
        
        return [
            'uri' => $uri,
            'action_key' => $actionKey,
        ];
    }
}

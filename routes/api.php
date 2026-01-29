<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Management\V1\AppModuleManagement\AppModule\AppModuleController;
use App\Http\Controllers\Management\V1\AppModuleManagement\AppModuleSubModule\AppModuleSubModuleController;
use App\Http\Controllers\Management\V1\AppModuleManagement\AppModuleSubModuleEndpoint\AppModuleSubModuleEndpointController;
use App\Http\Controllers\Management\V1\UserManagement\User\UserController;
use App\Http\Controllers\Management\V1\UserManagement\UserRole\UserRoleController;
use Illuminate\Support\Facades\Route;

if (! function_exists('common_routes')) {
    function common_routes($route, $controller)
    {
        Route::get('/', $controller . '@index');
        Route::post('/store', $controller . '@store');
        Route::post('/update', $controller . '@update');
        Route::post('/soft-delete', $controller . '@soft_delete');
        Route::post('/restore', $controller . '@restore');
        Route::post('/destroy', $controller . '@destroy');
        Route::post('/import', $controller . '@import');

        Route::get('/analytics', $controller . '@analytics');

        Route::get('/{id}', $controller . '@show');
    }
}

// Public routes

// Protected routes (require authentication)
Route::group(['prefix' => 'v1',], function () {

    // public routes will go here
    Route::post('auth/login', [AuthController::class, 'login'])->name('api.login');

    // Permission management API routes (public for web UI, token validated in controller)
    Route::prefix('permission-management')->group(function () {
        Route::post('validate-token', [\App\Http\Controllers\Management\PermissionManagementController::class, 'validateToken']);
        Route::get('modules', [\App\Http\Controllers\Management\PermissionManagementController::class, 'getModules']);
        Route::get('sub-modules/{moduleId?}', [\App\Http\Controllers\Management\PermissionManagementController::class, 'getSubModules']);
        Route::get('endpoints/{moduleId?}/{subModuleId?}', [\App\Http\Controllers\Management\PermissionManagementController::class, 'getEndpoints']);
        Route::get('endpoints-grouped', [\App\Http\Controllers\Management\PermissionManagementController::class, 'getAllEndpointsGrouped']);
        Route::get('roles', [\App\Http\Controllers\Management\PermissionManagementController::class, 'getRoles']);
        Route::get('role-permissions/{roleId}', [\App\Http\Controllers\Management\PermissionManagementController::class, 'getRolePermissions']);
    });

    // protected routes will go here
    Route::group(['middleware' => 'api.auth'], function () {

        // auth routes will go here
        Route::group(['prefix' => 'auth'], function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/me', [AuthController::class, 'me']);
        });

        // superadmin routes will go here
        Route::group(['middleware' => 'superadmin'], function () {
            // Register management routes into database
            Route::post('register_managments_into_db', [\App\Http\Controllers\Management\V1\AppModuleManagement\AppModulesPermissionManageController::class, 'register_managments_into_db']);
    
            // Add permissions to role
            Route::post('add-permission-to-role', [\App\Http\Controllers\Management\V1\AppModuleManagement\AppModulesPermissionSetController::class, 'add_permission_to_role']);
        });

        // management routes will go here
        Route::group(['prefix' => 'management', 'middleware' => 'management'], function () {

            // user management routes will go here
            Route::group(['prefix' => 'user-management'], function () {
                // user management routes will go here
                Route::group(['prefix' => 'users'], function () {
                    common_routes(Route::class, 'App\Http\Controllers\Management\V1\UserManagement\User\UserController');
                    // extra routes will go here
                });

                // user role management routes will go here
                Route::group(['prefix' => 'user-roles'], function () {
                    common_routes(Route::class, 'App\Http\Controllers\Management\V1\UserManagement\UserRole\UserRoleController');
                    // extra routes will go here
                });
            });

            // app module management routes will go here
            Route::group(['prefix' => 'app-module-management'], function () {
                Route::group(['prefix' => 'app-modules'], function () {
                    common_routes(Route::class, 'App\Http\Controllers\Management\V1\AppModuleManagement\AppModule\AppModuleController');
                    // extra routes will go here
                });

                // app module sub module management routes will go here
                Route::group(['prefix' => 'app-module-sub-modules'], function () {
                    common_routes(Route::class, 'App\Http\Controllers\Management\V1\AppModuleManagement\AppModuleSubModule\AppModuleSubModuleController');
                    // extra routes will go here
                });

                // app module sub module endpoint management routes will go here
                Route::group(['prefix' => 'app-module-sub-module-endpoints'], function () {
                    common_routes(Route::class, 'App\Http\Controllers\Management\V1\AppModuleManagement\AppModuleSubModuleEndpoint\AppModuleSubModuleEndpointController');
                    // extra routes will go here
                });
            });

        });
        
    });
});

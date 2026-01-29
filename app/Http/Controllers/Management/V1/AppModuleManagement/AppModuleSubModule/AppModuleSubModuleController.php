<?php

namespace App\Http\Controllers\Management\V1\AppModuleManagement\AppModuleSubModule;

use App\Http\Controllers\Controller;
use App\Http\Actions\AppModuleManagement\AppModuleSubModule\AppModuleSubModuleIndexAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModule\AppModuleSubModuleStoreAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModule\AppModuleSubModuleShowAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModule\AppModuleSubModuleUpdateAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModule\AppModuleSubModuleSoftDeleteAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModule\AppModuleSubModuleRestoreAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModule\AppModuleSubModuleDestroyAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModule\AppModuleSubModuleImportAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModule\AppModuleSubModuleAnalyticsAction;
use App\Models\AppModuleManagement\AppModuleSubModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppModuleSubModuleController extends Controller
{
    protected $model = AppModuleSubModule::class;
    protected $table_name = 'app_module_sub_modules';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = AppModuleSubModuleIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'App module sub modules retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving app module sub modules', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_module_id' => 'nullable|integer|exists:app_modules,id',
            'title' => 'nullable|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $data = $request->all();
            $data['slug'] = uniqid();
            $data['creator'] = auth('api')->id() ?? 0;
            $data['status'] = $request->input('status', 1);

            $result = AppModuleSubModuleStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'App module sub module created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating app module sub module', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = AppModuleSubModuleShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'App module sub module not found', 404);
            }
            return api_response($data, 'App module sub module retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving app module sub module', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:app_module_sub_modules,id',
            'app_module_id' => 'nullable|integer|exists:app_modules,id',
            'title' => 'nullable|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $data = $request->all();
            $result = AppModuleSubModuleUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'App module sub module not found', 404);
            }
            return api_response($result, 'App module sub module updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating app module sub module', 500);
        }
    }

    /**
     * Soft delete the specified resource.
     */
    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:app_module_sub_modules,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = AppModuleSubModuleSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'App module sub module not found', 404);
            }
            return api_response($result, 'App module sub module soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting app module sub module', 500);
        }
    }

    /**
     * Restore the specified resource.
     */
    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:app_module_sub_modules,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = AppModuleSubModuleRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'App module sub module not found', 404);
            }
            return api_response($result, 'App module sub module restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring app module sub module', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:app_module_sub_modules,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = AppModuleSubModuleDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'App module sub module not found', 404);
            }
            return api_response($result, 'App module sub module deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting app module sub module', 500);
        }
    }

    /**
     * Import resources.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.app_module_id' => 'nullable|integer|exists:app_modules,id',
            'data.*.title' => 'nullable|string|max:100',
            'data.*.status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $data = $request->input('data');
            $result = AppModuleSubModuleImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'App module sub modules imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing app module sub modules', 500);
        }
    }

    /**
     * Get analytics data.
     */
    public function analytics(Request $request)
    {
        try {
            $data = AppModuleSubModuleAnalyticsAction::execute($this->model, $this->table_name);
            return api_response($data, 'Analytics retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving analytics', 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Management\V1\AppModuleManagement\AppModuleSubModuleEndpoint;

use App\Http\Controllers\Controller;
use App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint\AppModuleSubModuleEndpointIndexAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint\AppModuleSubModuleEndpointStoreAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint\AppModuleSubModuleEndpointShowAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint\AppModuleSubModuleEndpointUpdateAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint\AppModuleSubModuleEndpointSoftDeleteAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint\AppModuleSubModuleEndpointRestoreAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint\AppModuleSubModuleEndpointDestroyAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint\AppModuleSubModuleEndpointImportAction;
use App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint\AppModuleSubModuleEndpointAnalyticsAction;
use App\Models\AppModuleManagement\AppModuleSubModuleEndpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppModuleSubModuleEndpointController extends Controller
{
    protected $model = AppModuleSubModuleEndpoint::class;
    protected $table_name = 'app_module_sub_module_endpoints';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = AppModuleSubModuleEndpointIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'App module sub module endpoints retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving app module sub module endpoints', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_module_id' => 'nullable|integer|exists:app_modules,id',
            'app_module_sub_module_id' => 'nullable|integer|exists:app_module_sub_modules,id',
            'uri' => 'nullable|string|max:200',
            'action_key' => 'nullable|string|max:100',
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

            $result = AppModuleSubModuleEndpointStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'App module sub module endpoint created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating app module sub module endpoint', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = AppModuleSubModuleEndpointShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'App module sub module endpoint not found', 404);
            }
            return api_response($data, 'App module sub module endpoint retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving app module sub module endpoint', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:app_module_sub_module_endpoints,id',
            'app_module_sub_module_id' => 'nullable|integer|exists:app_module_sub_modules,id',
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
            $result = AppModuleSubModuleEndpointUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'App module sub module endpoint not found', 404);
            }
            return api_response($result, 'App module sub module endpoint updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating app module sub module endpoint', 500);
        }
    }

    /**
     * Soft delete the specified resource.
     */
    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:app_module_sub_module_endpoints,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = AppModuleSubModuleEndpointSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'App module sub module endpoint not found', 404);
            }
            return api_response($result, 'App module sub module endpoint soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting app module sub module endpoint', 500);
        }
    }

    /**
     * Restore the specified resource.
     */
    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:app_module_sub_module_endpoints,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = AppModuleSubModuleEndpointRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'App module sub module endpoint not found', 404);
            }
            return api_response($result, 'App module sub module endpoint restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring app module sub module endpoint', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:app_module_sub_module_endpoints,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = AppModuleSubModuleEndpointDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'App module sub module endpoint not found', 404);
            }
            return api_response($result, 'App module sub module endpoint deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting app module sub module endpoint', 500);
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
            'data.*.app_module_sub_module_id' => 'nullable|integer|exists:app_module_sub_modules,id',
            'data.*.uri' => 'nullable|string|max:200',
            'data.*.action_key' => 'nullable|string|max:100',
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
            $result = AppModuleSubModuleEndpointImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'App module sub module endpoints imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing app module sub module endpoints', 500);
        }
    }

    /**
     * Get analytics data.
     */
    public function analytics(Request $request)
    {
        try {
            $data = AppModuleSubModuleEndpointAnalyticsAction::execute($this->model, $this->table_name);
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

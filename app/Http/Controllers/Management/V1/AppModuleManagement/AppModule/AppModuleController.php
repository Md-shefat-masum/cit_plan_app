<?php

namespace App\Http\Controllers\Management\V1\AppModuleManagement\AppModule;

use App\Http\Controllers\Controller;
use App\Http\Actions\AppModuleManagement\AppModule\AppModuleIndexAction;
use App\Http\Actions\AppModuleManagement\AppModule\AppModuleStoreAction;
use App\Http\Actions\AppModuleManagement\AppModule\AppModuleShowAction;
use App\Http\Actions\AppModuleManagement\AppModule\AppModuleUpdateAction;
use App\Http\Actions\AppModuleManagement\AppModule\AppModuleSoftDeleteAction;
use App\Http\Actions\AppModuleManagement\AppModule\AppModuleRestoreAction;
use App\Http\Actions\AppModuleManagement\AppModule\AppModuleDestroyAction;
use App\Http\Actions\AppModuleManagement\AppModule\AppModuleImportAction;
use App\Http\Actions\AppModuleManagement\AppModule\AppModuleAnalyticsAction;
use App\Models\AppModuleManagement\AppModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppModuleController extends Controller
{
    protected $model = AppModule::class;
    protected $table_name = 'app_modules';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = AppModuleIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'App modules retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving app modules', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
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

            $result = AppModuleStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'App module created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating app module', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = AppModuleShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'App module not found', 404);
            }
            return api_response($data, 'App module retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving app module', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:app_modules,id',
            'title' => 'required|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $data = $request->all();
            $result = AppModuleUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'App module not found', 404);
            }
            return api_response($result, 'App module updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating app module', 500);
        }
    }

    /**
     * Soft delete the specified resource.
     */
    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:app_modules,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = AppModuleSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'App module not found', 404);
            }
            return api_response($result, 'App module soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting app module', 500);
        }
    }

    /**
     * Restore the specified resource.
     */
    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:app_modules,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = AppModuleRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'App module not found', 404);
            }
            return api_response($result, 'App module restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring app module', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:app_modules,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = AppModuleDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'App module not found', 404);
            }
            return api_response($result, 'App module deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting app module', 500);
        }
    }

    /**
     * Import resources.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.title' => 'required|string|max:100',
            'data.*.status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $data = $request->input('data');
            $result = AppModuleImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'App modules imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing app modules', 500);
        }
    }

    /**
     * Get analytics data.
     */
    public function analytics(Request $request)
    {
        try {
            $data = AppModuleAnalyticsAction::execute($this->model, $this->table_name);
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

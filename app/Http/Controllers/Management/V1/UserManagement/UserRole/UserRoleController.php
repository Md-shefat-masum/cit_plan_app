<?php

namespace App\Http\Controllers\Management\V1\UserManagement\UserRole;

use App\Http\Controllers\Controller;
use App\Http\Actions\UserManagement\UserRole\UserRoleIndexAction;
use App\Http\Actions\UserManagement\UserRole\UserRoleStoreAction;
use App\Http\Actions\UserManagement\UserRole\UserRoleShowAction;
use App\Http\Actions\UserManagement\UserRole\UserRoleUpdateAction;
use App\Http\Actions\UserManagement\UserRole\UserRoleSoftDeleteAction;
use App\Http\Actions\UserManagement\UserRole\UserRoleRestoreAction;
use App\Http\Actions\UserManagement\UserRole\UserRoleDestroyAction;
use App\Http\Actions\UserManagement\UserRole\UserRoleImportAction;
use App\Http\Actions\UserManagement\UserRole\UserRoleAnalyticsAction;
use App\Models\UserManagement\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserRoleController extends Controller
{
    protected $model = UserRole::class;
    protected $table_name = 'user_roles';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = UserRoleIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'User roles retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving user roles', 500);
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

            $result = UserRoleStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'User role created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating user role', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = UserRoleShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'User role not found', 404);
            }
            return api_response($data, 'User role retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving user role', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:user_roles,id',
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
            $result = UserRoleUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'User role not found', 404);
            }
            return api_response($result, 'User role updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating user role', 500);
        }
    }

    /**
     * Soft delete the specified resource.
     */
    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:user_roles,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = UserRoleSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'User role not found', 404);
            }
            return api_response($result, 'User role soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting user role', 500);
        }
    }

    /**
     * Restore the specified resource.
     */
    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:user_roles,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = UserRoleRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'User role not found', 404);
            }
            return api_response($result, 'User role restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring user role', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:user_roles,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = UserRoleDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'User role not found', 404);
            }
            return api_response($result, 'User role deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting user role', 500);
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
            $result = UserRoleImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'User roles imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing user roles', 500);
        }
    }

    /**
     * Get analytics data.
     */
    public function analytics(Request $request)
    {
        try {
            $data = UserRoleAnalyticsAction::execute($this->model, $this->table_name);
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

<?php

namespace App\Http\Controllers\Management\V1\UserManagement\User;

use App\Http\Controllers\Controller;
use App\Http\Actions\UserManagement\User\UserIndexAction;
use App\Http\Actions\UserManagement\User\UserStoreAction;
use App\Http\Actions\UserManagement\User\UserShowAction;
use App\Http\Actions\UserManagement\User\UserUpdateAction;
use App\Http\Actions\UserManagement\User\UserSoftDeleteAction;
use App\Http\Actions\UserManagement\User\UserRestoreAction;
use App\Http\Actions\UserManagement\User\UserDestroyAction;
use App\Http\Actions\UserManagement\User\UserImportAction;
use App\Http\Actions\UserManagement\User\UserAnalyticsAction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $model = User::class;
    protected $table_name = 'users';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = UserIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'Users retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving users', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'username' => 'required|string|max:20|unique:users,username',
            'user_role_id' => 'required|integer|exists:user_roles,id',
            'email' => 'required|email|max:50|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $data = $request->all();
            $data['slug'] = $data['username']; // Set slug from username
            $data['creator'] = auth('api')->id() ?? 0;
            $data['status'] = $request->input('status', 1);
            
            // Remove password_confirmation as it's not needed in database
            unset($data['password_confirmation']);

            $result = UserStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'User created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating user', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = UserShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'User not found', 404);
            }
            return api_response($data, 'User retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving user', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:50',
            'username' => 'required|string|max:20|unique:users,username,' . $request->input('id'),
            'user_role_id' => 'required|integer|exists:user_roles,id',
            'email' => 'required|email|max:50|unique:users,email,' . $request->input('id'),
            'status' => 'nullable|integer|in:0,1',
        ];

        // Password is required only if provided in request
        if ($request->has('password')) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $data = $request->all();
            $data['slug'] = $data['username']; // Update slug from username
            $data['creator'] = auth('api')->id() ?? 0;
            
            // Remove password_confirmation as it's not needed in database
            unset($data['password_confirmation']);

            $result = UserUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'User not found', 404);
            }
            return api_response($result, 'User updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating user', 500);
        }
    }

    /**
     * Soft delete the specified resource (set status = 0).
     */
    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = UserSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'User not found', 404);
            }
            return api_response($result, 'User soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting user', 500);
        }
    }

    /**
     * Restore the specified resource.
     */
    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = UserRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'User not found', 404);
            }
            return api_response($result, 'User restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring user', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Note: According to instructions, users should not be destroyed, only soft deleted.
     * This method is kept for consistency but should be used with caution.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $result = UserDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'User not found', 404);
            }
            return api_response($result, 'User deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting user', 500);
        }
    }

    /**
     * Import resources.
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.name' => 'required|string|max:50',
            'data.*.username' => 'required|string|max:20',
            'data.*.user_role_id' => 'required|integer|exists:user_roles,id',
            'data.*.email' => 'required|email|max:50',
            'data.*.password' => 'required|string|min:6',
            'data.*.status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response([
                'errors' => $validator->errors()
            ], 'Validation failed', 422);
        }

        try {
            $data = $request->input('data');
            $result = UserImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Users imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing users', 500);
        }
    }

    /**
     * Get analytics data.
     */
    public function analytics(Request $request)
    {
        try {
            $data = UserAnalyticsAction::execute($this->model, $this->table_name);
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

<?php

namespace App\Http\Controllers\Management\V1\TaskManagement\Department;

use App\Http\Controllers\Controller;
use App\Http\Actions\TaskManagement\Department\DepartmentIndexAction;
use App\Http\Actions\TaskManagement\Department\DepartmentStoreAction;
use App\Http\Actions\TaskManagement\Department\DepartmentShowAction;
use App\Http\Actions\TaskManagement\Department\DepartmentUpdateAction;
use App\Http\Actions\TaskManagement\Department\DepartmentSoftDeleteAction;
use App\Http\Actions\TaskManagement\Department\DepartmentRestoreAction;
use App\Http\Actions\TaskManagement\Department\DepartmentDestroyAction;
use App\Http\Actions\TaskManagement\Department\DepartmentImportAction;
use App\Http\Actions\TaskManagement\Department\DepartmentAnalyticsAction;
use App\Models\TaskManagement\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    protected $model = Department::class;
    protected $table_name = 'departments';

    public function index(Request $request)
    {
        try {
            $data = DepartmentIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'Departments retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving departments', 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->all();
            $data['slug'] = uniqid();
            $data['creator'] = auth('api')->id() ?? 0;
            $data['status'] = $request->input('status', 1);

            $result = DepartmentStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Department created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating department', 500);
        }
    }

    public function show($id)
    {
        try {
            $data = DepartmentShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'Department not found', 404);
            }
            return api_response($data, 'Department retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving department', 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:departments,id',
            'title' => 'required|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->all();
            $result = DepartmentUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'Department not found', 404);
            }
            return api_response($result, 'Department updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating department', 500);
        }
    }

    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = DepartmentSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Department not found', 404);
            }
            return api_response($result, 'Department soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting department', 500);
        }
    }

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = DepartmentRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Department not found', 404);
            }
            return api_response($result, 'Department restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring department', 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = DepartmentDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Department not found', 404);
            }
            return api_response($result, 'Department deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting department', 500);
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.title' => 'required|string|max:100',
            'data.*.status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->input('data');
            $result = DepartmentImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Departments imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing departments', 500);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $data = DepartmentAnalyticsAction::execute($this->model, $this->table_name);
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

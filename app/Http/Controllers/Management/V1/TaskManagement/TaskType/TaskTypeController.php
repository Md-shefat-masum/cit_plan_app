<?php

namespace App\Http\Controllers\Management\V1\TaskManagement\TaskType;

use App\Http\Controllers\Controller;
use App\Http\Actions\TaskManagement\TaskType\TaskTypeIndexAction;
use App\Http\Actions\TaskManagement\TaskType\TaskTypeStoreAction;
use App\Http\Actions\TaskManagement\TaskType\TaskTypeShowAction;
use App\Http\Actions\TaskManagement\TaskType\TaskTypeUpdateAction;
use App\Http\Actions\TaskManagement\TaskType\TaskTypeSoftDeleteAction;
use App\Http\Actions\TaskManagement\TaskType\TaskTypeRestoreAction;
use App\Http\Actions\TaskManagement\TaskType\TaskTypeDestroyAction;
use App\Http\Actions\TaskManagement\TaskType\TaskTypeImportAction;
use App\Http\Actions\TaskManagement\TaskType\TaskTypeAnalyticsAction;
use App\Models\TaskManagement\TaskType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskTypeController extends Controller
{
    protected $model = TaskType::class;
    protected $table_name = 'task_types';

    public function index(Request $request)
    {
        try {
            $data = TaskTypeIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'Task types retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving task types', 500);
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

            $result = TaskTypeStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Task type created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating task type', 500);
        }
    }

    public function show($id)
    {
        try {
            $data = TaskTypeShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'Task type not found', 404);
            }
            return api_response($data, 'Task type retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving task type', 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_types,id',
            'title' => 'required|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->all();
            $result = TaskTypeUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'Task type not found', 404);
            }
            return api_response($result, 'Task type updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating task type', 500);
        }
    }

    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_types,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskTypeSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task type not found', 404);
            }
            return api_response($result, 'Task type soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting task type', 500);
        }
    }

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_types,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskTypeRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task type not found', 404);
            }
            return api_response($result, 'Task type restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring task type', 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_types,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskTypeDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task type not found', 404);
            }
            return api_response($result, 'Task type deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting task type', 500);
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
            $result = TaskTypeImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Task types imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing task types', 500);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $data = TaskTypeAnalyticsAction::execute($this->model, $this->table_name);
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

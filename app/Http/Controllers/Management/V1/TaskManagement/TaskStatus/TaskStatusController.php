<?php

namespace App\Http\Controllers\Management\V1\TaskManagement\TaskStatus;

use App\Http\Controllers\Controller;
use App\Http\Actions\TaskManagement\TaskStatus\TaskStatusIndexAction;
use App\Http\Actions\TaskManagement\TaskStatus\TaskStatusStoreAction;
use App\Http\Actions\TaskManagement\TaskStatus\TaskStatusShowAction;
use App\Http\Actions\TaskManagement\TaskStatus\TaskStatusUpdateAction;
use App\Http\Actions\TaskManagement\TaskStatus\TaskStatusSoftDeleteAction;
use App\Http\Actions\TaskManagement\TaskStatus\TaskStatusRestoreAction;
use App\Http\Actions\TaskManagement\TaskStatus\TaskStatusDestroyAction;
use App\Http\Actions\TaskManagement\TaskStatus\TaskStatusImportAction;
use App\Http\Actions\TaskManagement\TaskStatus\TaskStatusAnalyticsAction;
use App\Models\TaskManagement\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskStatusController extends Controller
{
    protected $model = TaskStatus::class;
    protected $table_name = 'task_statuses';

    public function index(Request $request)
    {
        try {
            $data = TaskStatusIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'Task statuses retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving task statuses', 500);
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

            $result = TaskStatusStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Task status created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating task status', 500);
        }
    }

    public function show($id)
    {
        try {
            $data = TaskStatusShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'Task status not found', 404);
            }
            return api_response($data, 'Task status retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving task status', 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_statuses,id',
            'title' => 'required|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->all();
            $result = TaskStatusUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'Task status not found', 404);
            }
            return api_response($result, 'Task status updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating task status', 500);
        }
    }

    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_statuses,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskStatusSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task status not found', 404);
            }
            return api_response($result, 'Task status soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting task status', 500);
        }
    }

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_statuses,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskStatusRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task status not found', 404);
            }
            return api_response($result, 'Task status restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring task status', 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_statuses,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskStatusDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task status not found', 404);
            }
            return api_response($result, 'Task status deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting task status', 500);
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
            $result = TaskStatusImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Task statuses imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing task statuses', 500);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $data = TaskStatusAnalyticsAction::execute($this->model, $this->table_name);
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

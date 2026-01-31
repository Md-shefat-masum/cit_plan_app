<?php

namespace App\Http\Controllers\Management\V1\TaskManagement\TaskCompletorCategory;

use App\Http\Controllers\Controller;
use App\Http\Actions\TaskManagement\TaskCompletorCategory\TaskCompletorCategoryIndexAction;
use App\Http\Actions\TaskManagement\TaskCompletorCategory\TaskCompletorCategoryStoreAction;
use App\Http\Actions\TaskManagement\TaskCompletorCategory\TaskCompletorCategoryShowAction;
use App\Http\Actions\TaskManagement\TaskCompletorCategory\TaskCompletorCategoryUpdateAction;
use App\Http\Actions\TaskManagement\TaskCompletorCategory\TaskCompletorCategorySoftDeleteAction;
use App\Http\Actions\TaskManagement\TaskCompletorCategory\TaskCompletorCategoryRestoreAction;
use App\Http\Actions\TaskManagement\TaskCompletorCategory\TaskCompletorCategoryDestroyAction;
use App\Http\Actions\TaskManagement\TaskCompletorCategory\TaskCompletorCategoryImportAction;
use App\Http\Actions\TaskManagement\TaskCompletorCategory\TaskCompletorCategoryAnalyticsAction;
use App\Models\TaskManagement\TaskCompletorCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskCompletorCategoryController extends Controller
{
    protected $model = TaskCompletorCategory::class;
    protected $table_name = 'task_completor_categories';

    public function index(Request $request)
    {
        try {
            $data = TaskCompletorCategoryIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'Task completor categories retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving task completor categories', 500);
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

            $result = TaskCompletorCategoryStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Task completor category created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating task completor category', 500);
        }
    }

    public function show($id)
    {
        try {
            $data = TaskCompletorCategoryShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'Task completor category not found', 404);
            }
            return api_response($data, 'Task completor category retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving task completor category', 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_completor_categories,id',
            'title' => 'required|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->except(['id']);
            $result = TaskCompletorCategoryUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'Task completor category not found', 404);
            }
            return api_response($result, 'Task completor category updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating task completor category', 500);
        }
    }

    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_completor_categories,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskCompletorCategorySoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task completor category not found', 404);
            }
            return api_response($result, 'Task completor category soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting task completor category', 500);
        }
    }

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_completor_categories,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskCompletorCategoryRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task completor category not found', 404);
            }
            return api_response($result, 'Task completor category restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring task completor category', 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_completor_categories,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskCompletorCategoryDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task completor category not found', 404);
            }
            return api_response($result, 'Task completor category deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting task completor category', 500);
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
            $result = TaskCompletorCategoryImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Task completor categories imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing task completor categories', 500);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $data = TaskCompletorCategoryAnalyticsAction::execute($this->model, $this->table_name);
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

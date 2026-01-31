<?php

namespace App\Http\Controllers\Management\V1\TaskManagement\TaskCompletorSubCategory;

use App\Http\Controllers\Controller;
use App\Http\Actions\TaskManagement\TaskCompletorSubCategory\TaskCompletorSubCategoryIndexAction;
use App\Http\Actions\TaskManagement\TaskCompletorSubCategory\TaskCompletorSubCategoryStoreAction;
use App\Http\Actions\TaskManagement\TaskCompletorSubCategory\TaskCompletorSubCategoryShowAction;
use App\Http\Actions\TaskManagement\TaskCompletorSubCategory\TaskCompletorSubCategoryUpdateAction;
use App\Http\Actions\TaskManagement\TaskCompletorSubCategory\TaskCompletorSubCategorySoftDeleteAction;
use App\Http\Actions\TaskManagement\TaskCompletorSubCategory\TaskCompletorSubCategoryRestoreAction;
use App\Http\Actions\TaskManagement\TaskCompletorSubCategory\TaskCompletorSubCategoryDestroyAction;
use App\Http\Actions\TaskManagement\TaskCompletorSubCategory\TaskCompletorSubCategoryImportAction;
use App\Http\Actions\TaskManagement\TaskCompletorSubCategory\TaskCompletorSubCategoryAnalyticsAction;
use App\Models\TaskManagement\TaskCompletorSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskCompletorSubCategoryController extends Controller
{
    protected $model = TaskCompletorSubCategory::class;
    protected $table_name = 'task_completor_sub_categories';

    public function index(Request $request)
    {
        try {
            $data = TaskCompletorSubCategoryIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'Task completor sub categories retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving task completor sub categories', 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_completor_category_id' => 'nullable|integer|exists:task_completor_categories,id',
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

            $result = TaskCompletorSubCategoryStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Task completor sub category created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating task completor sub category', 500);
        }
    }

    public function show($id)
    {
        try {
            $data = TaskCompletorSubCategoryShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'Task completor sub category not found', 404);
            }
            return api_response($data, 'Task completor sub category retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving task completor sub category', 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_completor_sub_categories,id',
            'task_completor_category_id' => 'nullable|integer|exists:task_completor_categories,id',
            'title' => 'required|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->except(['id']);
            $result = TaskCompletorSubCategoryUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'Task completor sub category not found', 404);
            }
            return api_response($result, 'Task completor sub category updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating task completor sub category', 500);
        }
    }

    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_completor_sub_categories,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskCompletorSubCategorySoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task completor sub category not found', 404);
            }
            return api_response($result, 'Task completor sub category soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting task completor sub category', 500);
        }
    }

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_completor_sub_categories,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskCompletorSubCategoryRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task completor sub category not found', 404);
            }
            return api_response($result, 'Task completor sub category restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring task completor sub category', 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_completor_sub_categories,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskCompletorSubCategoryDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task completor sub category not found', 404);
            }
            return api_response($result, 'Task completor sub category deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting task completor sub category', 500);
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.task_completor_category_id' => 'nullable|integer|exists:task_completor_categories,id',
            'data.*.title' => 'required|string|max:100',
            'data.*.status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->input('data');
            $result = TaskCompletorSubCategoryImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Task completor sub categories imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing task completor sub categories', 500);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $data = TaskCompletorSubCategoryAnalyticsAction::execute($this->model, $this->table_name);
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

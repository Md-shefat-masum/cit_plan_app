<?php

namespace App\Http\Controllers\Management\V1\TaskManagement\TaskSubPlan;

use App\Http\Controllers\Controller;
use App\Http\Actions\TaskManagement\TaskSubPlan\TaskSubPlanIndexAction;
use App\Http\Actions\TaskManagement\TaskSubPlan\TaskSubPlanStoreAction;
use App\Http\Actions\TaskManagement\TaskSubPlan\TaskSubPlanShowAction;
use App\Http\Actions\TaskManagement\TaskSubPlan\TaskSubPlanUpdateAction;
use App\Http\Actions\TaskManagement\TaskSubPlan\TaskSubPlanSoftDeleteAction;
use App\Http\Actions\TaskManagement\TaskSubPlan\TaskSubPlanRestoreAction;
use App\Http\Actions\TaskManagement\TaskSubPlan\TaskSubPlanDestroyAction;
use App\Http\Actions\TaskManagement\TaskSubPlan\TaskSubPlanImportAction;
use App\Http\Actions\TaskManagement\TaskSubPlan\TaskSubPlanAnalyticsAction;
use App\Models\TaskManagement\TaskSubPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskSubPlanController extends Controller
{
    protected $model = TaskSubPlan::class;
    protected $table_name = 'task_sub_plans';

    public function index(Request $request)
    {
        try {
            $data = TaskSubPlanIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'Task sub plans retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving task sub plans', 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_plan_id' => 'nullable|integer|exists:task_plans,id',
            'description' => 'nullable|string|max:65535',
            'time_duration_id' => 'nullable|integer|exists:time_durations,id',
            'time_sub_duration_id' => 'nullable|integer|exists:time_sub_durations,id',
            'task_completor_category_id' => 'nullable|integer|exists:task_completor_categories,id',
            'task_completor_sub_category_id' => 'nullable|integer|exists:task_completor_sub_categories,id',
            'umbrella_department_id' => 'nullable|integer|exists:departments,id',
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

            $result = TaskSubPlanStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Task sub plan created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating task sub plan', 500);
        }
    }

    public function show($id)
    {
        try {
            $data = TaskSubPlanShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'Task sub plan not found', 404);
            }
            return api_response($data, 'Task sub plan retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving task sub plan', 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_sub_plans,id',
            'task_plan_id' => 'nullable|integer|exists:task_plans,id',
            'description' => 'nullable|string|max:65535',
            'time_duration_id' => 'nullable|integer|exists:time_durations,id',
            'time_sub_duration_id' => 'nullable|integer|exists:time_sub_durations,id',
            'task_completor_category_id' => 'nullable|integer|exists:task_completor_categories,id',
            'task_completor_sub_category_id' => 'nullable|integer|exists:task_completor_sub_categories,id',
            'umbrella_department_id' => 'nullable|integer|exists:departments,id',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->except(['id']);
            $result = TaskSubPlanUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'Task sub plan not found', 404);
            }
            return api_response($result, 'Task sub plan updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating task sub plan', 500);
        }
    }

    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_sub_plans,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskSubPlanSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task sub plan not found', 404);
            }
            return api_response($result, 'Task sub plan soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting task sub plan', 500);
        }
    }

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_sub_plans,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskSubPlanRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task sub plan not found', 404);
            }
            return api_response($result, 'Task sub plan restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring task sub plan', 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_sub_plans,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskSubPlanDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task sub plan not found', 404);
            }
            return api_response($result, 'Task sub plan deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting task sub plan', 500);
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.task_plan_id' => 'nullable|integer|exists:task_plans,id',
            'data.*.description' => 'nullable|string|max:65535',
            'data.*.time_duration_id' => 'nullable|integer|exists:time_durations,id',
            'data.*.time_sub_duration_id' => 'nullable|integer|exists:time_sub_durations,id',
            'data.*.task_completor_category_id' => 'nullable|integer|exists:task_completor_categories,id',
            'data.*.task_completor_sub_category_id' => 'nullable|integer|exists:task_completor_sub_categories,id',
            'data.*.umbrella_department_id' => 'nullable|integer|exists:departments,id',
            'data.*.status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->input('data');
            $result = TaskSubPlanImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Task sub plans imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing task sub plans', 500);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $data = TaskSubPlanAnalyticsAction::execute($this->model, $this->table_name);
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

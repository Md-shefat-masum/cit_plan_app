<?php

namespace App\Http\Controllers\Management\V1\TaskManagement\TaskPlan;

use App\Http\Controllers\Controller;
use App\Http\Actions\TaskManagement\TaskPlan\TaskPlanIndexAction;
use App\Http\Actions\TaskManagement\TaskPlan\TaskPlanStoreAction;
use App\Http\Actions\TaskManagement\TaskPlan\TaskPlanShowAction;
use App\Http\Actions\TaskManagement\TaskPlan\TaskPlanUpdateAction;
use App\Http\Actions\TaskManagement\TaskPlan\TaskPlanSoftDeleteAction;
use App\Http\Actions\TaskManagement\TaskPlan\TaskPlanRestoreAction;
use App\Http\Actions\TaskManagement\TaskPlan\TaskPlanDestroyAction;
use App\Http\Actions\TaskManagement\TaskPlan\TaskPlanImportAction;
use App\Http\Actions\TaskManagement\TaskPlan\TaskPlanAnalyticsAction;
use App\Models\TaskManagement\TaskPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskPlanController extends Controller
{
    protected $model = TaskPlan::class;
    protected $table_name = 'task_plans';

    public function index(Request $request)
    {
        try {
            $data = TaskPlanIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'Task plans retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving task plans', 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'nullable|integer|exists:departments,id',
            'department_section_id' => 'nullable|integer|exists:department_sections,id',
            'department_sub_section_id' => 'nullable|integer|exists:department_sub_sections,id',
            'dofa_id' => 'nullable|integer|exists:dofas,id',
            'description' => 'nullable|string|max:65535',
            'qty' => 'nullable|integer|min:0',
            'task_type_id' => 'nullable|integer|exists:task_types,id',
            'task_status_id' => 'nullable|integer|exists:task_statuses,id',
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

            $result = TaskPlanStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Task plan created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating task plan', 500);
        }
    }

    public function show($id)
    {
        try {
            $data = TaskPlanShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'Task plan not found', 404);
            }
            return api_response($data, 'Task plan retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving task plan', 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_plans,id',
            'department_id' => 'nullable|integer|exists:departments,id',
            'department_section_id' => 'nullable|integer|exists:department_sections,id',
            'department_sub_section_id' => 'nullable|integer|exists:department_sub_sections,id',
            'dofa_id' => 'nullable|integer|exists:dofas,id',
            'description' => 'nullable|string|max:65535',
            'qty' => 'nullable|integer|min:0',
            'task_type_id' => 'nullable|integer|exists:task_types,id',
            'task_status_id' => 'nullable|integer|exists:task_statuses,id',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->except(['id']);
            $result = TaskPlanUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'Task plan not found', 404);
            }
            return api_response($result, 'Task plan updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating task plan', 500);
        }
    }

    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_plans,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskPlanSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task plan not found', 404);
            }
            return api_response($result, 'Task plan soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting task plan', 500);
        }
    }

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_plans,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskPlanRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task plan not found', 404);
            }
            return api_response($result, 'Task plan restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring task plan', 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:task_plans,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TaskPlanDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Task plan not found', 404);
            }
            return api_response($result, 'Task plan deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting task plan', 500);
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.department_id' => 'nullable|integer|exists:departments,id',
            'data.*.department_section_id' => 'nullable|integer|exists:department_sections,id',
            'data.*.department_sub_section_id' => 'nullable|integer|exists:department_sub_sections,id',
            'data.*.dofa_id' => 'nullable|integer|exists:dofas,id',
            'data.*.description' => 'nullable|string|max:65535',
            'data.*.qty' => 'nullable|integer|min:0',
            'data.*.task_type_id' => 'nullable|integer|exists:task_types,id',
            'data.*.task_status_id' => 'nullable|integer|exists:task_statuses,id',
            'data.*.status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->input('data');
            $result = TaskPlanImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Task plans imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing task plans', 500);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $data = TaskPlanAnalyticsAction::execute($this->model, $this->table_name);
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

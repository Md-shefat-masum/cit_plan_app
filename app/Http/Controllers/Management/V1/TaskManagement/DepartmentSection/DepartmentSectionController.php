<?php

namespace App\Http\Controllers\Management\V1\TaskManagement\DepartmentSection;

use App\Http\Controllers\Controller;
use App\Http\Actions\TaskManagement\DepartmentSection\DepartmentSectionIndexAction;
use App\Http\Actions\TaskManagement\DepartmentSection\DepartmentSectionStoreAction;
use App\Http\Actions\TaskManagement\DepartmentSection\DepartmentSectionShowAction;
use App\Http\Actions\TaskManagement\DepartmentSection\DepartmentSectionUpdateAction;
use App\Http\Actions\TaskManagement\DepartmentSection\DepartmentSectionSoftDeleteAction;
use App\Http\Actions\TaskManagement\DepartmentSection\DepartmentSectionRestoreAction;
use App\Http\Actions\TaskManagement\DepartmentSection\DepartmentSectionDestroyAction;
use App\Http\Actions\TaskManagement\DepartmentSection\DepartmentSectionImportAction;
use App\Http\Actions\TaskManagement\DepartmentSection\DepartmentSectionAnalyticsAction;
use App\Models\TaskManagement\DepartmentSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentSectionController extends Controller
{
    protected $model = DepartmentSection::class;
    protected $table_name = 'department_sections';

    public function index(Request $request)
    {
        try {
            $data = DepartmentSectionIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'Department sections retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving department sections', 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'nullable|integer|exists:departments,id',
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

            $result = DepartmentSectionStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Department section created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating department section', 500);
        }
    }

    public function show($id)
    {
        try {
            $data = DepartmentSectionShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'Department section not found', 404);
            }
            return api_response($data, 'Department section retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving department section', 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:department_sections,id',
            'department_id' => 'nullable|integer|exists:departments,id',
            'title' => 'required|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->all();
            $result = DepartmentSectionUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'Department section not found', 404);
            }
            return api_response($result, 'Department section updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating department section', 500);
        }
    }

    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:department_sections,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = DepartmentSectionSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Department section not found', 404);
            }
            return api_response($result, 'Department section soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting department section', 500);
        }
    }

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:department_sections,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = DepartmentSectionRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Department section not found', 404);
            }
            return api_response($result, 'Department section restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring department section', 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:department_sections,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = DepartmentSectionDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Department section not found', 404);
            }
            return api_response($result, 'Department section deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting department section', 500);
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.department_id' => 'nullable|integer|exists:departments,id',
            'data.*.title' => 'required|string|max:100',
            'data.*.status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->input('data');
            $result = DepartmentSectionImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Department sections imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing department sections', 500);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $data = DepartmentSectionAnalyticsAction::execute($this->model, $this->table_name);
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

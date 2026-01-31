<?php

namespace App\Http\Controllers\Management\V1\TaskManagement\DepartmentSubSection;

use App\Http\Controllers\Controller;
use App\Http\Actions\TaskManagement\DepartmentSubSection\DepartmentSubSectionIndexAction;
use App\Http\Actions\TaskManagement\DepartmentSubSection\DepartmentSubSectionStoreAction;
use App\Http\Actions\TaskManagement\DepartmentSubSection\DepartmentSubSectionShowAction;
use App\Http\Actions\TaskManagement\DepartmentSubSection\DepartmentSubSectionUpdateAction;
use App\Http\Actions\TaskManagement\DepartmentSubSection\DepartmentSubSectionSoftDeleteAction;
use App\Http\Actions\TaskManagement\DepartmentSubSection\DepartmentSubSectionRestoreAction;
use App\Http\Actions\TaskManagement\DepartmentSubSection\DepartmentSubSectionDestroyAction;
use App\Http\Actions\TaskManagement\DepartmentSubSection\DepartmentSubSectionImportAction;
use App\Http\Actions\TaskManagement\DepartmentSubSection\DepartmentSubSectionAnalyticsAction;
use App\Models\TaskManagement\DepartmentSubSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentSubSectionController extends Controller
{
    protected $model = DepartmentSubSection::class;
    protected $table_name = 'department_sub_sections';

    public function index(Request $request)
    {
        try {
            $data = DepartmentSubSectionIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'Department sub sections retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving department sub sections', 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'nullable|integer|exists:departments,id',
            'department_section_id' => 'nullable|integer|exists:department_sections,id',
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

            $result = DepartmentSubSectionStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Department sub section created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating department sub section', 500);
        }
    }

    public function show($id)
    {
        try {
            $data = DepartmentSubSectionShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'Department sub section not found', 404);
            }
            return api_response($data, 'Department sub section retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving department sub section', 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:department_sub_sections,id',
            'department_id' => 'nullable|integer|exists:departments,id',
            'department_section_id' => 'nullable|integer|exists:department_sections,id',
            'title' => 'required|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->all();
            $result = DepartmentSubSectionUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'Department sub section not found', 404);
            }
            return api_response($result, 'Department sub section updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating department sub section', 500);
        }
    }

    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:department_sub_sections,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = DepartmentSubSectionSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Department sub section not found', 404);
            }
            return api_response($result, 'Department sub section soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting department sub section', 500);
        }
    }

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:department_sub_sections,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = DepartmentSubSectionRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Department sub section not found', 404);
            }
            return api_response($result, 'Department sub section restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring department sub section', 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:department_sub_sections,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = DepartmentSubSectionDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Department sub section not found', 404);
            }
            return api_response($result, 'Department sub section deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting department sub section', 500);
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.department_id' => 'nullable|integer|exists:departments,id',
            'data.*.department_section_id' => 'nullable|integer|exists:department_sections,id',
            'data.*.title' => 'required|string|max:100',
            'data.*.status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->input('data');
            $result = DepartmentSubSectionImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Department sub sections imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing department sub sections', 500);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $data = DepartmentSubSectionAnalyticsAction::execute($this->model, $this->table_name);
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

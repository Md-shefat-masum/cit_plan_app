<?php

namespace App\Http\Controllers\Management\V1\TaskManagement\TimeSubDuration;

use App\Http\Controllers\Controller;
use App\Http\Actions\TaskManagement\TimeSubDuration\TimeSubDurationIndexAction;
use App\Http\Actions\TaskManagement\TimeSubDuration\TimeSubDurationStoreAction;
use App\Http\Actions\TaskManagement\TimeSubDuration\TimeSubDurationShowAction;
use App\Http\Actions\TaskManagement\TimeSubDuration\TimeSubDurationUpdateAction;
use App\Http\Actions\TaskManagement\TimeSubDuration\TimeSubDurationSoftDeleteAction;
use App\Http\Actions\TaskManagement\TimeSubDuration\TimeSubDurationRestoreAction;
use App\Http\Actions\TaskManagement\TimeSubDuration\TimeSubDurationDestroyAction;
use App\Http\Actions\TaskManagement\TimeSubDuration\TimeSubDurationImportAction;
use App\Http\Actions\TaskManagement\TimeSubDuration\TimeSubDurationAnalyticsAction;
use App\Models\TaskManagement\TimeSubDuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimeSubDurationController extends Controller
{
    protected $model = TimeSubDuration::class;
    protected $table_name = 'time_sub_durations';

    public function index(Request $request)
    {
        try {
            $data = TimeSubDurationIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'Time sub durations retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving time sub durations', 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time_duration_id' => 'nullable|integer|exists:time_durations,id',
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

            $result = TimeSubDurationStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Time sub duration created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating time sub duration', 500);
        }
    }

    public function show($id)
    {
        try {
            $data = TimeSubDurationShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'Time sub duration not found', 404);
            }
            return api_response($data, 'Time sub duration retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving time sub duration', 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:time_sub_durations,id',
            'time_duration_id' => 'nullable|integer|exists:time_durations,id',
            'title' => 'required|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->except(['id']);
            $result = TimeSubDurationUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'Time sub duration not found', 404);
            }
            return api_response($result, 'Time sub duration updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating time sub duration', 500);
        }
    }

    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:time_sub_durations,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TimeSubDurationSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Time sub duration not found', 404);
            }
            return api_response($result, 'Time sub duration soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting time sub duration', 500);
        }
    }

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:time_sub_durations,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TimeSubDurationRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Time sub duration not found', 404);
            }
            return api_response($result, 'Time sub duration restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring time sub duration', 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:time_sub_durations,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TimeSubDurationDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Time sub duration not found', 404);
            }
            return api_response($result, 'Time sub duration deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting time sub duration', 500);
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.time_duration_id' => 'nullable|integer|exists:time_durations,id',
            'data.*.title' => 'required|string|max:100',
            'data.*.status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->input('data');
            $result = TimeSubDurationImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Time sub durations imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing time sub durations', 500);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $data = TimeSubDurationAnalyticsAction::execute($this->model, $this->table_name);
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

<?php

namespace App\Http\Controllers\Management\V1\TaskManagement\TimeDuration;

use App\Http\Controllers\Controller;
use App\Http\Actions\TaskManagement\TimeDuration\TimeDurationIndexAction;
use App\Http\Actions\TaskManagement\TimeDuration\TimeDurationStoreAction;
use App\Http\Actions\TaskManagement\TimeDuration\TimeDurationShowAction;
use App\Http\Actions\TaskManagement\TimeDuration\TimeDurationUpdateAction;
use App\Http\Actions\TaskManagement\TimeDuration\TimeDurationSoftDeleteAction;
use App\Http\Actions\TaskManagement\TimeDuration\TimeDurationRestoreAction;
use App\Http\Actions\TaskManagement\TimeDuration\TimeDurationDestroyAction;
use App\Http\Actions\TaskManagement\TimeDuration\TimeDurationImportAction;
use App\Http\Actions\TaskManagement\TimeDuration\TimeDurationAnalyticsAction;
use App\Models\TaskManagement\TimeDuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimeDurationController extends Controller
{
    protected $model = TimeDuration::class;
    protected $table_name = 'time_durations';

    public function index(Request $request)
    {
        try {
            $data = TimeDurationIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'Time durations retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving time durations', 500);
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

            $result = TimeDurationStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Time duration created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating time duration', 500);
        }
    }

    public function show($id)
    {
        try {
            $data = TimeDurationShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'Time duration not found', 404);
            }
            return api_response($data, 'Time duration retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving time duration', 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:time_durations,id',
            'title' => 'required|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->except(['id']);
            $result = TimeDurationUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'Time duration not found', 404);
            }
            return api_response($result, 'Time duration updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating time duration', 500);
        }
    }

    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:time_durations,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TimeDurationSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Time duration not found', 404);
            }
            return api_response($result, 'Time duration soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting time duration', 500);
        }
    }

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:time_durations,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TimeDurationRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Time duration not found', 404);
            }
            return api_response($result, 'Time duration restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring time duration', 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:time_durations,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = TimeDurationDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Time duration not found', 404);
            }
            return api_response($result, 'Time duration deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting time duration', 500);
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
            $result = TimeDurationImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Time durations imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing time durations', 500);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $data = TimeDurationAnalyticsAction::execute($this->model, $this->table_name);
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

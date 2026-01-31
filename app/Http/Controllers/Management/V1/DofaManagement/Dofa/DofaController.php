<?php

namespace App\Http\Controllers\Management\V1\DofaManagement\Dofa;

use App\Http\Controllers\Controller;
use App\Http\Actions\DofaManagement\Dofa\DofaIndexAction;
use App\Http\Actions\DofaManagement\Dofa\DofaStoreAction;
use App\Http\Actions\DofaManagement\Dofa\DofaShowAction;
use App\Http\Actions\DofaManagement\Dofa\DofaUpdateAction;
use App\Http\Actions\DofaManagement\Dofa\DofaSoftDeleteAction;
use App\Http\Actions\DofaManagement\Dofa\DofaRestoreAction;
use App\Http\Actions\DofaManagement\Dofa\DofaDestroyAction;
use App\Http\Actions\DofaManagement\Dofa\DofaImportAction;
use App\Http\Actions\DofaManagement\Dofa\DofaAnalyticsAction;
use App\Models\DofaManagement\Dofa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DofaController extends Controller
{
    protected $model = Dofa::class;
    protected $table_name = 'dofas';

    public function index(Request $request)
    {
        try {
            $data = DofaIndexAction::execute($this->model, $this->table_name);
            return api_response($data, 'Dofas retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving dofas', 500);
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

            $result = DofaStoreAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Dofa created successfully', 201);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while creating dofa', 500);
        }
    }

    public function show($id)
    {
        try {
            $data = DofaShowAction::execute($this->model, $this->table_name, $id);
            if (!$data) {
                return api_response([], 'Dofa not found', 404);
            }
            return api_response($data, 'Dofa retrieved successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while retrieving dofa', 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:dofas,id',
            'title' => 'required|string|max:100',
            'status' => 'nullable|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $data = $request->all();
            $result = DofaUpdateAction::execute($this->model, $this->table_name, $request->input('id'), $data);
            if (!$result) {
                return api_response([], 'Dofa not found', 404);
            }
            return api_response($result, 'Dofa updated successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while updating dofa', 500);
        }
    }

    public function soft_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:dofas,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = DofaSoftDeleteAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Dofa not found', 404);
            }
            return api_response($result, 'Dofa soft deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while soft deleting dofa', 500);
        }
    }

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:dofas,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = DofaRestoreAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Dofa not found', 404);
            }
            return api_response($result, 'Dofa restored successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while restoring dofa', 500);
        }
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:dofas,id',
        ]);

        if ($validator->fails()) {
            return api_response(['errors' => $validator->errors()], 'Validation failed', 422);
        }

        try {
            $result = DofaDestroyAction::execute($this->model, $this->table_name, $request->input('id'));
            if (!$result) {
                return api_response([], 'Dofa not found', 404);
            }
            return api_response($result, 'Dofa deleted successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while deleting dofa', 500);
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
            $result = DofaImportAction::execute($this->model, $this->table_name, $data);
            return api_response($result, 'Dofas imported successfully', 200);
        } catch (\Exception $e) {
            return api_response([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 'An error occurred while importing dofas', 500);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $data = DofaAnalyticsAction::execute($this->model, $this->table_name);
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

<?php

namespace App\Http\Actions\TaskManagement\TaskType;

use Illuminate\Support\Str;

class TaskTypeIndexAction
{
    public static function execute($model, $table_name)
    {
        $fields = request()->fields ?? ['id', 'title', 'creator', 'slug', 'status', 'created_at', 'updated_at'];
        $fields = array_map(fn ($col) => Str::contains($col, '.') ? $col : $table_name . '.' . $col, $fields);

        $conditions = request()->conditions ?? [];
        $paginate = request()->paginate ?? 10;
        $search = request()->search ?? null;
        $orderByCol = request()->orderByCol ?? 'id';
        $orderByAsc = request()->orderByAsc == 1 ? 'asc' : 'desc';
        $status = request()->status ?? 1;

        if (!Str::contains($orderByCol, '.')) {
            $orderByCol = $table_name . '.' . $orderByCol;
        }

        $query = $model::query()
            ->from($table_name)
            ->where("$table_name.status", $status)
            ->select($fields);

        foreach ($conditions as $key => $value) {
            $col = Str::contains($key, '.') ? $key : $table_name . '.' . $key;
            $query->where($col, $value);
        }

        if ($search) {
            $query->where(function ($q) use ($search, $table_name) {
                $q->where("$table_name.title", 'like', "%{$search}%")
                    ->orWhere("$table_name.slug", 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy($orderByCol, $orderByAsc)
            ->paginate($paginate)
            ->onEachSide(0)
            ->appends(request()->all());

        return [...$data->toArray()];
    }
}

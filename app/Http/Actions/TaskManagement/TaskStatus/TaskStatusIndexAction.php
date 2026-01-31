<?php

namespace App\Http\Actions\TaskManagement\TaskStatus;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TaskStatusIndexAction
{
    /**
     * Execute the query with flexible parameters
     *
     * @param string $model
     * @param string $table_name
     * @return array
     */
    public static function execute($model, $table_name)
    {
        $fields = request()->fields ?? [
            'id',
            'title',
            'creator',
            'slug',
            'status',
            'created_at',
            'updated_at',
        ];

        $fields = array_map(function ($col) use ($table_name) {
            return Str::contains($col, '.') ? $col : $table_name . '.' . $col;
        }, $fields);

        $relations = request()->relations ?? [];
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

        if (count($conditions) > 0) {
            foreach ($conditions as $key => $value) {
                $col = Str::contains($key, '.') ? $key : $table_name . '.' . $key;
                $query->where($col, $value);
            }
        }

        if (count($relations) > 0) {
            $query->with([...$relations]);
        }

        if ($search) {
            $query->where(function ($query) use ($search, $table_name) {
                $query
                    ->where("$table_name.title", 'like', "%{$search}%")
                    ->orWhere("$table_name.slug", 'like', "%{$search}%");
            });
        }

        $data = $query
            ->orderBy($orderByCol, $orderByAsc)
            ->paginate($paginate)
            ->onEachSide(0)
            ->appends(request()->all());

        return [
            ...$data->toArray(),
        ];
    }
}

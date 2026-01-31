<?php

namespace App\Http\Actions\TaskManagement\TimeSubDuration;

use Illuminate\Support\Str;

class TimeSubDurationIndexAction
{
    public static function execute($model, $table_name)
    {
        $time_duration_table = 'time_durations';

        $fields = request()->fields ?? [
            'id',
            'time_duration_id',
            'title',
            'creator',
            'slug',
            'status',
            'created_at',
            'updated_at',
        ];

        $fields = array_map(fn ($col) => Str::contains($col, '.') ? $col : $table_name . '.' . $col, $fields);
        $fields = array_merge($fields, ["$time_duration_table.title as time_duration_title"]);

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
            ->leftJoin($time_duration_table, "$time_duration_table.id", '=', "$table_name.time_duration_id")
            ->where("$table_name.status", $status)
            ->select($fields);

        foreach ($conditions as $key => $value) {
            $col = Str::contains($key, '.') ? $key : $table_name . '.' . $key;
            $query->where($col, $value);
        }

        if ($search) {
            $query->where(function ($q) use ($search, $table_name, $time_duration_table) {
                $q->where("$table_name.title", 'like', "%{$search}%")
                    ->orWhere("$table_name.slug", 'like', "%{$search}%")
                    ->orWhere("$time_duration_table.title", 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy($orderByCol, $orderByAsc)
            ->paginate($paginate)
            ->onEachSide(0)
            ->appends(request()->all());

        return [...$data->toArray()];
    }
}

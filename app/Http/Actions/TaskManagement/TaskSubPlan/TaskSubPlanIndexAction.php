<?php

namespace App\Http\Actions\TaskManagement\TaskSubPlan;

use Illuminate\Support\Str;

class TaskSubPlanIndexAction
{
    public static function execute($model, $table_name)
    {
        $task_plan_table = 'task_plans';
        $time_duration_table = 'time_durations';
        $time_sub_duration_table = 'time_sub_durations';
        $completor_cat_table = 'task_completor_categories';
        $completor_sub_cat_table = 'task_completor_sub_categories';
        $dept_table = 'departments';

        $fields = request()->fields ?? [
            'id',
            'task_plan_id',
            'description',
            'time_duration_id',
            'time_sub_duration_id',
            'task_completor_category_id',
            'task_completor_sub_category_id',
            'umbrella_department_id',
            'creator',
            'slug',
            'status',
            'created_at',
            'updated_at',
        ];

        $fields = array_map(fn ($col) => Str::contains($col, '.') ? $col : $table_name . '.' . $col, $fields);
        $fields = array_merge($fields, [
            "$task_plan_table.si as task_plan_si",
            "$time_duration_table.title as time_duration_title",
            "$time_sub_duration_table.title as time_sub_duration_title",
            "$completor_cat_table.title as task_completor_category_title",
            "$completor_sub_cat_table.title as task_completor_sub_category_title",
            "$dept_table.title as umbrella_department_title",
        ]);

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
            ->leftJoin($task_plan_table, "$task_plan_table.id", '=', "$table_name.task_plan_id")
            ->leftJoin($time_duration_table, "$time_duration_table.id", '=', "$table_name.time_duration_id")
            ->leftJoin($time_sub_duration_table, "$time_sub_duration_table.id", '=', "$table_name.time_sub_duration_id")
            ->leftJoin($completor_cat_table, "$completor_cat_table.id", '=', "$table_name.task_completor_category_id")
            ->leftJoin($completor_sub_cat_table, "$completor_sub_cat_table.id", '=', "$table_name.task_completor_sub_category_id")
            ->leftJoin($dept_table, "$dept_table.id", '=', "$table_name.umbrella_department_id")
            ->where("$table_name.status", $status)
            ->select($fields);

        foreach ($conditions as $key => $value) {
            $col = Str::contains($key, '.') ? $key : $table_name . '.' . $key;
            $query->where($col, $value);
        }

        if ($search) {
            $query->where(function ($q) use ($search, $table_name, $task_plan_table, $time_duration_table, $time_sub_duration_table, $completor_cat_table, $completor_sub_cat_table, $dept_table) {
                $q->where("$table_name.description", 'like', "%{$search}%")
                    ->orWhere("$task_plan_table.si", 'like', "%{$search}%")
                    ->orWhere("$time_duration_table.title", 'like', "%{$search}%")
                    ->orWhere("$time_sub_duration_table.title", 'like', "%{$search}%")
                    ->orWhere("$completor_cat_table.title", 'like', "%{$search}%")
                    ->orWhere("$completor_sub_cat_table.title", 'like', "%{$search}%")
                    ->orWhere("$dept_table.title", 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy($orderByCol, $orderByAsc)
            ->paginate($paginate)
            ->onEachSide(0)
            ->appends(request()->all());

        return [...$data->toArray()];
    }
}

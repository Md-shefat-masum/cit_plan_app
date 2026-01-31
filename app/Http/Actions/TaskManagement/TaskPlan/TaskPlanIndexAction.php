<?php

namespace App\Http\Actions\TaskManagement\TaskPlan;

use Illuminate\Support\Str;

class TaskPlanIndexAction
{
    public static function execute($model, $table_name)
    {
        $dept_table = 'departments';
        $section_table = 'department_sections';
        $sub_section_table = 'department_sub_sections';
        $dofa_table = 'dofas';
        $task_type_table = 'task_types';
        $task_status_table = 'task_statuses';

        $fields = request()->fields ?? [
            'id',
            'si',
            'department_id',
            'department_section_id',
            'department_sub_section_id',
            'dofa_id',
            'description',
            'qty',
            'task_type_id',
            'task_status_id',
            'creator',
            'slug',
            'status',
            'created_at',
            'updated_at',
        ];

        $fields = array_map(fn ($col) => Str::contains($col, '.') ? $col : $table_name . '.' . $col, $fields);
        $fields = array_merge($fields, [
            "$dept_table.title as department_title",
            "$section_table.title as department_section_title",
            "$sub_section_table.title as department_sub_section_title",
            "$dofa_table.title as dofa_title",
            "$task_type_table.title as task_type_title",
            "$task_status_table.title as task_status_title",
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
            ->leftJoin($dept_table, "$dept_table.id", '=', "$table_name.department_id")
            ->leftJoin($section_table, "$section_table.id", '=', "$table_name.department_section_id")
            ->leftJoin($sub_section_table, "$sub_section_table.id", '=', "$table_name.department_sub_section_id")
            ->leftJoin($dofa_table, "$dofa_table.id", '=', "$table_name.dofa_id")
            ->leftJoin($task_type_table, "$task_type_table.id", '=', "$table_name.task_type_id")
            ->leftJoin($task_status_table, "$task_status_table.id", '=', "$table_name.task_status_id")
            ->where("$table_name.status", $status)
            ->select($fields);

        foreach ($conditions as $key => $value) {
            $col = Str::contains($key, '.') ? $key : $table_name . '.' . $key;
            $query->where($col, $value);
        }

        if ($search) {
            $query->where(function ($q) use ($search, $table_name, $dept_table, $section_table, $sub_section_table, $dofa_table, $task_type_table, $task_status_table) {
                $q->where("$table_name.si", 'like', "%{$search}%")
                    ->orWhere("$table_name.description", 'like', "%{$search}%")
                    ->orWhere("$dept_table.title", 'like', "%{$search}%")
                    ->orWhere("$section_table.title", 'like', "%{$search}%")
                    ->orWhere("$sub_section_table.title", 'like', "%{$search}%")
                    ->orWhere("$dofa_table.title", 'like', "%{$search}%")
                    ->orWhere("$task_type_table.title", 'like', "%{$search}%")
                    ->orWhere("$task_status_table.title", 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy($orderByCol, $orderByAsc)
            ->paginate($paginate)
            ->onEachSide(0)
            ->appends(request()->all());

        return [...$data->toArray()];
    }
}

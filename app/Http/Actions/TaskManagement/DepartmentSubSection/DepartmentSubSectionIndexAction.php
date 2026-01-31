<?php

namespace App\Http\Actions\TaskManagement\DepartmentSubSection;

use Illuminate\Support\Str;

class DepartmentSubSectionIndexAction
{
    public static function execute($model, $table_name)
    {
        $department_table = 'departments';
        $section_table = 'department_sections';

        $fields = request()->fields ?? [
            'id',
            'department_id',
            'department_section_id',
            'title',
            'creator',
            'slug',
            'status',
            'created_at',
            'updated_at',
        ];

        $fields = array_map(fn ($col) => Str::contains($col, '.') ? $col : $table_name . '.' . $col, $fields);
        $fields = array_merge($fields, [
            "$department_table.title as department_title",
            "$section_table.title as department_section_title",
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
            ->leftJoin($department_table, "$department_table.id", '=', "$table_name.department_id")
            ->leftJoin($section_table, "$section_table.id", '=', "$table_name.department_section_id")
            ->where("$table_name.status", $status)
            ->select($fields);

        foreach ($conditions as $key => $value) {
            $col = Str::contains($key, '.') ? $key : $table_name . '.' . $key;
            $query->where($col, $value);
        }

        if ($search) {
            $query->where(function ($q) use ($search, $table_name, $department_table, $section_table) {
                $q->where("$table_name.title", 'like', "%{$search}%")
                    ->orWhere("$table_name.slug", 'like', "%{$search}%")
                    ->orWhere("$department_table.title", 'like', "%{$search}%")
                    ->orWhere("$section_table.title", 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy($orderByCol, $orderByAsc)
            ->paginate($paginate)
            ->onEachSide(0)
            ->appends(request()->all());

        return [...$data->toArray()];
    }
}

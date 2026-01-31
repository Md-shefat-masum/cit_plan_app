<?php

namespace App\Http\Actions\TaskManagement\TaskCompletorSubCategory;

use Illuminate\Support\Str;

class TaskCompletorSubCategoryIndexAction
{
    public static function execute($model, $table_name)
    {
        $category_table = 'task_completor_categories';

        $fields = request()->fields ?? [
            'id',
            'task_completor_category_id',
            'title',
            'creator',
            'slug',
            'status',
            'created_at',
            'updated_at',
        ];

        $fields = array_map(fn ($col) => Str::contains($col, '.') ? $col : $table_name . '.' . $col, $fields);
        $fields = array_merge($fields, ["$category_table.title as task_completor_category_title"]);

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
            ->leftJoin($category_table, "$category_table.id", '=', "$table_name.task_completor_category_id")
            ->where("$table_name.status", $status)
            ->select($fields);

        foreach ($conditions as $key => $value) {
            $col = Str::contains($key, '.') ? $key : $table_name . '.' . $key;
            $query->where($col, $value);
        }

        if ($search) {
            $query->where(function ($q) use ($search, $table_name, $category_table) {
                $q->where("$table_name.title", 'like', "%{$search}%")
                    ->orWhere("$table_name.slug", 'like', "%{$search}%")
                    ->orWhere("$category_table.title", 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy($orderByCol, $orderByAsc)
            ->paginate($paginate)
            ->onEachSide(0)
            ->appends(request()->all());

        return [...$data->toArray()];
    }
}

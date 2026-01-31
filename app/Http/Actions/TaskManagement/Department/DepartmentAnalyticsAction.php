<?php

namespace App\Http\Actions\TaskManagement\Department;

class DepartmentAnalyticsAction
{
    public static function execute($model, $table_name)
    {
        return [
            'total' => $model::count(),
            'active' => $model::where('status', 1)->count(),
            'inactive' => $model::where('status', 0)->count(),
        ];
    }
}

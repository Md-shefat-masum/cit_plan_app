<?php

namespace App\Http\Actions\TaskManagement\TaskStatus;

class TaskStatusAnalyticsAction
{
    public static function execute($model, $table_name)
    {
        $total = $model::count();
        $active = $model::where('status', 1)->count();
        $inactive = $model::where('status', 0)->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
        ];
    }
}

<?php

namespace App\Http\Actions\TaskManagement\TaskPlan;

class TaskPlanImportAction
{
    public static function execute($model, $table_name, array $data)
    {
        $imported = [];
        foreach ($data as $item) {
            $item['slug'] = $item['slug'] ?? uniqid();
            $item['creator'] = $item['creator'] ?? (auth('api')->id() ?? 0);
            $item['status'] = $item['status'] ?? 1;
            $imported[] = TaskPlanStoreAction::execute($model, $table_name, $item);
        }
        return $imported;
    }
}

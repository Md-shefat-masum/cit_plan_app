<?php

namespace App\Http\Actions\TaskManagement\TaskType;

class TaskTypeImportAction
{
    public static function execute($model, $table_name, array $data)
    {
        $imported = [];
        foreach ($data as $item) {
            $item['slug'] = uniqid();
            $item['creator'] = auth('api')->id() ?? 0;
            $item['status'] = $item['status'] ?? 1;
            $imported[] = $model::create($item);
        }
        return $imported;
    }
}

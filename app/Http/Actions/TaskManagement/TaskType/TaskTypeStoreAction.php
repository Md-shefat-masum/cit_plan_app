<?php

namespace App\Http\Actions\TaskManagement\TaskType;

class TaskTypeStoreAction
{
    public static function execute($model, $table_name, array $data)
    {
        return $model::create($data);
    }
}

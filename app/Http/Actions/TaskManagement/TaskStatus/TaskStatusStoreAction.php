<?php

namespace App\Http\Actions\TaskManagement\TaskStatus;

class TaskStatusStoreAction
{
    public static function execute($model, $table_name, array $data)
    {
        $taskStatus = $model::create($data);
        return $taskStatus;
    }
}

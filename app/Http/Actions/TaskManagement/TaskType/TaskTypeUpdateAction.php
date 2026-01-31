<?php

namespace App\Http\Actions\TaskManagement\TaskType;

class TaskTypeUpdateAction
{
    public static function execute($model, $table_name, $id, array $data)
    {
        $taskType = $model::find($id);
        if (!$taskType) return null;
        $taskType->update($data);
        return $taskType->fresh();
    }
}

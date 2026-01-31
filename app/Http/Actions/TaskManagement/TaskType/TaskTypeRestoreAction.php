<?php

namespace App\Http\Actions\TaskManagement\TaskType;

class TaskTypeRestoreAction
{
    public static function execute($model, $table_name, $id)
    {
        $taskType = $model::find($id);
        if (!$taskType) return null;
        $taskType->update(['status' => 1]);
        return $taskType->fresh();
    }
}

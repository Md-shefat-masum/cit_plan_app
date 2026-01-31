<?php

namespace App\Http\Actions\TaskManagement\TaskType;

class TaskTypeDestroyAction
{
    public static function execute($model, $table_name, $id)
    {
        $taskType = $model::find($id);
        if (!$taskType) return null;
        $taskType->delete();
        return ['message' => 'Task type deleted successfully'];
    }
}

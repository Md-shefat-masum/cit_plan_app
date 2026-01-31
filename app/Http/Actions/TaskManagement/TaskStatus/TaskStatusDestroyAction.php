<?php

namespace App\Http\Actions\TaskManagement\TaskStatus;

class TaskStatusDestroyAction
{
    public static function execute($model, $table_name, $id)
    {
        $taskStatus = $model::find($id);
        if (!$taskStatus) {
            return null;
        }
        $taskStatus->delete();
        return ['message' => 'Task status deleted successfully'];
    }
}

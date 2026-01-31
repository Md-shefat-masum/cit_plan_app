<?php

namespace App\Http\Actions\TaskManagement\TaskStatus;

class TaskStatusRestoreAction
{
    public static function execute($model, $table_name, $id)
    {
        $taskStatus = $model::find($id);
        if (!$taskStatus) {
            return null;
        }
        $taskStatus->update(['status' => 1]);
        return $taskStatus->fresh();
    }
}

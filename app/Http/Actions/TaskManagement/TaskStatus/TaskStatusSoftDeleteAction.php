<?php

namespace App\Http\Actions\TaskManagement\TaskStatus;

class TaskStatusSoftDeleteAction
{
    public static function execute($model, $table_name, $id)
    {
        $taskStatus = $model::find($id);
        if (!$taskStatus) {
            return null;
        }
        $taskStatus->update(['status' => 0]);
        return $taskStatus->fresh();
    }
}

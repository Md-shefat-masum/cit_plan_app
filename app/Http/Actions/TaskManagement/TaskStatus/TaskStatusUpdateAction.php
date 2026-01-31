<?php

namespace App\Http\Actions\TaskManagement\TaskStatus;

class TaskStatusUpdateAction
{
    public static function execute($model, $table_name, $id, array $data)
    {
        $taskStatus = $model::find($id);
        if (!$taskStatus) {
            return null;
        }
        $taskStatus->update($data);
        return $taskStatus->fresh();
    }
}

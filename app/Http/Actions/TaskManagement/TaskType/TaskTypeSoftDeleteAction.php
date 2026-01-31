<?php

namespace App\Http\Actions\TaskManagement\TaskType;

class TaskTypeSoftDeleteAction
{
    public static function execute($model, $table_name, $id)
    {
        $taskType = $model::find($id);
        if (!$taskType) return null;
        $taskType->update(['status' => 0]);
        return $taskType->fresh();
    }
}

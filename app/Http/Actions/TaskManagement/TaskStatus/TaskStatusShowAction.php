<?php

namespace App\Http\Actions\TaskManagement\TaskStatus;

class TaskStatusShowAction
{
    public static function execute($model, $table_name, $id)
    {
        $taskStatus = $model::find($id);
        return $taskStatus;
    }
}

<?php

namespace App\Http\Actions\TaskManagement\TaskType;

class TaskTypeShowAction
{
    public static function execute($model, $table_name, $id)
    {
        return $model::find($id);
    }
}

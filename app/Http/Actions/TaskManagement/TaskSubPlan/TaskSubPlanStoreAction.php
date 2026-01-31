<?php

namespace App\Http\Actions\TaskManagement\TaskSubPlan;

class TaskSubPlanStoreAction
{
    public static function execute($model, $table_name, array $data)
    {
        return $model::create($data);
    }
}

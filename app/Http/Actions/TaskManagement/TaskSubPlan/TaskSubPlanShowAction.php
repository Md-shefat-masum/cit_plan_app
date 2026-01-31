<?php

namespace App\Http\Actions\TaskManagement\TaskSubPlan;

class TaskSubPlanShowAction
{
    public static function execute($model, $table_name, $id)
    {
        return $model::with([
            'taskPlan:id,si,description',
            'timeDuration:id,title',
            'timeSubDuration:id,title',
            'taskCompletorCategory:id,title',
            'taskCompletorSubCategory:id,title',
            'umbrellaDepartment:id,title',
        ])->find($id);
    }
}

<?php

namespace App\Http\Actions\TaskManagement\TaskPlan;

class TaskPlanShowAction
{
    public static function execute($model, $table_name, $id)
    {
        return $model::with([
            'department:id,title',
            'departmentSection:id,title',
            'departmentSubSection:id,title',
            'dofa:id,title',
            'taskType:id,title',
            'taskStatus:id,title',
        ])->find($id);
    }
}

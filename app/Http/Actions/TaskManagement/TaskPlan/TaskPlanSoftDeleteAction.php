<?php

namespace App\Http\Actions\TaskManagement\TaskPlan;

class TaskPlanSoftDeleteAction
{
    public static function execute($model, $table_name, $id)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->update(['status' => 0]);
        return $item->fresh();
    }
}

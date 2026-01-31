<?php

namespace App\Http\Actions\TaskManagement\TaskPlan;

class TaskPlanRestoreAction
{
    public static function execute($model, $table_name, $id)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->update(['status' => 1]);
        return $item->fresh();
    }
}

<?php

namespace App\Http\Actions\TaskManagement\TaskSubPlan;

class TaskSubPlanRestoreAction
{
    public static function execute($model, $table_name, $id)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->update(['status' => 1]);
        return $item->fresh();
    }
}

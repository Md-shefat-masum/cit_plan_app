<?php

namespace App\Http\Actions\TaskManagement\TaskSubPlan;

class TaskSubPlanDestroyAction
{
    public static function execute($model, $table_name, $id)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->delete();
        return ['message' => 'Task sub plan deleted successfully'];
    }
}

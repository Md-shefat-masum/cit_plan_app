<?php

namespace App\Http\Actions\TaskManagement\TaskPlan;

class TaskPlanDestroyAction
{
    public static function execute($model, $table_name, $id)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->delete();
        return ['message' => 'Task plan deleted successfully'];
    }
}

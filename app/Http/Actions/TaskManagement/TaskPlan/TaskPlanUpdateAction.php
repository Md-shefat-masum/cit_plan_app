<?php

namespace App\Http\Actions\TaskManagement\TaskPlan;

class TaskPlanUpdateAction
{
    public static function execute($model, $table_name, $id, array $data)
    {
        $item = $model::find($id);
        if (!$item) return null;
        unset($data['si']);
        $item->update($data);
        return $item->fresh();
    }
}

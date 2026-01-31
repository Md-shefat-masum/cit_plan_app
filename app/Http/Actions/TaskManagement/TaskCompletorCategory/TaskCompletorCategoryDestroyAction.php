<?php

namespace App\Http\Actions\TaskManagement\TaskCompletorCategory;

class TaskCompletorCategoryDestroyAction
{
    public static function execute($model, $table_name, $id)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->delete();
        return ['message' => 'Task completor category deleted successfully'];
    }
}

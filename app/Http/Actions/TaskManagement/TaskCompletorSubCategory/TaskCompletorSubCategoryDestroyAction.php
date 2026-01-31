<?php

namespace App\Http\Actions\TaskManagement\TaskCompletorSubCategory;

class TaskCompletorSubCategoryDestroyAction
{
    public static function execute($model, $table_name, $id)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->delete();
        return ['message' => 'Task completor sub category deleted successfully'];
    }
}

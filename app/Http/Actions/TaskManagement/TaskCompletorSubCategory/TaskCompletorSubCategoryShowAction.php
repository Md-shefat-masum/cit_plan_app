<?php

namespace App\Http\Actions\TaskManagement\TaskCompletorSubCategory;

class TaskCompletorSubCategoryShowAction
{
    public static function execute($model, $table_name, $id)
    {
        return $model::with('taskCompletorCategory:id,title')->find($id);
    }
}

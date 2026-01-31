<?php

namespace App\Http\Actions\TaskManagement\TaskCompletorCategory;

class TaskCompletorCategoryShowAction
{
    public static function execute($model, $table_name, $id)
    {
        return $model::find($id);
    }
}

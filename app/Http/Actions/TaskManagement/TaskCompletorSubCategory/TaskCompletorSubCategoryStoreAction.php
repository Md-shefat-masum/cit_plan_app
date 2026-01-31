<?php

namespace App\Http\Actions\TaskManagement\TaskCompletorSubCategory;

class TaskCompletorSubCategoryStoreAction
{
    public static function execute($model, $table_name, array $data)
    {
        return $model::create($data);
    }
}

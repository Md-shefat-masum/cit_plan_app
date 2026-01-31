<?php

namespace App\Http\Actions\TaskManagement\TaskCompletorCategory;

class TaskCompletorCategoryStoreAction
{
    public static function execute($model, $table_name, array $data)
    {
        return $model::create($data);
    }
}

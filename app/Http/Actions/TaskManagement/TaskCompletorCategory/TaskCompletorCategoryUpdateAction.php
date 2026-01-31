<?php

namespace App\Http\Actions\TaskManagement\TaskCompletorCategory;

class TaskCompletorCategoryUpdateAction
{
    public static function execute($model, $table_name, $id, array $data)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->update($data);
        return $item->fresh();
    }
}

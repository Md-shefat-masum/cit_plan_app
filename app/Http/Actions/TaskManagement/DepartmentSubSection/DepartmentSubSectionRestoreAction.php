<?php

namespace App\Http\Actions\TaskManagement\DepartmentSubSection;

class DepartmentSubSectionRestoreAction
{
    public static function execute($model, $table_name, $id)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->update(['status' => 1]);
        return $item->fresh();
    }
}

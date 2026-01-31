<?php

namespace App\Http\Actions\TaskManagement\DepartmentSubSection;

class DepartmentSubSectionDestroyAction
{
    public static function execute($model, $table_name, $id)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->delete();
        return ['message' => 'Department sub section deleted successfully'];
    }
}

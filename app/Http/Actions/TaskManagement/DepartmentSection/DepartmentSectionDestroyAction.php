<?php

namespace App\Http\Actions\TaskManagement\DepartmentSection;

class DepartmentSectionDestroyAction
{
    public static function execute($model, $table_name, $id)
    {
        $departmentSection = $model::find($id);
        if (!$departmentSection) return null;
        $departmentSection->delete();
        return ['message' => 'Department section deleted successfully'];
    }
}

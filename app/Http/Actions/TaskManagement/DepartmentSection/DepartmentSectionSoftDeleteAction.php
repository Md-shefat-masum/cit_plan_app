<?php

namespace App\Http\Actions\TaskManagement\DepartmentSection;

class DepartmentSectionSoftDeleteAction
{
    public static function execute($model, $table_name, $id)
    {
        $departmentSection = $model::find($id);
        if (!$departmentSection) return null;
        $departmentSection->update(['status' => 0]);
        return $departmentSection->fresh();
    }
}

<?php

namespace App\Http\Actions\TaskManagement\DepartmentSection;

class DepartmentSectionRestoreAction
{
    public static function execute($model, $table_name, $id)
    {
        $departmentSection = $model::find($id);
        if (!$departmentSection) return null;
        $departmentSection->update(['status' => 1]);
        return $departmentSection->fresh();
    }
}

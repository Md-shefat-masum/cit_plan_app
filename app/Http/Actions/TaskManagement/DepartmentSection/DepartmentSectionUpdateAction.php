<?php

namespace App\Http\Actions\TaskManagement\DepartmentSection;

class DepartmentSectionUpdateAction
{
    public static function execute($model, $table_name, $id, array $data)
    {
        $departmentSection = $model::find($id);
        if (!$departmentSection) return null;
        $departmentSection->update($data);
        return $departmentSection->fresh();
    }
}

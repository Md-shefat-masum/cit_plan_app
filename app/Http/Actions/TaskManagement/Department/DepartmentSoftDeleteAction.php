<?php

namespace App\Http\Actions\TaskManagement\Department;

class DepartmentSoftDeleteAction
{
    public static function execute($model, $table_name, $id)
    {
        $department = $model::find($id);
        if (!$department) return null;
        $department->update(['status' => 0]);
        return $department->fresh();
    }
}

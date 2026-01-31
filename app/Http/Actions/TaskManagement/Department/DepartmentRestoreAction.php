<?php

namespace App\Http\Actions\TaskManagement\Department;

class DepartmentRestoreAction
{
    public static function execute($model, $table_name, $id)
    {
        $department = $model::find($id);
        if (!$department) return null;
        $department->update(['status' => 1]);
        return $department->fresh();
    }
}

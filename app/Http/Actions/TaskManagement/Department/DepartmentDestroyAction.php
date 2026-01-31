<?php

namespace App\Http\Actions\TaskManagement\Department;

class DepartmentDestroyAction
{
    public static function execute($model, $table_name, $id)
    {
        $department = $model::find($id);
        if (!$department) return null;
        $department->delete();
        return ['message' => 'Department deleted successfully'];
    }
}

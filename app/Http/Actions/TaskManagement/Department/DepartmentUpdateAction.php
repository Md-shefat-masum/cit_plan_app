<?php

namespace App\Http\Actions\TaskManagement\Department;

class DepartmentUpdateAction
{
    public static function execute($model, $table_name, $id, array $data)
    {
        $department = $model::find($id);
        if (!$department) return null;
        $department->update($data);
        return $department->fresh();
    }
}

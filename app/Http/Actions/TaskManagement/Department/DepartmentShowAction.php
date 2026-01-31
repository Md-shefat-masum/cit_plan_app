<?php

namespace App\Http\Actions\TaskManagement\Department;

class DepartmentShowAction
{
    public static function execute($model, $table_name, $id)
    {
        return $model::find($id);
    }
}

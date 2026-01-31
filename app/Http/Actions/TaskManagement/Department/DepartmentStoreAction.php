<?php

namespace App\Http\Actions\TaskManagement\Department;

class DepartmentStoreAction
{
    public static function execute($model, $table_name, array $data)
    {
        return $model::create($data);
    }
}

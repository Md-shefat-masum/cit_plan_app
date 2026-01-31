<?php

namespace App\Http\Actions\TaskManagement\DepartmentSection;

class DepartmentSectionStoreAction
{
    public static function execute($model, $table_name, array $data)
    {
        return $model::create($data);
    }
}

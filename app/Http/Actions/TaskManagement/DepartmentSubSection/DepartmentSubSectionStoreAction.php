<?php

namespace App\Http\Actions\TaskManagement\DepartmentSubSection;

class DepartmentSubSectionStoreAction
{
    public static function execute($model, $table_name, array $data)
    {
        return $model::create($data);
    }
}

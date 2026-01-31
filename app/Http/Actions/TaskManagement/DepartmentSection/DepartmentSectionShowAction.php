<?php

namespace App\Http\Actions\TaskManagement\DepartmentSection;

class DepartmentSectionShowAction
{
    public static function execute($model, $table_name, $id)
    {
        return $model::with('department:id,title')->find($id);
    }
}

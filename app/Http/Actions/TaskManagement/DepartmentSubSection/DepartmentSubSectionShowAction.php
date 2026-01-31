<?php

namespace App\Http\Actions\TaskManagement\DepartmentSubSection;

class DepartmentSubSectionShowAction
{
    public static function execute($model, $table_name, $id)
    {
        return $model::with(['department:id,title', 'departmentSection:id,title'])->find($id);
    }
}

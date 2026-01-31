<?php

namespace App\Http\Actions\TaskManagement\TimeDuration;

class TimeDurationShowAction
{
    public static function execute($model, $table_name, $id)
    {
        return $model::find($id);
    }
}

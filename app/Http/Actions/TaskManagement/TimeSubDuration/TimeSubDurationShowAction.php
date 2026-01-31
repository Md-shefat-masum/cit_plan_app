<?php

namespace App\Http\Actions\TaskManagement\TimeSubDuration;

class TimeSubDurationShowAction
{
    public static function execute($model, $table_name, $id)
    {
        return $model::with('timeDuration:id,title')->find($id);
    }
}

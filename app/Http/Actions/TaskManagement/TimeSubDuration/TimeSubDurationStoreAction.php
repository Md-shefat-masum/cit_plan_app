<?php

namespace App\Http\Actions\TaskManagement\TimeSubDuration;

class TimeSubDurationStoreAction
{
    public static function execute($model, $table_name, array $data)
    {
        return $model::create($data);
    }
}

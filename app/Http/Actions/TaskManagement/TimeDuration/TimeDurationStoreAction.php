<?php

namespace App\Http\Actions\TaskManagement\TimeDuration;

class TimeDurationStoreAction
{
    public static function execute($model, $table_name, array $data)
    {
        return $model::create($data);
    }
}

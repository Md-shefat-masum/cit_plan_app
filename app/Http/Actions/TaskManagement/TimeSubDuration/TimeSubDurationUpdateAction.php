<?php

namespace App\Http\Actions\TaskManagement\TimeSubDuration;

class TimeSubDurationUpdateAction
{
    public static function execute($model, $table_name, $id, array $data)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->update($data);
        return $item->fresh();
    }
}

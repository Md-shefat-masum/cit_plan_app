<?php

namespace App\Http\Actions\TaskManagement\TimeDuration;

class TimeDurationUpdateAction
{
    public static function execute($model, $table_name, $id, array $data)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->update($data);
        return $item->fresh();
    }
}

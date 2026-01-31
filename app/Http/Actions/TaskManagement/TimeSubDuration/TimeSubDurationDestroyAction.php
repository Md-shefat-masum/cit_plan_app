<?php

namespace App\Http\Actions\TaskManagement\TimeSubDuration;

class TimeSubDurationDestroyAction
{
    public static function execute($model, $table_name, $id)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->delete();
        return ['message' => 'Time sub duration deleted successfully'];
    }
}

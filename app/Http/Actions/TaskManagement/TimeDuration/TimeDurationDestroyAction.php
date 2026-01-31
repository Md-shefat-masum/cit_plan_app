<?php

namespace App\Http\Actions\TaskManagement\TimeDuration;

class TimeDurationDestroyAction
{
    public static function execute($model, $table_name, $id)
    {
        $item = $model::find($id);
        if (!$item) return null;
        $item->delete();
        return ['message' => 'Time duration deleted successfully'];
    }
}

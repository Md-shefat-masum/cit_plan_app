<?php

namespace App\Http\Actions\DofaManagement\Dofa;

class DofaAnalyticsAction
{
    public static function execute($model, $table_name)
    {
        return [
            'total' => $model::count(),
            'active' => $model::where('status', 1)->count(),
            'inactive' => $model::where('status', 0)->count(),
        ];
    }
}

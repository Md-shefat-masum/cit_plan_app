<?php

namespace App\Http\Actions\DofaManagement\Dofa;

class DofaStoreAction
{
    public static function execute($model, $table_name, array $data)
    {
        return $model::create($data);
    }
}

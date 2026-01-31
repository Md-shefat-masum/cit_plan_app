<?php

namespace App\Http\Actions\DofaManagement\Dofa;

class DofaShowAction
{
    public static function execute($model, $table_name, $id)
    {
        return $model::find($id);
    }
}

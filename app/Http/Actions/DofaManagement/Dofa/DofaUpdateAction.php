<?php

namespace App\Http\Actions\DofaManagement\Dofa;

class DofaUpdateAction
{
    public static function execute($model, $table_name, $id, array $data)
    {
        $dofa = $model::find($id);
        if (!$dofa) return null;
        $dofa->update($data);
        return $dofa->fresh();
    }
}

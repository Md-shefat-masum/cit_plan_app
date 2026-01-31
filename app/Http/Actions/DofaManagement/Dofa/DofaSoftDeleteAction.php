<?php

namespace App\Http\Actions\DofaManagement\Dofa;

class DofaSoftDeleteAction
{
    public static function execute($model, $table_name, $id)
    {
        $dofa = $model::find($id);
        if (!$dofa) return null;
        $dofa->update(['status' => 0]);
        return $dofa->fresh();
    }
}

<?php

namespace App\Http\Actions\DofaManagement\Dofa;

class DofaDestroyAction
{
    public static function execute($model, $table_name, $id)
    {
        $dofa = $model::find($id);
        if (!$dofa) return null;
        $dofa->delete();
        return ['message' => 'Dofa deleted successfully'];
    }
}

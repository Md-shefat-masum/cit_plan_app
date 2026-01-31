<?php

namespace App\Http\Actions\DofaManagement\Dofa;

class DofaRestoreAction
{
    public static function execute($model, $table_name, $id)
    {
        $dofa = $model::find($id);
        if (!$dofa) return null;
        $dofa->update(['status' => 1]);
        return $dofa->fresh();
    }
}

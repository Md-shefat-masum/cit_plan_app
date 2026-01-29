<?php

namespace App\Http\Actions\AppModuleManagement\AppModule;

class AppModuleShowAction
{
    /**
     * Execute the show operation
     *
     * @param string $model
     * @param string $table_name
     * @param int $id
     * @return mixed
     */
    public static function execute($model, $table_name, $id)
    {
        $appModule = $model::find($id);
        return $appModule;
    }
}

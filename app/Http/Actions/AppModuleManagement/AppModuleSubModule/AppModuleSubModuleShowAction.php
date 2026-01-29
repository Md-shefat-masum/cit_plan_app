<?php

namespace App\Http\Actions\AppModuleManagement\AppModuleSubModule;

class AppModuleSubModuleShowAction
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
        $appModuleSubModule = $model::find($id);
        return $appModuleSubModule;
    }
}

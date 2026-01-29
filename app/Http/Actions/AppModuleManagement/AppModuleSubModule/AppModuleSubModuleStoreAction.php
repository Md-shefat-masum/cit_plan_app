<?php

namespace App\Http\Actions\AppModuleManagement\AppModuleSubModule;

class AppModuleSubModuleStoreAction
{
    /**
     * Execute the store operation
     *
     * @param string $model
     * @param string $table_name
     * @param array $data
     * @return mixed
     */
    public static function execute($model, $table_name, array $data)
    {
        $appModuleSubModule = $model::create($data);
        return $appModuleSubModule;
    }
}

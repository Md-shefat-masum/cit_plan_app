<?php

namespace App\Http\Actions\AppModuleManagement\AppModule;

class AppModuleStoreAction
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
        $appModule = $model::create($data);
        return $appModule;
    }
}

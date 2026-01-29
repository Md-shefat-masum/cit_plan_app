<?php

namespace App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint;

class AppModuleSubModuleEndpointStoreAction
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
        $endpoint = $model::create($data);
        return $endpoint;
    }
}

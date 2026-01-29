<?php

namespace App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint;

class AppModuleSubModuleEndpointShowAction
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
        $endpoint = $model::find($id);
        return $endpoint;
    }
}

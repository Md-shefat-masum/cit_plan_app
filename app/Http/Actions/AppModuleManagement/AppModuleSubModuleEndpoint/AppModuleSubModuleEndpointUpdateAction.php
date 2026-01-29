<?php

namespace App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint;

class AppModuleSubModuleEndpointUpdateAction
{
    /**
     * Execute the update operation
     *
     * @param string $model
     * @param string $table_name
     * @param int $id
     * @param array $data
     * @return mixed|null
     */
    public static function execute($model, $table_name, $id, array $data)
    {
        $endpoint = $model::find($id);
        if (!$endpoint) {
            return null;
        }

        $endpoint->update($data);
        return $endpoint->fresh();
    }
}

<?php

namespace App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint;

class AppModuleSubModuleEndpointDestroyAction
{
    /**
     * Execute the destroy operation
     *
     * @param string $model
     * @param string $table_name
     * @param int $id
     * @return array|null
     */
    public static function execute($model, $table_name, $id)
    {
        $endpoint = $model::find($id);
        if (!$endpoint) {
            return null;
        }

        $endpoint->delete();
        return ['message' => 'App module sub module endpoint deleted successfully'];
    }
}

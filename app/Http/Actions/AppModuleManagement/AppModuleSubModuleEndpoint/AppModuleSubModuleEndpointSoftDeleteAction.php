<?php

namespace App\Http\Actions\AppModuleManagement\AppModuleSubModuleEndpoint;

class AppModuleSubModuleEndpointSoftDeleteAction
{
    /**
     * Execute the soft delete operation
     *
     * @param string $model
     * @param string $table_name
     * @param int $id
     * @return mixed|null
     */
    public static function execute($model, $table_name, $id)
    {
        $endpoint = $model::find($id);
        if (!$endpoint) {
            return null;
        }

        $endpoint->update(['status' => 0]);
        return $endpoint->fresh();
    }
}

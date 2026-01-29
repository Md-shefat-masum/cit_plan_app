<?php

namespace App\Http\Actions\AppModuleManagement\AppModuleSubModule;

class AppModuleSubModuleUpdateAction
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
        $appModuleSubModule = $model::find($id);
        if (!$appModuleSubModule) {
            return null;
        }

        $appModuleSubModule->update($data);
        return $appModuleSubModule->fresh();
    }
}

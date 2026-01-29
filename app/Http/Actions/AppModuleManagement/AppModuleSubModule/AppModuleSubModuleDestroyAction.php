<?php

namespace App\Http\Actions\AppModuleManagement\AppModuleSubModule;

class AppModuleSubModuleDestroyAction
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
        $appModuleSubModule = $model::find($id);
        if (!$appModuleSubModule) {
            return null;
        }

        $appModuleSubModule->delete();
        return ['message' => 'App module sub module deleted successfully'];
    }
}

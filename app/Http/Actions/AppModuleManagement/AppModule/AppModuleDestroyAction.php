<?php

namespace App\Http\Actions\AppModuleManagement\AppModule;

class AppModuleDestroyAction
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
        $appModule = $model::find($id);
        if (!$appModule) {
            return null;
        }

        $appModule->delete();
        return ['message' => 'App module deleted successfully'];
    }
}

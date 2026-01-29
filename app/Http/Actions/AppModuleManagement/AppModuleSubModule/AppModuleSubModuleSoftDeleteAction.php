<?php

namespace App\Http\Actions\AppModuleManagement\AppModuleSubModule;

class AppModuleSubModuleSoftDeleteAction
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
        $appModuleSubModule = $model::find($id);
        if (!$appModuleSubModule) {
            return null;
        }

        $appModuleSubModule->update(['status' => 0]);
        return $appModuleSubModule->fresh();
    }
}

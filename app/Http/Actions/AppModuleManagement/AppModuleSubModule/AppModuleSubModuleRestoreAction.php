<?php

namespace App\Http\Actions\AppModuleManagement\AppModuleSubModule;

class AppModuleSubModuleRestoreAction
{
    /**
     * Execute the restore operation
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

        $appModuleSubModule->update(['status' => 1]);
        return $appModuleSubModule->fresh();
    }
}

<?php

namespace App\Http\Actions\AppModuleManagement\AppModule;

class AppModuleRestoreAction
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
        $appModule = $model::find($id);
        if (!$appModule) {
            return null;
        }

        $appModule->update(['status' => 1]);
        return $appModule->fresh();
    }
}

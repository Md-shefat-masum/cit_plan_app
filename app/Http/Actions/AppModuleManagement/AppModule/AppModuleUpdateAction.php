<?php

namespace App\Http\Actions\AppModuleManagement\AppModule;

class AppModuleUpdateAction
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
        $appModule = $model::find($id);
        if (!$appModule) {
            return null;
        }

        $appModule->update($data);
        return $appModule->fresh();
    }
}

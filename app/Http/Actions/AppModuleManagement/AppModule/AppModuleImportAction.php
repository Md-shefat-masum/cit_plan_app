<?php

namespace App\Http\Actions\AppModuleManagement\AppModule;

class AppModuleImportAction
{
    /**
     * Execute the import operation
     *
     * @param string $model
     * @param string $table_name
     * @param array $data
     * @return array
     */
    public static function execute($model, $table_name, array $data)
    {
        $imported = [];
        foreach ($data as $item) {
            $item['slug'] = uniqid();
            $item['creator'] = auth('api')->id() ?? 0;
            $item['status'] = $item['status'] ?? 1;
            $imported[] = $model::create($item);
        }
        return $imported;
    }
}

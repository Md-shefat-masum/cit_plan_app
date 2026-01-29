<?php

namespace App\Http\Actions\UserManagement\User;

use Illuminate\Support\Facades\Hash;

class UserImportAction
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
            // Hash password if present
            if (isset($item['password'])) {
                $item['password'] = Hash::make($item['password']);
            }

            // Set slug from username if not provided
            if (empty($item['slug']) && !empty($item['username'])) {
                $item['slug'] = $item['username'];
            }

            $item['creator'] = auth('api')->id() ?? 0;
            $item['status'] = $item['status'] ?? 1;
            $imported[] = $model::create($item);
        }
        return $imported;
    }
}

<?php

namespace App\Http\Actions\UserManagement\User;

use Illuminate\Support\Facades\Hash;

class UserStoreAction
{
    /**
     * Execute the store operation
     *
     * @param string $model
     * @param string $table_name
     * @param array $data
     * @return mixed
     */
    public static function execute($model, $table_name, array $data)
    {
        // Hash password if present
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Set slug from username if not provided
        if (empty($data['slug']) && !empty($data['username'])) {
            $data['slug'] = $data['username'];
        }

        $user = $model::create($data);
        return $user;
    }
}

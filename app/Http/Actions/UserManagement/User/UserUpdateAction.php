<?php

namespace App\Http\Actions\UserManagement\User;

use Illuminate\Support\Facades\Hash;

class UserUpdateAction
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
        $user = $model::find($id);
        if (!$user) {
            return null;
        }

        // Hash password if present
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Update slug from username if username changed
        if (isset($data['username']) && empty($data['slug'])) {
            $data['slug'] = $data['username'];
        }

        $user->update($data);
        return $user->fresh();
    }
}

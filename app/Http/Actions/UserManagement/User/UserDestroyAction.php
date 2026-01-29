<?php

namespace App\Http\Actions\UserManagement\User;

class UserDestroyAction
{
    /**
     * Execute the destroy operation
     * Note: According to instructions, users should not be destroyed, only soft deleted
     * This method is kept for consistency but can be disabled if needed
     *
     * @param string $model
     * @param string $table_name
     * @param int $id
     * @return array|null
     */
    public static function execute($model, $table_name, $id)
    {
        $user = $model::find($id);
        if (!$user) {
            return null;
        }

        $user->delete();
        return ['message' => 'User deleted successfully'];
    }
}

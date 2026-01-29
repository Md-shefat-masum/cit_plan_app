<?php

namespace App\Http\Actions\UserManagement\UserRole;

class UserRoleDestroyAction
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
        $userRole = $model::find($id);
        if (!$userRole) {
            return null;
        }

        $userRole->delete();
        return ['message' => 'User role deleted successfully'];
    }
}

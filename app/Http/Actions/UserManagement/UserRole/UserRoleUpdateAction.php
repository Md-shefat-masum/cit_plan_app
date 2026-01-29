<?php

namespace App\Http\Actions\UserManagement\UserRole;

class UserRoleUpdateAction
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
        $userRole = $model::find($id);
        if (!$userRole) {
            return null;
        }

        $userRole->update($data);
        return $userRole->fresh();
    }
}

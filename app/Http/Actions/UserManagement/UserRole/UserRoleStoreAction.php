<?php

namespace App\Http\Actions\UserManagement\UserRole;

class UserRoleStoreAction
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
        $userRole = $model::create($data);
        return $userRole;
    }
}

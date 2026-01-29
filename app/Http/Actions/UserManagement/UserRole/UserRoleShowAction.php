<?php

namespace App\Http\Actions\UserManagement\UserRole;

class UserRoleShowAction
{
    /**
     * Execute the show operation
     *
     * @param string $model
     * @param string $table_name
     * @param int $id
     * @return mixed
     */
    public static function execute($model, $table_name, $id)
    {
        $userRole = $model::find($id);
        return $userRole;
    }
}

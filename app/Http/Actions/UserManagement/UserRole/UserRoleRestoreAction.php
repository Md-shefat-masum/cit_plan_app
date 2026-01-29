<?php

namespace App\Http\Actions\UserManagement\UserRole;

class UserRoleRestoreAction
{
    /**
     * Execute the restore operation
     *
     * @param string $model
     * @param string $table_name
     * @param int $id
     * @return mixed|null
     */
    public static function execute($model, $table_name, $id)
    {
        $userRole = $model::find($id);
        if (!$userRole) {
            return null;
        }

        $userRole->update(['status' => 1]);
        return $userRole->fresh();
    }
}

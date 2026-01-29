<?php

namespace App\Http\Actions\UserManagement\User;

class UserRestoreAction
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
        $user = $model::find($id);
        if (!$user) {
            return null;
        }

        $user->update(['status' => 1]);
        return $user->fresh();
    }
}

<?php

namespace App\Http\Actions\UserManagement\User;

class UserSoftDeleteAction
{
    /**
     * Execute the soft delete operation (set status = 0)
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

        $user->update(['status' => 0]);
        return $user->fresh();
    }
}

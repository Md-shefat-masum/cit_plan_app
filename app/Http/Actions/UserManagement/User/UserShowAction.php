<?php

namespace App\Http\Actions\UserManagement\User;

class UserShowAction
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
        $user = $model::with(['userRole'])->find($id);
        return $user;
    }
}

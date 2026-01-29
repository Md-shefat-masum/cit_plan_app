<?php

namespace App\Http\Actions\UserManagement\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserIndexAction
{
    /**
     * Execute the query with flexible parameters
     *
     * @param string $model
     * @param string $table_name
     * @return array
     */
    public static function execute($model, $table_name)
    {
        $user_roles_table = 'user_roles';
        $creators_table = 'users';
        $creators_alias = 'creators';

        $fields = request()->fields ?? [
            'id',
            'user_role_id',
            'name',
            'username',
            'email',
            'creator',
            'status',
            'slug',
            'created_at',
            'updated_at',
        ];

        // prefix
        $fields = array_map(function ($col) use ($table_name) {
            return Str::contains($col, '.') ? $col : $table_name . '.' . $col;
        }, $fields);

        // user_roles join
        $fields = array_merge($fields, [
            "$user_roles_table.title as user_role_title",
        ]);

        // creators join (for creator_name) - join users table as creators
        $fields = array_merge($fields, [
            "$creators_alias.name as creator_name",
        ]);

        $relations = request()->relations ?? [];
        $conditions = request()->conditions ?? [];
        $paginate = request()->paginate ?? 10;
        $search = request()->search ?? null;
        $orderByCol = request()->orderByCol ?? 'id';
        $orderByAsc = request()->orderByAsc == 1 ? 'asc' : 'desc';
        $status = request()->status ?? 1;

        if (!Str::contains($orderByCol, '.')) {
            $orderByCol = $table_name . '.' . $orderByCol;
        }

        // মূল query
        $query = $model::query()
            ->from($table_name)
            ->leftJoin($user_roles_table, "$user_roles_table.id", '=', "$table_name.user_role_id")
            ->leftJoin("$creators_table as $creators_alias", "$creators_alias.id", '=', "$table_name.creator")
            ->where("$table_name.status", $status)
            ->select($fields);

        // conditions
        if (count($conditions) > 0) {
            foreach ($conditions as $key => $value) {
                $col = Str::contains($key, '.') ? $key : $table_name . '.' . $key;
                $query->where($col, $value);
            }
        }

        // relations
        if (count($relations) > 0) {
            $query->with([...$relations]);
        }

        // 🔍 search block - search by all fields
        if ($search) {
            $query->where(function ($query) use ($search, $table_name, $user_roles_table, $creators_alias) {
                $query
                    ->where("$table_name.id", 'like', "%{$search}%")
                    ->orWhere("$table_name.username", 'like', "%{$search}%")
                    ->orWhere("$table_name.email", 'like', "%{$search}%")
                    ->orWhere("$table_name.name", 'like', "%{$search}%")
                    ->orWhere("$table_name.slug", 'like', "%{$search}%")
                    ->orWhere("$user_roles_table.title", 'like', "%{$search}%")
                    ->orWhere("$creators_alias.name", 'like', "%{$search}%");
            });
        }

        // Auth based filter (if needed)
        $auth_user = Auth::guard('api')->user();
        if ($auth_user) {
            // Add auth-based filtering logic here if needed
            // Example: $query->where("$table_name.creator", $auth_user->id);
        }

        $data = $query
            ->orderBy($orderByCol, $orderByAsc)
            ->paginate($paginate)
            ->onEachSide(0)
            ->appends(request()->all());

        return [
            ...$data->toArray(),
        ];
    }
}

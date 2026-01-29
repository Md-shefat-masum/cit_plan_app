<?php

namespace App\Models\UserManagement;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserRole extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'status',
        'slug',
        'creator',
    ];

    /**
     * Get the users for the user role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_role_id');
    }
}

<?php

namespace App\Models\AppModuleManagement;

use App\Models\UserManagement\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppModuleRole extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'app_module_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'app_module_id',
        'app_module_sub_module_id',
        'app_module_sub_module_endpoint_id',
        'user_role_id',
        'status',
        'slug',
        'creator',
    ];

    /**
     * Get the app module.
     */
    public function appModule(): BelongsTo
    {
        return $this->belongsTo(AppModule::class, 'app_module_id');
    }

    /**
     * Get the app module sub module.
     */
    public function appModuleSubModule(): BelongsTo
    {
        return $this->belongsTo(AppModuleSubModule::class, 'app_module_sub_module_id');
    }

    /**
     * Get the app module sub module endpoint.
     */
    public function appModuleSubModuleEndpoint(): BelongsTo
    {
        return $this->belongsTo(AppModuleSubModuleEndpoint::class, 'app_module_sub_module_endpoint_id');
    }

    /**
     * Get the user role.
     */
    public function userRole(): BelongsTo
    {
        return $this->belongsTo(UserRole::class, 'user_role_id');
    }
}

<?php

namespace App\Models\AppModuleManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppModuleSubModuleEndpoint extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'app_module_sub_module_endpoints';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'app_module_id',
        'app_module_sub_module_id',
        'uri',
        'action_key',
        'title',
        'status',
        'slug',
        'creator',
    ];

    /**
     * Get the app module that owns the endpoint.
     */
    public function appModule(): BelongsTo
    {
        return $this->belongsTo(AppModule::class, 'app_module_id');
    }

    /**
     * Get the app module sub module that owns the endpoint.
     */
    public function appModuleSubModule(): BelongsTo
    {
        return $this->belongsTo(AppModuleSubModule::class, 'app_module_sub_module_id');
    }
}

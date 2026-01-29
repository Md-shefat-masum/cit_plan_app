<?php

namespace App\Models\AppModuleManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppModuleSubModule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'app_module_sub_modules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'app_module_id',
        'title',
        'status',
        'slug',
        'creator',
    ];

    /**
     * Get the app module that owns the sub module.
     */
    public function appModule(): BelongsTo
    {
        return $this->belongsTo(AppModule::class, 'app_module_id');
    }
}

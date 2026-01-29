<?php

namespace App\Models\AppModuleManagement;

use Illuminate\Database\Eloquent\Model;

class AppModule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'app_modules';

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
}

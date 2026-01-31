<?php

namespace App\Models\DofaManagement;

use Illuminate\Database\Eloquent\Model;

class Dofa extends Model
{
    protected $table = 'dofas';

    protected $fillable = [
        'title',
        'status',
        'slug',
        'creator',
    ];
}

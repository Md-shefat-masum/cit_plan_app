<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

    protected $fillable = [
        'title',
        'status',
        'slug',
        'creator',
    ];
}

<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Model;

class TimeDuration extends Model
{
    protected $table = 'time_durations';

    protected $fillable = [
        'title',
        'status',
        'slug',
        'creator',
    ];
}

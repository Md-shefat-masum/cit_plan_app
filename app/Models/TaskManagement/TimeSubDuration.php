<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeSubDuration extends Model
{
    protected $table = 'time_sub_durations';

    protected $fillable = [
        'time_duration_id',
        'title',
        'status',
        'slug',
        'creator',
    ];

    public function timeDuration(): BelongsTo
    {
        return $this->belongsTo(TimeDuration::class, 'time_duration_id');
    }
}

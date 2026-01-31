<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_statuses';

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

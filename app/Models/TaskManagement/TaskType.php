<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Model;

class TaskType extends Model
{
    protected $table = 'task_types';

    protected $fillable = [
        'title',
        'status',
        'slug',
        'creator',
    ];
}

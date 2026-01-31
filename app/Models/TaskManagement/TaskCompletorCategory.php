<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Model;

class TaskCompletorCategory extends Model
{
    protected $table = 'task_completor_categories';

    protected $fillable = [
        'title',
        'status',
        'slug',
        'creator',
    ];
}

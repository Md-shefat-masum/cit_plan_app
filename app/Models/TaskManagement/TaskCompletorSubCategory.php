<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskCompletorSubCategory extends Model
{
    protected $table = 'task_completor_sub_categories';

    protected $fillable = [
        'task_completor_category_id',
        'title',
        'status',
        'slug',
        'creator',
    ];

    public function taskCompletorCategory(): BelongsTo
    {
        return $this->belongsTo(TaskCompletorCategory::class, 'task_completor_category_id');
    }
}

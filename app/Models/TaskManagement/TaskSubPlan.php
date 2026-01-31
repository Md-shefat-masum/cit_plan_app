<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskSubPlan extends Model
{
    protected $table = 'task_sub_plans';

    protected $fillable = [
        'task_plan_id',
        'description',
        'time_duration_id',
        'time_sub_duration_id',
        'task_completor_category_id',
        'task_completor_sub_category_id',
        'umbrella_department_id',
        'status',
        'slug',
        'creator',
    ];

    public function taskPlan(): BelongsTo
    {
        return $this->belongsTo(TaskPlan::class, 'task_plan_id');
    }

    public function timeDuration(): BelongsTo
    {
        return $this->belongsTo(TimeDuration::class, 'time_duration_id');
    }

    public function timeSubDuration(): BelongsTo
    {
        return $this->belongsTo(TimeSubDuration::class, 'time_sub_duration_id');
    }

    public function taskCompletorCategory(): BelongsTo
    {
        return $this->belongsTo(TaskCompletorCategory::class, 'task_completor_category_id');
    }

    public function taskCompletorSubCategory(): BelongsTo
    {
        return $this->belongsTo(TaskCompletorSubCategory::class, 'task_completor_sub_category_id');
    }

    public function umbrellaDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'umbrella_department_id');
    }
}

<?php

namespace App\Models\TaskManagement;

use App\Models\DofaManagement\Dofa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskPlan extends Model
{
    protected $table = 'task_plans';

    protected $fillable = [
        'si',
        'department_id',
        'department_section_id',
        'department_sub_section_id',
        'dofa_id',
        'description',
        'qty',
        'task_type_id',
        'task_status_id',
        'status',
        'slug',
        'creator',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function departmentSection(): BelongsTo
    {
        return $this->belongsTo(DepartmentSection::class, 'department_section_id');
    }

    public function departmentSubSection(): BelongsTo
    {
        return $this->belongsTo(DepartmentSubSection::class, 'department_sub_section_id');
    }

    public function dofa(): BelongsTo
    {
        return $this->belongsTo(Dofa::class, 'dofa_id');
    }

    public function taskType(): BelongsTo
    {
        return $this->belongsTo(TaskType::class, 'task_type_id');
    }

    public function taskStatus(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }
}

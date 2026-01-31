<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentSubSection extends Model
{
    protected $table = 'department_sub_sections';

    protected $fillable = [
        'department_id',
        'department_section_id',
        'title',
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
}

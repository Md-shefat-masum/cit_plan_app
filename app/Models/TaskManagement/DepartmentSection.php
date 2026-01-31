<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentSection extends Model
{
    protected $table = 'department_sections';

    protected $fillable = [
        'department_id',
        'title',
        'status',
        'slug',
        'creator',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}

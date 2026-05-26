<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Grade extends Trackable
{
    protected function casts(): array
    {
        return [
            'assessment_methods' => 'array',
            'meeting_methods' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'classroom')
            ->withTimestamps()
            ->withPivot(['name', 'capacity', 'form_teacher_id', 'status'])
            ->as('classroom')
            ->using(Classroom::class);
    }

    public function students(): HasManyThrough
    {
        return $this->throughClassrooms()->hasStudents();
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class)->withTimestamps();
    }

    public function yearHead(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'year_head_id');
    }
}

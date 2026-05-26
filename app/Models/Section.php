<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Trackable
{
    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(Grade::class, 'classroom')
            ->withTimestamps()
            ->withPivot(['name', 'capacity', 'form_teacher_id', 'status'])
            ->as('classroom')
            ->using(Classroom::class);
    }
}

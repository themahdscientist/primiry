<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description', 'status'])]
class Section extends Model
{
    use SoftDeletes;

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

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}

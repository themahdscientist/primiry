<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'code', 'description', 'type'])]
class Subject extends Model
{
    use SoftDeletes;

    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(Grade::class)->withTimestamps();
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}

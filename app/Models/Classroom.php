<?php

namespace App\Models;

use App\Enums\GeneralStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'capacity', 'form_teacher_id', 'grade_id', 'section_id', 'status'])]
class Classroom extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        static::restoring(function (Classroom $classroom): void {
            $classroom->status = GeneralStatus::Active->value;
        });
    }

    public function formTeacher(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'form_teacher_id');
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class)->withTimestamps();
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * i can't remember why i have a staff classroom pivot and relationship.
     * find out why.
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class)->withTimestamps();
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function hasActiveClassroomConflict(): bool
    {
        return static::query()
            ->where('school_id', $this->school_id)
            ->where('grade_id', $this->grade_id)
            ->where('section_id', $this->section_id)
            ->where('status', GeneralStatus::Active->value)
            ->whereKeyNot($this->getKey())
            ->exists();
    }

    protected function runSoftDelete(): void
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery());

        $time = $this->freshTimestamp();

        $columns = [
            $this->getDeletedAtColumn() => $this->fromDateTime($time),
            'status' => GeneralStatus::Inactive->value,
        ];

        $this->{$this->getDeletedAtColumn()} = $time;
        $this->status = GeneralStatus::Inactive->value;

        if ($this->usesTimestamps() && ! is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;
            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['bank_account_details', 'code', 'contract_expiry_date', 'emergency_contact', 'employment_type', 'marital_status', 'notes', 'qualifications', 'salary'])]
class Staff extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'bank_account_details' => 'array',
            'qualifications' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formClasses(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function yearGrades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class)->withTimestamps();
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}

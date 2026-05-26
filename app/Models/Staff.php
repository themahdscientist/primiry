<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
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
}

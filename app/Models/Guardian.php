<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guardian extends Model
{
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);

    }
}

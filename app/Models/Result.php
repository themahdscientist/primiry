<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([])]
class Result extends Model
{
    use SoftDeletes;

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}

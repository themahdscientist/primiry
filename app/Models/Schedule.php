<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Trackable
{
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}

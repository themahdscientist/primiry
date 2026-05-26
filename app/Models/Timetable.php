<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timetable extends Trackable
{
    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['classroom_id', 'date', 'end_time', 'exam_id', 'full_mark', 'pass_mark', 'start_time', 'subject_id'])]
class Schedule extends Model
{
    use SoftDeletes;

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

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}

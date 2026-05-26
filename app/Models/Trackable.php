<?php

namespace App\Models;

use App\Models\Scopes\TrackableScope;
use App\Observers\TrackableObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy(TrackableScope::class)]
#[ObservedBy(TrackableObserver::class)]
class Trackable extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

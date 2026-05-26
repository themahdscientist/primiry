<?php

namespace App\Observers;

use App\Models\Trackable;
use Filament\Facades\Filament;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class TrackableObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Trackable "created" event.
     */
    public function created(Trackable $trackable): void
    {
        if (! Filament::auth()->check()) {
            return;
        }

        if (Filament::auth()->user()->isAdmin()) {
            $trackable->user()->associate(Filament::auth()->user())->saveQuietly();

            return;
        }

        $trackable->user()->associate(Filament::auth()->user()->user_id)->saveQuietly();
    }

    /**
     * Handle the Trackable "updated" event.
     */
    public function updated(Trackable $trackable): void
    {
        //
    }

    /**
     * Handle the Trackable "deleted" event.
     */
    public function deleted(Trackable $trackable): void
    {
        //
    }

    /**
     * Handle the Trackable "restored" event.
     */
    public function restored(Trackable $trackable): void
    {
        //
    }

    /**
     * Handle the Trackable "force deleted" event.
     */
    public function forceDeleted(Trackable $trackable): void
    {
        //
    }
}

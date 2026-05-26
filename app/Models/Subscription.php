<?php

namespace App\Models;

class Subscription extends Trackable
{
    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class School extends Model
{
    protected $fillable = [
        'name',
        'code',
        'domain',
        'owner_user_id',
        'settings',
        'billing_info',
        'trial_ends_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'billing_info' => 'array',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}

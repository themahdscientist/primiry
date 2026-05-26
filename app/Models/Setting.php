<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use function Illuminate\Events\queueable;

class Setting extends Trackable
{
    protected static function booted()
    {
        static::created(queueable(function (self $setting) {
            do {
                $code = config('primiry.code.prefix').'-'.now()->format('Y').'-'.substr(strtoupper(uniqid()), -6);
            } while (self::whereSchCode($code)->exists());

            $setting->update(['sch_code' => $code]);
        }));
    }

    protected $hidden = [
        'bank_acc_no',
    ];

    protected function casts(): array
    {
        return [
            'bank_acc_no' => 'hashed',
            'sch_affiliates' => 'array',
            'sch_aliases' => 'array',
            'sch_contact' => 'array',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'sch_country', 'iso2');
    }

    public function billableCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'billable_country_code', 'iso2');
    }
}

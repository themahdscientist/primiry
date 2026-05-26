<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function countryOfResidence(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_of_residence', 'iso2');
    }

    public function stateOfResidence(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_of_residence', 'state_code');
    }

    // you can't have a function and model attribute share the same name, hence => nationalityy.
    public function nationalityy(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'nationality', 'iso2');
    }

    public function stateOfOrigin(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_of_origin', 'state_code');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

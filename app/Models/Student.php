<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['admission_number', 'blood_group', 'city_of_residence', 'classroom_id', 'country_of_residence', 'dob', 'emergency_contact', 'guardian_id', 'lga_of_origin', 'nationality', 'religion', 'rhesus_factor', 'state_of_residence', 'state_of_origin', 'status', 'type', 'graduated_at'])]
class Student extends Model
{
    use SoftDeletes;

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

    public function countryOfOrigin(): BelongsTo
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

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}

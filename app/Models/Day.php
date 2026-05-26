<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sushi\Sushi;

class Day extends Model
{
    use Sushi;

    protected $keyType = 'string';

    protected $rows = [
        ['id' => self::SUNDAY, 'name' => 'sunday'],
        ['id' => self::MONDAY, 'name' => 'monday'],
        ['id' => self::TUESDAY, 'name' => 'tuesday'],
        ['id' => self::WEDNESDAY, 'name' => 'wednesday'],
        ['id' => self::THURSDAY, 'name' => 'thursday'],
        ['id' => self::FRIDAY, 'name' => 'friday'],
        ['id' => self::SATURDAY, 'name' => 'saturday'],
    ];

    const SUNDAY = 1;

    const MONDAY = 2;

    const TUESDAY = 3;

    const WEDNESDAY = 4;

    const THURSDAY = 4;

    const FRIDAY = 4;

    const SATURDAY = 4;

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }
}

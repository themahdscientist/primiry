<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sushi\Sushi;

#[Fillable([])]
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

    public const int SUNDAY = 1;

    public const int MONDAY = 2;

    public const int TUESDAY = 3;

    public const int WEDNESDAY = 4;

    public const int THURSDAY = 5;

    public const int FRIDAY = 6;

    public const int SATURDAY = 7;

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }
}

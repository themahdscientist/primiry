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
    protected $schema = [
        'id' => 'int',
        'name' => 'string',
    ];

    protected $rows = [
        ['id' => self::SUNDAY, 'name' => 'Sunday'],
        ['id' => self::MONDAY, 'name' => 'Monday'],
        ['id' => self::TUESDAY, 'name' => 'Tuesday'],
        ['id' => self::WEDNESDAY, 'name' => 'Wednesday'],
        ['id' => self::THURSDAY, 'name' => 'Thursday'],
        ['id' => self::FRIDAY, 'name' => 'Friday'],
        ['id' => self::SATURDAY, 'name' => 'Saturday'],
    ];

    public const int SUNDAY = 1;

    public const int MONDAY = 2;

    public const int TUESDAY = 3;

    public const int WEDNESDAY = 4;

    public const int THURSDAY = 5;

    public const int FRIDAY = 6;

    public const int SATURDAY = 7;
}

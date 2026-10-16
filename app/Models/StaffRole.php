<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sushi\Sushi;

#[Fillable([])]
class StaffRole extends Model
{
    use Sushi;

    protected $rows = [
        [
            'id' => self::TEACHING,
            'name' => 'teaching',
            'description' => 'Teaching staff',
        ],
        [
            'id' => self::NON_TEACHING,
            'name' => 'non-teaching',
            'description' => 'Non-teaching staff',
        ],
        [
            'id' => self::ADMINISTRATIVE,
            'name' => 'administrative',
            'description' => 'Administrative',
        ],
    ];

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public const int TEACHING = 1;

    public const int NON_TEACHING = 2;

    public const int ADMINISTRATIVE = 3;
}

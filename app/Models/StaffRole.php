<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sushi\Sushi;

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

    const TEACHING = 1;

    const NON_TEACHING = 2;

    const ADMINISTRATIVE = 3;
}

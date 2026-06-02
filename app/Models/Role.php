<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sushi\Sushi;

class Role extends Model
{
    use Sushi;

    protected $rows = [
        [
            'id' => self::ADMIN,
            'name' => 'admin',
            'description' => 'Administrator',
        ],
        [
            'id' => self::STUDENT,
            'name' => 'student',
            'description' => 'Student/Pupil',
        ],
        [
            'id' => self::STAFF,
            'name' => 'staff',
            'description' => 'Teaching/Non-teaching/Administrative',
        ],
        [
            'id' => self::GUARDIAN,
            'name' => 'guardian',
            'description' => 'Guardian',
        ],
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public const int ADMIN = 1;

    public const int STUDENT = 2;

    public const int STAFF = 3;

    public const int GUARDIAN = 4;
}

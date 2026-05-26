<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BloodGroup: string implements HasLabel
{
    case A = 'a';
    case B = 'b';
    case AB = 'ab';
    case O = 'o';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::A => 'A',
            self::B => 'B',
            self::AB => 'AB',
            self::O => 'O',
        };
    }
}

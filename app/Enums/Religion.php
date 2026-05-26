<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Religion: string implements HasLabel
{
    case Christianity = 'christianity';
    case Islam = 'islam';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Christianity => 'Christianity',
            self::Islam => 'Islam',
            self::Other => 'Other',
        };
    }
}

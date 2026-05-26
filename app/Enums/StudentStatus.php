<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StudentStatus: string implements HasLabel
{
    case Active = 'active';
    case Suspended = 'suspended';
    case Expelled = 'expelled';
    case Graduated = 'graduated';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Suspended => 'Suspended',
            self::Expelled => 'Expelled',
            self::Graduated => 'Graduated',
        };
    }
}

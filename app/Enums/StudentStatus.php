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

    public function getColor(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Suspended => 'warning',
            self::Expelled => 'danger',
            self::Graduated => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Active => 'heroicon-s-check-circle',
            self::Suspended => 'heroicon-s-pause-circle',
            self::Expelled => 'heroicon-s-x-circle',
            self::Graduated => 'heroicon-s-academic-cap',
        };
    }
}

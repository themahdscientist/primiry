<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RhesusFactor: string implements HasLabel
{
    case RhPositive = 'rh+';
    case RhNegative = 'rh-';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::RhPositive => 'Rh-positive',
            self::RhNegative => 'Rh-negative',
        };
    }
}

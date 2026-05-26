<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StudentType: string implements HasLabel
{
    case Day = 'day';
    case Boarding = 'boarding';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Day => 'Day',
            self::Boarding => 'Boarding',
        };
    }
}

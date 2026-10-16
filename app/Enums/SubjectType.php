<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SubjectType: string implements HasLabel
{
    case Theory = 'theory';
    case Practical = 'practical';
    case Viva = 'viva';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Theory => 'Theory',
            self::Practical => 'Practical',
            self::Viva => 'Viva (oral)',
        };
    }
}

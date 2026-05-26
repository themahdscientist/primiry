<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EmploymentType: string implements HasLabel
{
    case FullTime = 'full-time';
    case PartTime = 'part-time';
    case Contractual = 'contractual';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FullTime => 'Full time',
            self::PartTime => 'Part time',
            self::Contractual => 'Contractual',
        };
    }
}

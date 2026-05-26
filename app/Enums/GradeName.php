<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum GradeName: string implements HasLabel
{
    case Kindergarten = 'Kindergarten';
    case G1st = '1st Grade';
    case G2nd = '2nd Grade';
    case G3rd = '3rd Grade';
    case G4th = '4th Grade';
    case G5th = '5th Grade';
    case G6th = '6th Grade';
    case G7th = '7th Grade';
    case G8th = '8th Grade';
    case G9th = '9th Grade';
    case G10th = '10th Grade';
    case G11th = '11th Grade';
    case G12th = '12th Grade';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Kindergarten => 'Kindergarten',
            self::G1st => '1st Grade',
            self::G2nd => '2nd Grade',
            self::G3rd => '3rd Grade',
            self::G4th => '4th Grade',
            self::G5th => '5th Grade',
            self::G6th => '6th Grade',
            self::G7th => '7th Grade',
            self::G8th => '8th Grade',
            self::G9th => '9th Grade',
            self::G10th => '10th Grade',
            self::G11th => '11th Grade',
            self::G12th => '12th Grade',
        };
    }
}

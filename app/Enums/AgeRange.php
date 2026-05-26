<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AgeRange: string implements HasLabel
{
    case Kindergarten = '5-6';
    case G1st = '6-7';
    case G2nd = '7-8';
    case G3rd = '8-9';
    case G4th = '9-10';
    case G5th = '10-11';
    case G6th = '11-12';
    case G7th = '12-13';
    case G8th = '13-14';
    case G9th = '14-15';
    case G10th = '15-16';
    case G11th = '16-17';
    case G12th = '17-18';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Kindergarten => 'Kindergarten: 5-6',
            self::G1st => '1st Grade: 6-7',
            self::G2nd => '2nd Grade: 7-8',
            self::G3rd => '3rd Grade: 8-9',
            self::G4th => '4th Grade: 9-10',
            self::G5th => '5th Grade: 10-11',
            self::G6th => '6th Grade: 11-12',
            self::G7th => '7th Grade: 12-13',
            self::G8th => '8th Grade: 13-14',
            self::G9th => '9th Grade: 14-15',
            self::G10th => '10th Grade: 15-16',
            self::G11th => '11th Grade: 16-17',
            self::G12th => '12th Grade: 17-18',
        };
    }
}

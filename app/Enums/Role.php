<?php

namespace App\Enums;

enum Role: int
{
    case RA = 1;
    case EA = 2;
    case VOTER = 3;

    public function label(): string
    {
        return match ($this) {
            self::RA => 'Registration authority',
            self::EA => 'Election authority',
            self::VOTER => 'Voter',
        };
    }
}

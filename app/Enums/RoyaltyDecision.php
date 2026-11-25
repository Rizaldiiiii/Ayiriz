<?php

namespace App\Enums;

enum RoyaltyDecision: string
{
    case Tetap = 'tetap';
    case Naik = 'naik';

    public function label(): string
    {
        return match ($this) {
            self::Tetap => 'Tetap',
            self::Naik => 'Naik',
        };
    }
}

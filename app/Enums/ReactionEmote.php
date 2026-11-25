<?php

namespace App\Enums;

enum ReactionEmote: string
{
    case Enak = 'enak';
    case Pedas = 'pedas';
    case Manis = 'manis';

    public function emoji(): string
    {
        return match ($this) {
            self::Enak => '😋',
            self::Pedas => '🥵',
            self::Manis => '🫠',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Enak => 'Enak',
            self::Pedas => 'Pedas',
            self::Manis => 'Manis / menyejukkan',
        };
    }

    public function fullLabel(): string
    {
        return $this->emoji() . ' ' . $this->label();
    }
}

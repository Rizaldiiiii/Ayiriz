<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Diproses = 'diproses';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Diproses => 'Diproses',
            self::Selesai => 'Selesai',
        };
    }
}

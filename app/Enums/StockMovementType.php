<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Masuk = 'masuk';
    case Keluar = 'keluar';
    case Penyesuaian = 'penyesuaian';

    public function label(): string
    {
        return match ($this) {
            self::Masuk => 'Stok Masuk',
            self::Keluar => 'Stok Keluar',
            self::Penyesuaian => 'Penyesuaian',
        };
    }
}

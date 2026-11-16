<?php

namespace App\Enums;

enum OutletStatus: string
{
    case Terverifikasi = 'terverifikasi';
    case BelumTerverifikasi = 'belum_terverifikasi';
    case Nonaktif = 'nonaktif';

    public function label(): string
    {
        return match ($this) {
            self::Terverifikasi => 'Aktif (Terverifikasi)',
            self::BelumTerverifikasi => 'Belum Terverifikasi',
            self::Nonaktif => 'Nonaktif',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Terverifikasi => 'Aktif',
            self::BelumTerverifikasi => 'Menunggu Verifikasi',
            self::Nonaktif => 'Nonaktif',
        };
    }
}

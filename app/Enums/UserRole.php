<?php

namespace App\Enums;

enum UserRole: string
{
    case Pusat = 'pusat';
    case Outlet = 'outlet';

    public function label(): string
    {
        return match ($this) {
            self::Pusat => 'Pusat Ayiriz',
            self::Outlet => 'Admin Outlet',
        };
    }
}

<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Outlet;

class RoyaltyService
{
    /**
     * Hitung nominal royalti dari total pesanan dan persen royalti outlet.
     */
    public function calculateNominal(int $total, float|string $royaltyPercent): int
    {
        $percent = (float) $royaltyPercent;

        return (int) round($total * ($percent / 100));
    }

    /**
     * Ringkasan penjualan, royalti, pesanan, dan daftar tunggu (antrean) satu outlet (penjualan & royalti hanya dari pesanan Selesai).
     *
     * @return array{total_penjualan: int, total_royalti: int, jumlah_selesai: int, jumlah_diproses: int, jumlah_antrean: int}
     */
    public function summarizeOutlet(Outlet $outlet): array
    {
        $selesaiQuery = $outlet->orders()->where('status', OrderStatus::Selesai);
        $diprosesQuery = $outlet->orders()->where('status', OrderStatus::Diproses);
        $antreanQuery = $outlet->antreans();

        return [
            'total_penjualan' => (int) (clone $selesaiQuery)->sum('total'),
            'total_royalti' => (int) (clone $selesaiQuery)->sum('royalty_nominal'),
            'jumlah_selesai' => (int) (clone $selesaiQuery)->count(),
            'jumlah_diproses' => (int) $diprosesQuery->count(),
            'jumlah_antrean' => (int) $antreanQuery->count(),
        ];
    }
}

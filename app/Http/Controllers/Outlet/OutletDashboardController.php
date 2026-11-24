<?php

namespace App\Http\Controllers\Outlet;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Antrean;
use App\Models\Order;
use App\Services\RoyaltyService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OutletDashboardController extends Controller
{
    public function __construct(
        private readonly RoyaltyService $royaltyService
    ) {}

    public function index(Request $request): View
    {
        $outlet = $request->user()->outlet;
        abort_if(! $outlet, 403, 'Akun outlet Anda belum terhubung dengan data kantin.');

        $summary = $this->royaltyService->summarizeOutlet($outlet);

        $antreans = Antrean::with('items')
            ->where('outlet_id', $outlet->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $pesananDiproses = Order::with('items')
            ->where('outlet_id', $outlet->id)
            ->where('status', OrderStatus::Diproses)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $menuHabisCount = $outlet->menus()
            ->where(function ($q): void {
                $q->where('stok_porsi', '<=', 0)
                    ->orWhere('tersedia', false);
            })
            ->count();

        return view('outlet.dashboard', [
            'outlet' => $outlet,
            'summary' => $summary,
            'antreans' => $antreans,
            'pesananDiproses' => $pesananDiproses,
            'menuHabisCount' => $menuHabisCount,
        ]);
    }

    public function stubStok(): View
    {
        return view('stubs.tahap-berikutnya', [
            'kodeFitur' => 'KF-06',
            'namaFitur' => 'Manajemen Stok Bahan Baku & Resep',
            'tahap' => 'Tahap 2 — Operasional Outlet',
            'deskripsi' => 'Pencatatan stok bahan baku (masuk/keluar manual, batas minimum stok menipis, dan resep takaran per menu) belum tersedia di tahap ini dan akan dibangun pada Tahap 2. Stok porsi harian menu sudah aktif di halaman Menu Outlet.',
            'backUrl' => route('outlet.dashboard'),
            'backLabel' => 'Kembali ke Dashboard Outlet',
        ]);
    }

    public function stubLaporan(): View
    {
        return view('stubs.tahap-berikutnya', [
            'kodeFitur' => 'KF-09',
            'namaFitur' => 'Laporan Penjualan & Reaksi Outlet',
            'tahap' => 'Tahap 2 — Operasional Outlet',
            'deskripsi' => 'Laporan penjualan harian/mingguan/bulanan, menu terlaris di outlet, dan rekap hitungan reaksi per menu belum tersedia di tahap ini dan akan dibangun pada Tahap 2.',
            'backUrl' => route('outlet.dashboard'),
            'backLabel' => 'Kembali ke Dashboard Outlet',
        ]);
    }
}

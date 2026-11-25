<?php

namespace App\Http\Controllers\Outlet;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OutletReportController extends Controller
{
    public function index(Request $request): View
    {
        $outlet = $request->user()->outlet;
        abort_if(! $outlet, 403, 'Akun outlet Anda belum terhubung dengan data kantin.');

        $periode = (string) $request->query('periode', 'bulanan');
        if (! in_array($periode, ['harian', 'mingguan', 'bulanan', 'semua'], true)) {
            $periode = 'bulanan';
        }

        // Rekap 3 periode utama (Harian, Mingguan, Bulanan) untuk outlet ini (hanya pesanan Selesai)
        $baseSelesai = fn () => Order::where('outlet_id', $outlet->id)
            ->where('status', OrderStatus::Selesai);

        $harianQuery = ($baseSelesai)()->whereDate('created_at', now()->toDateString());
        $mingguanQuery = ($baseSelesai)()->where('created_at', '>=', now()->subDays(7)->startOfDay());
        $bulananQuery = ($baseSelesai)()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month);

        $rekapPeriode = [
            'harian' => [
                'label' => 'Hari Ini (' . now()->translatedFormat('d M Y') . ')',
                'penjualan' => (int) (clone $harianQuery)->sum('total'),
                'royalti' => (int) (clone $harianQuery)->sum('royalty_nominal'),
                'pesanan' => (int) (clone $harianQuery)->count(),
            ],
            'mingguan' => [
                'label' => '7 Hari Terakhir',
                'penjualan' => (int) (clone $mingguanQuery)->sum('total'),
                'royalti' => (int) (clone $mingguanQuery)->sum('royalty_nominal'),
                'pesanan' => (int) (clone $mingguanQuery)->count(),
            ],
            'bulanan' => [
                'label' => 'Bulan Ini (' . now()->translatedFormat('F Y') . ')',
                'penjualan' => (int) (clone $bulananQuery)->sum('total'),
                'royalti' => (int) (clone $bulananQuery)->sum('royalty_nominal'),
                'pesanan' => (int) (clone $bulananQuery)->count(),
            ],
        ];

        // Query periode aktif yang dipilih pengguna
        $filteredOrdersQuery = ($baseSelesai)();
        if ($periode === 'harian') {
            $filteredOrdersQuery->whereDate('created_at', now()->toDateString());
        } elseif ($periode === 'mingguan') {
            $filteredOrdersQuery->where('created_at', '>=', now()->subDays(7)->startOfDay());
        } elseif ($periode === 'bulanan') {
            $filteredOrdersQuery->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month);
        }

        $totalPenjualan = (int) (clone $filteredOrdersQuery)->sum('total');
        $totalRoyalti = (int) (clone $filteredOrdersQuery)->sum('royalty_nominal');
        $jumlahSelesai = (int) (clone $filteredOrdersQuery)->count();

        $selectedSummary = [
            'total_penjualan' => $totalPenjualan,
            'total_royalti' => $totalRoyalti,
            'pendapatan_bersih' => max(0, $totalPenjualan - $totalRoyalti),
            'jumlah_selesai' => $jumlahSelesai,
        ];

        // Menu terlaris di outlet ini sendiri (hanya dari pesanan Selesai milik outlet ini)
        $menuTerlaris = OrderItem::query()
            ->selectRaw('menu_id, nama_menu_saat_submit, SUM(jumlah) as total_porsi, SUM(subtotal) as total_omzet')
            ->whereHas('order', function (Builder $q) use ($outlet, $periode): void {
                $q->where('outlet_id', $outlet->id)
                    ->where('status', OrderStatus::Selesai);

                if ($periode === 'harian') {
                    $q->whereDate('created_at', now()->toDateString());
                } elseif ($periode === 'mingguan') {
                    $q->where('created_at', '>=', now()->subDays(7)->startOfDay());
                } elseif ($periode === 'bulanan') {
                    $q->whereYear('created_at', now()->year)
                        ->whereMonth('created_at', now()->month);
                }
            })
            ->groupBy('menu_id', 'nama_menu_saat_submit')
            ->orderByDesc('total_porsi')
            ->orderByDesc('total_omzet')
            ->get();

        // Stok bahan menipis di outlet ini
        $stokMenipis = Ingredient::where('outlet_id', $outlet->id)
            ->menipis()
            ->orderBy('nama')
            ->get();

        // Hitungan reaksi per menu di outlet ini (GROUP BY emote via withCount, tanpa skor/rata-rata/ranking)
        $menusWithReactions = Menu::with('category')
            ->where('outlet_id', $outlet->id)
            ->withCount([
                'reactions as enak_count' => fn ($q) => $q->where('emote', 'enak'),
                'reactions as pedas_count' => fn ($q) => $q->where('emote', 'pedas'),
                'reactions as manis_count' => fn ($q) => $q->where('emote', 'manis'),
            ])
            ->orderBy('nama')
            ->get();

        return view('outlet.report-index', [
            'outlet' => $outlet,
            'periode' => $periode,
            'rekapPeriode' => $rekapPeriode,
            'selectedSummary' => $selectedSummary,
            'menuTerlaris' => $menuTerlaris,
            'stokMenipis' => $stokMenipis,
            'menusWithReactions' => $menusWithReactions,
        ]);
    }
}

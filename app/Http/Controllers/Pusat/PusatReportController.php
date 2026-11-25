<?php

namespace App\Http\Controllers\Pusat;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Services\RoyaltyService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PusatReportController extends Controller
{
    public function __construct(
        private readonly RoyaltyService $royaltyService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Outlet::class);

        $periode = (string) $request->query('periode', 'semester');
        if (! in_array($periode, ['harian', 'mingguan', 'bulanan', 'semester', 'semua'], true)) {
            $periode = 'semester';
        }

        $availableSemesters = $this->royaltyService->availableSemesters();
        $currentSemester = $this->royaltyService->currentSemesterLabel();
        $selectedSemester = (string) $request->query('semester', $currentSemester);
        if (trim($selectedSemester) === '') {
            $selectedSemester = $currentSemester;
        }

        // Rekap 4 periode utama gabungan semua outlet (hanya pesanan Selesai)
        $baseSelesai = fn () => Order::where('status', OrderStatus::Selesai);

        $harianQuery = $this->royaltyService->applyPeriodFilter(($baseSelesai)(), 'harian');
        $mingguanQuery = $this->royaltyService->applyPeriodFilter(($baseSelesai)(), 'mingguan');
        $bulananQuery = $this->royaltyService->applyPeriodFilter(($baseSelesai)(), 'bulanan');
        $semesterQuery = $this->royaltyService->applyPeriodFilter(($baseSelesai)(), 'semester', $selectedSemester);

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
            'semester' => [
                'label' => 'Semester ' . $selectedSemester,
                'penjualan' => (int) (clone $semesterQuery)->sum('total'),
                'royalti' => (int) (clone $semesterQuery)->sum('royalty_nominal'),
                'pesanan' => (int) (clone $semesterQuery)->count(),
            ],
        ];

        // Query periode aktif yang dipilih Pusat
        $activeOrdersQuery = $this->royaltyService->applyPeriodFilter(($baseSelesai)(), $periode, $selectedSemester);

        $totalPenjualanGabungan = (int) (clone $activeOrdersQuery)->sum('total');
        $totalRoyaltiGabungan = (int) (clone $activeOrdersQuery)->sum('royalty_nominal');
        $jumlahPesananSelesai = (int) (clone $activeOrdersQuery)->count();

        // Penjualan & royalti per outlet pada periode terpilih (HANYA pesanan Selesai)
        // Perbandingan penjualan antar outlet diperbolehkan untuk audit bisnis oleh Pusat (PRD KF-08)
        $outlets = Outlet::with('user')
            ->withCount([
                'orders as orders_selesai_count' => fn (Builder $q) => $this->royaltyService
                    ->applyPeriodFilter($q->where('status', OrderStatus::Selesai), $periode, $selectedSemester),
                'orders as orders_diproses_count' => fn (Builder $q) => $this->royaltyService
                    ->applyPeriodFilter($q->where('status', OrderStatus::Diproses), $periode, $selectedSemester),
                'menus',
            ])
            ->withSum(
                [
                    'orders as period_penjualan' => fn (Builder $q) => $this->royaltyService
                        ->applyPeriodFilter($q->where('status', OrderStatus::Selesai), $periode, $selectedSemester),
                ],
                'total'
            )
            ->withSum(
                [
                    'orders as period_royalti' => fn (Builder $q) => $this->royaltyService
                        ->applyPeriodFilter($q->where('status', OrderStatus::Selesai), $periode, $selectedSemester),
                ],
                'royalty_nominal'
            )
            ->get()
            ->sortByDesc(fn (Outlet $o) => (int) $o->period_penjualan)
            ->values();

        // Menu terlaris PER OUTLET (bukan digabung/dibandingkan antar outlet — PRD KF-08)
        // Tanpa menampilkan atau mengurutkan berdasarkan reaksi pelanggan (KF-07)
        $topMenusByOutlet = [];
        foreach ($outlets as $outlet) {
            $topMenusByOutlet[$outlet->id] = OrderItem::query()
                ->selectRaw('menu_id, nama_menu_saat_submit, SUM(jumlah) as total_porsi, SUM(subtotal) as total_omzet')
                ->whereHas('order', function (Builder $q) use ($outlet, $periode, $selectedSemester): void {
                    $q->where('outlet_id', $outlet->id)
                        ->where('status', OrderStatus::Selesai);

                    $this->royaltyService->applyPeriodFilter($q, $periode, $selectedSemester);
                })
                ->groupBy('menu_id', 'nama_menu_saat_submit')
                ->orderByDesc('total_porsi')
                ->orderByDesc('total_omzet')
                ->limit(5)
                ->get();
        }

        return view('pusat.report-index', [
            'periode' => $periode,
            'selectedSemester' => $selectedSemester,
            'availableSemesters' => $availableSemesters,
            'rekapPeriode' => $rekapPeriode,
            'summary' => [
                'total_penjualan' => $totalPenjualanGabungan,
                'total_royalti' => $totalRoyaltiGabungan,
                'jumlah_selesai' => $jumlahPesananSelesai,
                'total_outlet' => $outlets->count(),
            ],
            'outlets' => $outlets,
            'topMenusByOutlet' => $topMenusByOutlet,
        ]);
    }
}

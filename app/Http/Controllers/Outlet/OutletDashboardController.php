<?php

namespace App\Http\Controllers\Outlet;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Antrean;
use App\Models\Ingredient;
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

        $stokMenipis = Ingredient::where('outlet_id', $outlet->id)
            ->menipis()
            ->orderBy('nama')
            ->get();

        return view('outlet.dashboard', [
            'outlet' => $outlet,
            'summary' => $summary,
            'antreans' => $antreans,
            'pesananDiproses' => $pesananDiproses,
            'menuHabisCount' => $menuHabisCount,
            'stokMenipis' => $stokMenipis,
        ]);
    }
}

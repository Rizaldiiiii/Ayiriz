<?php

namespace App\Http\Controllers\Outlet;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Antrean;
use App\Models\Order;
use App\Services\AntreanService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierOrderController extends Controller
{
    public function __construct(
        private readonly AntreanService $antreanService,
        private readonly OrderService $orderService
    ) {}

    public function index(Request $request): View
    {
        $outlet = $request->user()->outlet;
        abort_if(! $outlet, 403, 'Akun outlet Anda belum terhubung dengan data kantin.');

        $statusFilter = $request->query('status');

        $antreans = Antrean::with('items')
            ->where('outlet_id', $outlet->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $ordersQuery = Order::with('items')
            ->where('outlet_id', $outlet->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($statusFilter && in_array($statusFilter, [OrderStatus::Diproses->value, OrderStatus::Selesai->value], true)) {
            $ordersQuery->where('status', $statusFilter);
        }

        $orders = $ordersQuery->paginate(12)->withQueryString();

        $counts = [
            'antrean' => $antreans->count(),
            'semua' => Order::where('outlet_id', $outlet->id)->count(),
            'diproses' => Order::where('outlet_id', $outlet->id)->where('status', OrderStatus::Diproses)->count(),
            'selesai' => Order::where('outlet_id', $outlet->id)->where('status', OrderStatus::Selesai)->count(),
        ];

        return view('outlet.orders-index', [
            'outlet' => $outlet,
            'antreans' => $antreans,
            'orders' => $orders,
            'statusFilter' => $statusFilter,
            'counts' => $counts,
        ]);
    }

    public function showAntrean(Request $request, Antrean $antrean): View
    {
        $this->authorize('view', $antrean);

        $outlet = $request->user()->outlet;
        $ownedAntrean = Antrean::with(['outlet', 'items.menu'])
            ->where('outlet_id', $outlet->id)
            ->findOrFail($antrean->id);

        return view('outlet.antrean-show', [
            'outlet' => $outlet,
            'antrean' => $ownedAntrean,
        ]);
    }

    public function confirmPayment(Request $request, Antrean $antrean): RedirectResponse
    {
        $this->authorize('confirmPayment', $antrean);

        $outlet = $request->user()->outlet;
        $ownedAntrean = Antrean::where('outlet_id', $outlet->id)->findOrFail($antrean->id);

        $order = $this->antreanService->confirmCashPayment($ownedAntrean, $request->user());

        return redirect()->route('outlet.orders.show', $order)
            ->with(
                'toast_success',
                "Pembayaran tunai diterima. Antrean {$order->kode} ({$order->nama_pemesan}) kini berstatus Diproses dan stok porsi telah dikurangi."
            );
    }

    public function destroyAntrean(Request $request, Antrean $antrean): RedirectResponse
    {
        $this->authorize('delete', $antrean);

        $outlet = $request->user()->outlet;
        $ownedAntrean = Antrean::where('outlet_id', $outlet->id)->findOrFail($antrean->id);
        $kode = $ownedAntrean->kode;
        $nama = $ownedAntrean->nama_pemesan;

        $this->antreanService->deleteAntrean($ownedAntrean, $request->user());

        return redirect()->route('outlet.orders.index')
            ->with(
                'toast_info',
                "Antrean {$kode} ({$nama} — pelanggan tidak datang) berhasil dihapus dari daftar tunggu tanpa perubahan stok maupun royalti."
            );
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorize('view', $order);

        $outlet = $request->user()->outlet;
        $ownedOrder = Order::with(['outlet', 'items'])
            ->where('outlet_id', $outlet->id)
            ->findOrFail($order->id);

        return view('outlet.orders-show', [
            'outlet' => $outlet,
            'order' => $ownedOrder,
        ]);
    }

    public function complete(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('complete', $order);

        $outlet = $request->user()->outlet;
        $ownedOrder = Order::where('outlet_id', $outlet->id)->findOrFail($order->id);

        $completed = $this->orderService->completeOrder($ownedOrder, $request->user());

        return redirect()->route('outlet.orders.show', $completed)
            ->with('toast_success', 'Pesanan ditandai Selesai');
    }
}

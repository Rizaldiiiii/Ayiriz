<?php

namespace App\Http\Controllers\Outlet;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierOrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    public function index(Request $request): View
    {
        $outlet = $request->user()->outlet;
        abort_if(! $outlet, 403, 'Akun outlet Anda belum terhubung dengan data kantin.');

        $statusFilter = $request->query('status');

        $ordersQuery = Order::with(['user', 'items'])
            ->where('outlet_id', $outlet->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($statusFilter && in_array($statusFilter, [OrderStatus::Diproses->value, OrderStatus::Selesai->value], true)) {
            $ordersQuery->where('status', $statusFilter);
        }

        $orders = $ordersQuery->paginate(12)->withQueryString();

        $counts = [
            'semua' => Order::where('outlet_id', $outlet->id)->count(),
            'diproses' => Order::where('outlet_id', $outlet->id)->where('status', OrderStatus::Diproses)->count(),
            'selesai' => Order::where('outlet_id', $outlet->id)->where('status', OrderStatus::Selesai)->count(),
        ];

        return view('outlet.orders-index', [
            'outlet' => $outlet,
            'orders' => $orders,
            'statusFilter' => $statusFilter,
            'counts' => $counts,
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorize('view', $order);

        $outlet = $request->user()->outlet;
        $ownedOrder = Order::with(['user', 'outlet', 'items'])
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

    public function destroy(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('delete', $order);

        $outlet = $request->user()->outlet;
        $ownedOrder = Order::where('outlet_id', $outlet->id)->findOrFail($order->id);
        $kode = $ownedOrder->kode;

        $this->orderService->deleteDiprosesOrder($ownedOrder, $request->user());

        return redirect()->route('outlet.orders.index')
            ->with(
                'toast_info',
                "Pesanan {$kode} (pelanggan tidak datang) berhasil dihapus dan stok porsi menu telah dikembalikan."
            );
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with(['outlet', 'items'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('pelanggan.orders-index', [
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, string $kode): View
    {
        $order = Order::with(['outlet', 'items.menu', 'user'])
            ->where('kode', $kode)
            ->firstOrFail();

        $this->authorize('view', $order);

        return view('pelanggan.orders-show', [
            'order' => $order,
        ]);
    }
}

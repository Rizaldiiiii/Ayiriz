<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\AddCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\Menu;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService
    ) {}

    public function index(): View
    {
        $cart = $this->cartService->getCartDetails();

        return view('pelanggan.cart', [
            'cart' => $cart,
        ]);
    }

    public function addItem(AddCartItemRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $menu = Menu::with('outlet')->findOrFail((int) $validated['menu_id']);
        $jumlah = (int) ($validated['jumlah'] ?? 1);
        $replaceOutlet = $request->boolean('replace_outlet');

        $result = $this->cartService->addItem($menu, $jumlah, $replaceOutlet);

        if ($result['conflict']) {
            return back()->with('cart_conflict', [
                'menu_id' => $menu->id,
                'menu_nama' => $menu->nama,
                'jumlah' => $jumlah,
                'current_outlet_nama' => $result['current_outlet']?->nama ?? 'Kantin sebelumnya',
                'new_outlet_nama' => $result['new_outlet']?->nama ?? $menu->outlet->nama,
            ]);
        }

        return back()->with(
            'toast_success',
            "\"{$menu->nama}\" ditambahkan ke keranjang ({$menu->outlet->nama})."
        );
    }

    public function updateItem(UpdateCartItemRequest $request, Menu $menu): RedirectResponse
    {
        $jumlah = (int) $request->validated()['jumlah'];
        $this->cartService->updateItemQuantity($menu, $jumlah);

        return redirect()->route('cart.index')->with(
            'toast_success',
            $jumlah > 0
                ? "Jumlah \"{$menu->nama}\" diperbarui menjadi {$jumlah} porsi."
                : "\"{$menu->nama}\" dihapus dari keranjang."
        );
    }

    public function removeItem(Menu $menu): RedirectResponse
    {
        $this->cartService->removeItem($menu->id);

        return redirect()->route('cart.index')
            ->with('toast_info', "\"{$menu->nama}\" dihapus dari keranjang.");
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clear();

        return redirect()->route('cart.index')
            ->with('toast_info', 'Keranjang telah dikosongkan.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $this->authorize('create', \App\Models\Order::class);

        $payload = $this->cartService->getCheckoutPayload();

        $order = $this->orderService->checkout($request->user(), $payload);
        $this->cartService->clear();

        return redirect()->route('pelanggan.orders.show', $order->kode)
            ->with(
                'toast_success',
                "Pesanan {$order->kode} berhasil dibuat! Silakan datang dan bayar di kasir {$order->outlet->nama}."
            );
    }
}

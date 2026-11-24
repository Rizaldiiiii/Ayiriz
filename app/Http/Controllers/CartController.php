<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\AddCartItemRequest;
use App\Http\Requests\Cart\SubmitAntreanRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\Menu;
use App\Services\AntreanService;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly AntreanService $antreanService
    ) {}

    public function index(): View
    {
        $cart = $this->cartService->getCartDetails();
        $lastAntrean = $this->cartService->getLastSubmittedAntrean();

        return view('pelanggan.cart', [
            'cart' => $cart,
            'lastAntrean' => $lastAntrean,
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

    public function submit(SubmitAntreanRequest $request): RedirectResponse
    {
        $namaPemesan = $request->validated()['nama_pemesan'];
        $payload = $this->cartService->getSubmitPayload();

        $antrean = $this->antreanService->submit($namaPemesan, $payload);

        $this->cartService->storeLastSubmittedAntrean([
            'kode' => $antrean->kode,
            'nama_pemesan' => $antrean->nama_pemesan,
            'outlet_nama' => $antrean->outlet->nama,
            'outlet_alamat' => $antrean->outlet->alamat,
            'outlet_jam_buka' => $antrean->outlet->jam_buka,
            'outlet_slug' => $antrean->outlet->slug,
            'total' => $antrean->total,
            'created_at' => $antrean->created_at?->translatedFormat('d M Y, H:i') . ' WITA',
            'items' => $antrean->items->map(fn ($item) => [
                'nama_menu_saat_submit' => $item->nama_menu_saat_submit,
                'harga_saat_submit' => $item->harga_saat_submit,
                'jumlah' => $item->jumlah,
                'subtotal' => $item->subtotal,
            ])->all(),
        ]);

        $this->cartService->clear();

        return redirect()->route('cart.index')
            ->with(
                'toast_success',
                "Antrean {$antrean->kode} atas nama {$antrean->nama_pemesan} berhasil masuk daftar tunggu! Silakan tunjukkan kode ini dan bayar tunai di kasir {$antrean->outlet->nama}."
            );
    }
}

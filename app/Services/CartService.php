<?php

namespace App\Services;

use App\Enums\OutletStatus;
use App\Models\Menu;
use App\Models\Outlet;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CartService
{
    private const SESSION_KEY = 'ayiriz_cart';

    /**
     * Ambil isi keranjang dari session beserta objek Outlet dan Menu terkini.
     *
     * @return array{outlet: Outlet|null, items: array<int, array{menu: Menu, jumlah: int, subtotal: int}>, total: int, total_item: int}
     */
    public function getCartDetails(): array
    {
        $raw = Session::get(self::SESSION_KEY, [
            'outlet_id' => null,
            'items' => [],
        ]);

        $outletId = $raw['outlet_id'] ?? null;
        $rawItems = $raw['items'] ?? [];

        if (! $outletId || empty($rawItems)) {
            return [
                'outlet' => null,
                'items' => [],
                'total' => 0,
                'total_item' => 0,
            ];
        }

        $outlet = Outlet::find($outletId);
        if (! $outlet || $outlet->status !== OutletStatus::Terverifikasi) {
            $this->clear();

            return [
                'outlet' => null,
                'items' => [],
                'total' => 0,
                'total_item' => 0,
            ];
        }

        $menus = Menu::where('outlet_id', $outlet->id)
            ->whereIn('id', array_keys($rawItems))
            ->get()
            ->keyBy('id');

        $items = [];
        $total = 0;
        $totalItem = 0;

        foreach ($rawItems as $menuId => $jumlah) {
            $menu = $menus->get((int) $menuId);
            if (! $menu) {
                continue;
            }

            $qty = max(1, (int) $jumlah);
            $subtotal = $menu->harga * $qty;

            $items[] = [
                'menu' => $menu,
                'jumlah' => $qty,
                'subtotal' => $subtotal,
            ];

            $total += $subtotal;
            $totalItem += $qty;
        }

        if (empty($items)) {
            $this->clear();

            return [
                'outlet' => null,
                'items' => [],
                'total' => 0,
                'total_item' => 0,
            ];
        }

        return [
            'outlet' => $outlet,
            'items' => $items,
            'total' => $total,
            'total_item' => $totalItem,
        ];
    }

    /**
     * Tambahkan menu ke keranjang satu outlet.
     *
     * @return array{conflict: bool, current_outlet: Outlet|null, new_outlet: Outlet|null}
     */
    public function addItem(Menu $menu, int $jumlah = 1, bool $replaceOutlet = false): array
    {
        $menu->loadMissing('outlet');

        if (! $menu->outlet || $menu->outlet->status !== OutletStatus::Terverifikasi) {
            throw ValidationException::withMessages([
                'menu_id' => 'Outlet dari menu ini sedang tidak aktif atau belum terverifikasi.',
            ]);
        }

        if (! $menu->tersedia || $menu->stok_porsi <= 0) {
            throw ValidationException::withMessages([
                'menu_id' => "Menu \"{$menu->nama}\" sedang habis atau tidak tersedia.",
            ]);
        }

        $raw = Session::get(self::SESSION_KEY, [
            'outlet_id' => null,
            'items' => [],
        ]);

        $currentOutletId = $raw['outlet_id'] ?? null;
        $currentItems = $raw['items'] ?? [];

        if ($currentOutletId && ! empty($currentItems) && (int) $currentOutletId !== (int) $menu->outlet_id) {
            if (! $replaceOutlet) {
                $currentOutlet = Outlet::find($currentOutletId);

                return [
                    'conflict' => true,
                    'current_outlet' => $currentOutlet,
                    'new_outlet' => $menu->outlet,
                ];
            }

            $currentItems = [];
        }

        $existingQty = (int) ($currentItems[$menu->id] ?? 0);
        $newQty = $existingQty + max(1, $jumlah);

        if ($newQty > $menu->stok_porsi) {
            throw ValidationException::withMessages([
                'jumlah' => "Stok porsi \"{$menu->nama}\" tersisa {$menu->stok_porsi} porsi.",
            ]);
        }

        $currentItems[$menu->id] = $newQty;

        Session::put(self::SESSION_KEY, [
            'outlet_id' => $menu->outlet_id,
            'items' => $currentItems,
        ]);

        return [
            'conflict' => false,
            'current_outlet' => $menu->outlet,
            'new_outlet' => $menu->outlet,
        ];
    }

    /**
     * Ubah jumlah item di keranjang (0 = hapus item).
     */
    public function updateItemQuantity(Menu $menu, int $jumlah): void
    {
        $raw = Session::get(self::SESSION_KEY, [
            'outlet_id' => null,
            'items' => [],
        ]);

        $items = $raw['items'] ?? [];

        if ($jumlah <= 0) {
            unset($items[$menu->id]);
        } else {
            if (! $menu->tersedia || $jumlah > $menu->stok_porsi) {
                throw ValidationException::withMessages([
                    'jumlah' => "Stok porsi \"{$menu->nama}\" hanya tersedia {$menu->stok_porsi} porsi.",
                ]);
            }
            $items[$menu->id] = $jumlah;
        }

        if (empty($items)) {
            $this->clear();

            return;
        }

        Session::put(self::SESSION_KEY, [
            'outlet_id' => $raw['outlet_id'],
            'items' => $items,
        ]);
    }

    public function removeItem(int $menuId): void
    {
        $raw = Session::get(self::SESSION_KEY, [
            'outlet_id' => null,
            'items' => [],
        ]);

        $items = $raw['items'] ?? [];
        unset($items[$menuId]);

        if (empty($items)) {
            $this->clear();

            return;
        }

        Session::put(self::SESSION_KEY, [
            'outlet_id' => $raw['outlet_id'],
            'items' => $items,
        ]);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Format item keranjang untuk dikirim ke OrderService::checkout().
     *
     * @return array<int, array{menu_id: int, jumlah: int}>
     */
    public function getCheckoutPayload(): array
    {
        $raw = Session::get(self::SESSION_KEY, [
            'outlet_id' => null,
            'items' => [],
        ]);

        $payload = [];
        foreach (($raw['items'] ?? []) as $menuId => $jumlah) {
            if ((int) $jumlah > 0) {
                $payload[] = [
                    'menu_id' => (int) $menuId,
                    'jumlah' => (int) $jumlah,
                ];
            }
        }

        return $payload;
    }
}

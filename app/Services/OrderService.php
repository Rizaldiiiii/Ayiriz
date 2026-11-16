<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\OutletStatus;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private readonly RoyaltyService $royaltyService
    ) {}

    /**
     * Buat pesanan baru dari daftar item (satu pesanan = satu outlet, harga dikunci saat checkout).
     *
     * @param  array<int, array{menu_id: int, jumlah: int}>  $items
     */
    public function checkout(User $pelanggan, array $items): Order
    {
        if (empty($items)) {
            throw ValidationException::withMessages([
                'keranjang' => 'Keranjang pesanan masih kosong.',
            ]);
        }

        return DB::transaction(function () use ($pelanggan, $items): Order {
            $normalized = [];
            foreach ($items as $row) {
                $menuId = (int) ($row['menu_id'] ?? 0);
                $jumlah = (int) ($row['jumlah'] ?? 0);

                if ($menuId <= 0 || $jumlah <= 0) {
                    throw ValidationException::withMessages([
                        'keranjang' => 'Item dan jumlah pesanan tidak valid.',
                    ]);
                }

                $normalized[$menuId] = ($normalized[$menuId] ?? 0) + $jumlah;
            }

            $menus = Menu::with('outlet')
                ->whereIn('id', array_keys($normalized))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($menus->count() !== count($normalized)) {
                throw ValidationException::withMessages([
                    'keranjang' => 'Salah satu menu yang dipilih tidak ditemukan.',
                ]);
            }

            $outletIds = $menus->pluck('outlet_id')->unique()->values();
            if ($outletIds->count() !== 1) {
                throw ValidationException::withMessages([
                    'keranjang' => 'Satu pesanan hanya boleh berisi menu dari satu kantin/outlet.',
                ]);
            }

            /** @var Outlet|null $outlet */
            $outlet = $menus->first()->outlet;
            if (! $outlet || $outlet->status !== OutletStatus::Terverifikasi) {
                throw ValidationException::withMessages([
                    'keranjang' => 'Outlet ini belum terverifikasi atau sedang nonaktif sehingga tidak dapat menerima pesanan.',
                ]);
            }

            $total = 0;
            $orderItemsPayload = [];

            foreach ($normalized as $menuId => $jumlah) {
                /** @var Menu $menu */
                $menu = $menus->get($menuId);

                if (! $menu->tersedia) {
                    throw ValidationException::withMessages([
                        'keranjang' => "Menu \"{$menu->nama}\" sedang tidak tersedia.",
                    ]);
                }

                if ($menu->stok_porsi < $jumlah) {
                    throw ValidationException::withMessages([
                        'keranjang' => "Stok porsi untuk \"{$menu->nama}\" tidak mencukupi (tersisa {$menu->stok_porsi} porsi).",
                    ]);
                }

                $hargaCheckout = (int) $menu->harga;
                $subtotal = $hargaCheckout * $jumlah;
                $total += $subtotal;

                $orderItemsPayload[] = [
                    'menu' => $menu,
                    'nama_menu_saat_checkout' => $menu->nama,
                    'harga_saat_checkout' => $hargaCheckout,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal,
                ];
            }

            $kode = $this->generateOrderCode();

            $order = Order::create([
                'kode' => $kode,
                'user_id' => $pelanggan->id,
                'outlet_id' => $outlet->id,
                'status' => OrderStatus::Diproses,
                'total' => $total,
                'royalty_percent_saat_selesai' => null,
                'royalty_nominal' => 0,
                'metode_bayar' => 'bayar_di_kasir',
                'selesai_at' => null,
                'is_sample' => false,
            ]);

            foreach ($orderItemsPayload as $payload) {
                /** @var Menu $menu */
                $menu = $payload['menu'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'nama_menu_saat_checkout' => $payload['nama_menu_saat_checkout'],
                    'harga_saat_checkout' => $payload['harga_saat_checkout'],
                    'jumlah' => $payload['jumlah'],
                    'subtotal' => $payload['subtotal'],
                ]);

                $menu->decrement('stok_porsi', $payload['jumlah']);
            }

            return $order->load(['outlet', 'items', 'user']);
        });
    }

    /**
     * Terima pembayaran di kasir dan ubah status pesanan Diproses -> Selesai (satu arah).
     */
    public function completeOrder(Order $order, User $actor): Order
    {
        return DB::transaction(function () use ($order, $actor): Order {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::with(['outlet', 'items'])
                ->lockForUpdate()
                ->findOrFail($order->id);

            if (! $actor->isOutlet() || ! $actor->outlet || (int) $lockedOrder->outlet_id !== (int) $actor->outlet->id) {
                abort(403, 'Anda hanya dapat menyelesaikan pesanan milik outlet Anda sendiri.');
            }

            if ($lockedOrder->status !== OrderStatus::Diproses) {
                throw ValidationException::withMessages([
                    'status' => 'Pesanan yang sudah Selesai tidak dapat diubah kembali.',
                ]);
            }

            $royaltyPercent = (float) $lockedOrder->outlet->royalty_percent;
            $royaltyNominal = $this->royaltyService->calculateNominal($lockedOrder->total, $royaltyPercent);

            $lockedOrder->update([
                'status' => OrderStatus::Selesai,
                'royalty_percent_saat_selesai' => $royaltyPercent,
                'royalty_nominal' => $royaltyNominal,
                'metode_bayar' => 'bayar_di_kasir',
                'selesai_at' => now(),
            ]);

            return $lockedOrder->fresh(['outlet', 'items', 'user']);
        });
    }

    /**
     * Hapus pesanan yang masih Diproses (pelanggan tidak datang, makanan belum dimasak).
     * Stok porsi kembali, tanpa royalti dan tanpa pengurangan stok bahan.
     * Pesanan yang sudah Selesai TIDAK BISA dihapus.
     */
    public function deleteDiprosesOrder(Order $order, User $actor): void
    {
        DB::transaction(function () use ($order, $actor): void {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::with('items')
                ->lockForUpdate()
                ->findOrFail($order->id);

            if (! $actor->isOutlet() || ! $actor->outlet || (int) $lockedOrder->outlet_id !== (int) $actor->outlet->id) {
                abort(403, 'Anda hanya dapat menghapus pesanan milik outlet Anda sendiri.');
            }

            if ($lockedOrder->status !== OrderStatus::Diproses) {
                throw ValidationException::withMessages([
                    'status' => 'Pesanan yang sudah Selesai tidak dapat dihapus.',
                ]);
            }

            foreach ($lockedOrder->items as $item) {
                Menu::where('id', $item->menu_id)
                    ->where('outlet_id', $lockedOrder->outlet_id)
                    ->increment('stok_porsi', $item->jumlah);
            }

            $lockedOrder->items()->delete();
            $lockedOrder->delete();
        });
    }

    public function generateOrderCode(): string
    {
        $datePrefix = 'AYZ-' . now()->format('Ymd') . '-';

        $latest = Order::where('kode', 'like', $datePrefix . '%')
            ->orderByDesc('id')
            ->value('kode');

        $nextNumber = 1;
        if ($latest && preg_match('/-(\d{4,})$/', $latest, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        } else {
            $nextNumber = Order::count() + 1;
        }

        $candidate = $datePrefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
        while (Order::where('kode', $candidate)->exists()) {
            $nextNumber++;
            $candidate = $datePrefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}

<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\OutletStatus;
use App\Models\Antrean;
use App\Models\AntreanItem;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AntreanService
{
    /**
     * Submit keranjang tamu ke daftar tunggu (antrean).
     * Satu antrean = satu outlet, nama_pemesan wajib, kunci harga_saat_submit, TIDAK mengurangi stok porsi.
     *
     * @param  array<int, array{menu_id: int, jumlah: int}>  $items
     */
    public function submit(string $namaPemesan, array $items): Antrean
    {
        $namaPemesan = trim($namaPemesan);
        if ($namaPemesan === '') {
            throw ValidationException::withMessages([
                'nama_pemesan' => 'Nama pemesan wajib diisi.',
            ]);
        }

        if (empty($items)) {
            throw ValidationException::withMessages([
                'keranjang' => 'Keranjang pesanan masih kosong.',
            ]);
        }

        return DB::transaction(function () use ($namaPemesan, $items): Antrean {
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
                    'keranjang' => 'Satu antrean/pesanan hanya boleh berisi menu dari satu kantin/outlet.',
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
            $antreanItemsPayload = [];

            foreach ($normalized as $menuId => $jumlah) {
                /** @var Menu $menu */
                $menu = $menus->get($menuId);

                if (! $menu->tersedia || $menu->stok_porsi <= 0) {
                    throw ValidationException::withMessages([
                        'keranjang' => "Menu \"{$menu->nama}\" sedang habis atau tidak tersedia.",
                    ]);
                }

                if ($menu->stok_porsi < $jumlah) {
                    throw ValidationException::withMessages([
                        'keranjang' => "Stok porsi untuk \"{$menu->nama}\" tidak mencukupi (tersisa {$menu->stok_porsi} porsi).",
                    ]);
                }

                $hargaSubmit = (int) $menu->harga;
                $subtotal = $hargaSubmit * $jumlah;
                $total += $subtotal;

                $antreanItemsPayload[] = [
                    'menu_id' => $menu->id,
                    'nama_menu_saat_submit' => $menu->nama,
                    'harga_saat_submit' => $hargaSubmit,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal,
                ];
            }

            $kode = $this->generateUniqueCode();

            $antrean = Antrean::create([
                'kode' => $kode,
                'outlet_id' => $outlet->id,
                'nama_pemesan' => $namaPemesan,
                'total' => $total,
                'is_sample' => false,
            ]);

            foreach ($antreanItemsPayload as $payload) {
                AntreanItem::create([
                    'antrean_id' => $antrean->id,
                    'menu_id' => $payload['menu_id'],
                    'nama_menu_saat_submit' => $payload['nama_menu_saat_submit'],
                    'harga_saat_submit' => $payload['harga_saat_submit'],
                    'jumlah' => $payload['jumlah'],
                    'subtotal' => $payload['subtotal'],
                ]);
            }

            return $antrean->load(['outlet', 'items']);
        });
    }

    /**
     * Konfirmasi pembayaran tunai di kasir:
     * Buat orders (status Diproses) + order_items dari antrean dalam satu transaksi,
     * kurangi stok_porsi menu, lalu hapus antrean. Tolak bila stok porsi habis/tidak cukup.
     */
    public function confirmCashPayment(Antrean $antrean, User $actor): Order
    {
        return DB::transaction(function () use ($antrean, $actor): Order {
            /** @var Antrean $lockedAntrean */
            $lockedAntrean = Antrean::with(['outlet', 'items'])
                ->lockForUpdate()
                ->findOrFail($antrean->id);

            if (! $actor->isOutlet() || ! $actor->outlet || (int) $lockedAntrean->outlet_id !== (int) $actor->outlet->id) {
                abort(403, 'Anda hanya dapat mengonfirmasi pembayaran antrean milik outlet Anda sendiri.');
            }

            if ($lockedAntrean->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'antrean' => 'Antrean ini tidak memiliki item menu.',
                ]);
            }

            $menuIds = $lockedAntrean->items->pluck('menu_id')->unique()->all();
            $menus = Menu::where('outlet_id', $lockedAntrean->outlet_id)
                ->whereIn('id', $menuIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Validasi stok porsi saat konfirmasi bayar
            foreach ($lockedAntrean->items as $item) {
                /** @var Menu|null $menu */
                $menu = $menus->get($item->menu_id);

                if (! $menu || ! $menu->tersedia || $menu->stok_porsi <= 0) {
                    throw ValidationException::withMessages([
                        'stok_porsi' => "Stok porsi untuk \"{$item->nama_menu_saat_submit}\" sudah habis. Antrean tidak dapat dikonfirmasi.",
                    ]);
                }

                if ($menu->stok_porsi < $item->jumlah) {
                    throw ValidationException::withMessages([
                        'stok_porsi' => "Stok porsi untuk \"{$item->nama_menu_saat_submit}\" tidak mencukupi (dibutuhkan {$item->jumlah}, tersisa {$menu->stok_porsi}). Antrean tidak dapat dikonfirmasi.",
                    ]);
                }
            }

            $order = Order::create([
                'kode' => $lockedAntrean->kode,
                'nama_pemesan' => $lockedAntrean->nama_pemesan,
                'outlet_id' => $lockedAntrean->outlet_id,
                'status' => OrderStatus::Diproses,
                'total' => $lockedAntrean->total,
                'royalty_percent_saat_selesai' => null,
                'royalty_nominal' => 0,
                'metode_bayar' => 'bayar_di_kasir',
                'selesai_at' => null,
                'is_sample' => false,
            ]);

            foreach ($lockedAntrean->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item->menu_id,
                    'nama_menu_saat_submit' => $item->nama_menu_saat_submit,
                    'harga_saat_submit' => $item->harga_saat_submit,
                    'jumlah' => $item->jumlah,
                    'subtotal' => $item->subtotal,
                ]);

                /** @var Menu $menu */
                $menu = $menus->get($item->menu_id);
                $menu->decrement('stok_porsi', $item->jumlah);
            }

            $lockedAntrean->items()->delete();
            $lockedAntrean->delete();

            return $order->load(['outlet', 'items']);
        });
    }

    /**
     * Hapus antrean yang tidak datang (tanpa efek apa pun pada stok maupun royalti).
     */
    public function deleteAntrean(Antrean $antrean, User $actor): void
    {
        DB::transaction(function () use ($antrean, $actor): void {
            /** @var Antrean $lockedAntrean */
            $lockedAntrean = Antrean::lockForUpdate()->findOrFail($antrean->id);

            if (! $actor->isOutlet() || ! $actor->outlet || (int) $lockedAntrean->outlet_id !== (int) $actor->outlet->id) {
                abort(403, 'Anda hanya dapat menghapus antrean milik outlet Anda sendiri.');
            }

            $lockedAntrean->items()->delete();
            $lockedAntrean->delete();
        });
    }

    /**
     * Buat kode unik AYZ-YYYYMMDD-XXXX yang belum dipakai di tabel antrean maupun orders.
     */
    public function generateUniqueCode(): string
    {
        $datePrefix = 'AYZ-' . now()->format('Ymd') . '-';

        $latestOrderKode = Order::where('kode', 'like', $datePrefix . '%')
            ->orderByDesc('kode')
            ->value('kode');

        $latestAntreanKode = Antrean::where('kode', 'like', $datePrefix . '%')
            ->orderByDesc('kode')
            ->value('kode');

        $maxNumber = 0;
        foreach ([$latestOrderKode, $latestAntreanKode] as $kode) {
            if ($kode && preg_match('/-(\d{4,})$/', $kode, $matches)) {
                $maxNumber = max($maxNumber, (int) $matches[1]);
            }
        }

        $nextNumber = $maxNumber > 0 ? $maxNumber + 1 : (Order::count() + Antrean::count() + 1);

        $candidate = $datePrefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
        while (
            Order::where('kode', $candidate)->exists()
            || Antrean::where('kode', $candidate)->exists()
        ) {
            $nextNumber++;
            $candidate = $datePrefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}

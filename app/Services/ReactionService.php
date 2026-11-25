<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\ReactionEmote;
use App\Models\Antrean;
use App\Models\Order;
use App\Models\Reaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReactionService
{
    /**
     * Ambil pesanan yang SUDAH DIBAYAR (berstatus Diproses atau Selesai) berdasarkan kode pesanan.
     * Menolak bila kode tersebut masih di daftar tunggu (antrean / belum dibayar) atau tidak ditemukan.
     */
    public function findPaidOrderOrFail(string $kode): Order
    {
        $kode = trim($kode);

        $order = Order::with(['outlet', 'items.menu', 'reactions'])
            ->where('kode', $kode)
            ->first();

        if (! $order) {
            $isAntrean = Antrean::where('kode', $kode)->exists();
            if ($isAntrean) {
                throw ValidationException::withMessages([
                    'kode' => "Kode {$kode} masih berada di daftar tunggu (antrean) dan belum dibayar di kasir. Reaksi emote hanya dapat diberikan setelah pembayaran tunai dikonfirmasi kasir.",
                ]);
            }

            throw ValidationException::withMessages([
                'kode' => "Pesanan dengan kode \"{$kode}\" tidak ditemukan.",
            ]);
        }

        if (! in_array($order->status, [OrderStatus::Diproses, OrderStatus::Selesai], true)) {
            throw ValidationException::withMessages([
                'kode' => 'Reaksi hanya dapat diberikan untuk pesanan yang sudah dibayar.',
            ]);
        }

        return $order;
    }

    /**
     * Simpan atau ganti (update) reaksi emote pelanggan tamu untuk satu menu dalam satu pesanan yang sudah dibayar.
     * Unique key: (order_id, menu_id) — reaksi kedua menggantikan yang lama, bukan menambah baris.
     */
    public function submitReaction(string $kode, int $menuId, ReactionEmote|string $emote): Reaction
    {
        $emoteEnum = $emote instanceof ReactionEmote ? $emote : ReactionEmote::from($emote);

        return DB::transaction(function () use ($kode, $menuId, $emoteEnum): Reaction {
            $order = $this->findPaidOrderOrFail($kode);

            $hasMenuInOrder = $order->items->contains(fn ($item) => (int) $item->menu_id === $menuId);
            if (! $hasMenuInOrder) {
                throw ValidationException::withMessages([
                    'menu_id' => 'Menu yang dipilih bukan bagian dari pesanan ini.',
                ]);
            }

            return Reaction::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'menu_id' => $menuId,
                ],
                [
                    'emote' => $emoteEnum,
                    'is_sample' => false,
                ]
            );
        });
    }

    /**
     * Hitung reaksi per emote untuk daftar menu_id menggunakan GROUP BY emote (tanpa skor, rata-rata, atau ranking).
     *
     * @param  iterable<int>  $menuIds
     * @return array<int, array{enak: int, pedas: int, manis: int}>
     */
    public function getCountsByMenuIds(iterable $menuIds): array
    {
        $ids = collect($menuIds)->map(fn ($id) => (int) $id)->filter()->unique()->values()->all();
        if (empty($ids)) {
            return [];
        }

        $result = [];
        foreach ($ids as $id) {
            $result[$id] = [
                'enak' => 0,
                'pedas' => 0,
                'manis' => 0,
            ];
        }

        $rows = Reaction::query()
            ->selectRaw('menu_id, emote, COUNT(*) as total')
            ->whereIn('menu_id', $ids)
            ->groupBy('menu_id', 'emote')
            ->get();

        foreach ($rows as $row) {
            $mId = (int) $row->menu_id;
            $emoteKey = $row->emote instanceof ReactionEmote ? $row->emote->value : (string) $row->emote;
            if (isset($result[$mId][$emoteKey])) {
                $result[$mId][$emoteKey] = (int) $row->total;
            }
        }

        return $result;
    }
}

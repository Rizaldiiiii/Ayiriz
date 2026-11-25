<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private readonly RoyaltyService $royaltyService,
        private readonly StockService $stockService
    ) {}

    /**
     * Tandai pesanan yang sudah dibayar dan dimasak menjadi Selesai saat diambil pelanggan (Diproses -> Selesai, satu arah).
     * Mencatat royalti 10% (atau persen royalti outlet saat itu) dari total pesanan,
     * serta mengurangi stok bahan baku otomatis untuk menu yang memiliki resep (KF-06).
     */
    public function completeOrder(Order $order, User $actor): Order
    {
        return DB::transaction(function () use ($order, $actor): Order {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::with(['outlet', 'items.menu.menuIngredients'])
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

            // KF-06: Kurangi stok bahan baku otomatis hanya untuk menu yang memiliki resep saat pesanan Selesai
            $this->stockService->deductIngredientsForCompletedOrder($lockedOrder);

            return $lockedOrder->fresh(['outlet', 'items']);
        });
    }
}

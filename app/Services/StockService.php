<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\MenuIngredient;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    /**
     * Buat bahan baku baru untuk outlet dan catat movement awal bila stok > 0.
     *
     * @param  array{nama: string, satuan: string, stok: float|int|string, batas_minimum: float|int|string, is_sample?: bool}  $data
     */
    public function createIngredient(Outlet $outlet, array $data): Ingredient
    {
        return DB::transaction(function () use ($outlet, $data): Ingredient {
            $stokAwal = round(max(0, (float) ($data['stok'] ?? 0)), 2);
            $batasMin = round(max(0, (float) ($data['batas_minimum'] ?? 0)), 2);

            $ingredient = Ingredient::create([
                'outlet_id' => $outlet->id,
                'nama' => trim($data['nama']),
                'satuan' => trim($data['satuan']),
                'stok' => $stokAwal,
                'batas_minimum' => $batasMin,
                'is_sample' => (bool) ($data['is_sample'] ?? false),
            ]);

            if ($stokAwal > 0) {
                StockMovement::create([
                    'ingredient_id' => $ingredient->id,
                    'jenis' => StockMovementType::Masuk,
                    'jumlah' => $stokAwal,
                    'keterangan' => 'Stok awal bahan baku',
                    'created_at' => now(),
                ]);
            }

            return $ingredient;
        });
    }

    /**
     * Catat pergerakan stok bahan baku manual (masuk / keluar / penyesuaian) dan perbarui ingredients.stok dalam transaksi yang sama.
     */
    public function recordMovement(
        Ingredient $ingredient,
        User $actor,
        StockMovementType|string $jenis,
        float|int|string $jumlah,
        ?string $keterangan = null
    ): StockMovement {
        $jenisEnum = $jenis instanceof StockMovementType ? $jenis : StockMovementType::from($jenis);
        $nilai = round((float) $jumlah, 2);

        if ($jenisEnum !== StockMovementType::Penyesuaian && $nilai <= 0) {
            throw ValidationException::withMessages([
                'jumlah' => 'Jumlah stok masuk/keluar harus lebih besar dari 0.',
            ]);
        }

        if ($jenisEnum === StockMovementType::Penyesuaian && $nilai < 0) {
            throw ValidationException::withMessages([
                'jumlah' => 'Stok hasil penyesuaian tidak boleh negatif.',
            ]);
        }

        return DB::transaction(function () use ($ingredient, $actor, $jenisEnum, $nilai, $keterangan): StockMovement {
            /** @var Ingredient $locked */
            $locked = Ingredient::lockForUpdate()->findOrFail($ingredient->id);

            if (! $actor->isOutlet() || ! $actor->outlet || (int) $locked->outlet_id !== (int) $actor->outlet->id) {
                abort(403, 'Anda hanya dapat mengelola stok bahan milik outlet Anda sendiri.');
            }

            $currentStok = (float) $locked->stok;

            if ($jenisEnum === StockMovementType::Masuk) {
                $newStok = round($currentStok + $nilai, 2);
                $recordedAmount = $nilai;
            } elseif ($jenisEnum === StockMovementType::Keluar) {
                if ($currentStok < $nilai) {
                    throw ValidationException::withMessages([
                        'jumlah' => "Stok bahan \"{$locked->nama}\" tidak mencukupi (tersisa {$locked->formattedStok()}).",
                    ]);
                }
                $newStok = round($currentStok - $nilai, 2);
                $recordedAmount = $nilai;
            } else {
                $newStok = round($nilai, 2);
                $recordedAmount = $nilai;
            }

            $locked->update([
                'stok' => $newStok,
            ]);

            return StockMovement::create([
                'ingredient_id' => $locked->id,
                'jenis' => $jenisEnum,
                'jumlah' => $recordedAmount,
                'keterangan' => $keterangan ? trim($keterangan) : $jenisEnum->label(),
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Simpan resep sederhana (0..n bahan baku dengan takaran per 1 porsi) untuk satu menu milik outlet.
     *
     * @param  array<int, array{ingredient_id?: int|string|null, takaran?: float|int|string|null}>  $recipeRows
     */
    public function syncMenuRecipe(Menu $menu, User $actor, array $recipeRows): void
    {
        DB::transaction(function () use ($menu, $actor, $recipeRows): void {
            if (! $actor->isOutlet() || ! $actor->outlet || (int) $menu->outlet_id !== (int) $actor->outlet->id) {
                abort(403, 'Anda hanya dapat mengatur resep untuk menu milik outlet Anda sendiri.');
            }

            $normalized = [];
            foreach ($recipeRows as $row) {
                $ingId = (int) ($row['ingredient_id'] ?? 0);
                $takaran = round((float) ($row['takaran'] ?? 0), 2);

                if ($ingId > 0 && $takaran > 0) {
                    $normalized[$ingId] = $takaran;
                }
            }

            if (! empty($normalized)) {
                $validCount = Ingredient::where('outlet_id', $menu->outlet_id)
                    ->whereIn('id', array_keys($normalized))
                    ->count();

                if ($validCount !== count($normalized)) {
                    throw ValidationException::withMessages([
                        'resep' => 'Seluruh bahan baku pada resep harus berasal dari outlet Anda sendiri.',
                    ]);
                }
            }

            MenuIngredient::where('menu_id', $menu->id)->delete();

            foreach ($normalized as $ingId => $takaran) {
                MenuIngredient::create([
                    'menu_id' => $menu->id,
                    'ingredient_id' => $ingId,
                    'takaran' => $takaran,
                ]);
            }
        });
    }

    /**
     * Kurangi stok bahan baku secara otomatis saat pesanan ditandai Selesai,
     * HANYA untuk menu yang memiliki resep (menu_ingredients).
     */
    public function deductIngredientsForCompletedOrder(Order $order): void
    {
        $order->loadMissing(['items.menu.menuIngredients']);

        $usageByIngredient = [];
        foreach ($order->items as $orderItem) {
            $menu = $orderItem->menu;
            if (! $menu || $menu->menuIngredients->isEmpty()) {
                continue;
            }

            foreach ($menu->menuIngredients as $recipeRow) {
                $ingId = (int) $recipeRow->ingredient_id;
                $used = round((float) $recipeRow->takaran * (int) $orderItem->jumlah, 2);
                if ($used > 0) {
                    $usageByIngredient[$ingId] = round(($usageByIngredient[$ingId] ?? 0.0) + $used, 2);
                }
            }
        }

        if (empty($usageByIngredient)) {
            return;
        }

        $ingredients = Ingredient::where('outlet_id', $order->outlet_id)
            ->whereIn('id', array_keys($usageByIngredient))
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($usageByIngredient as $ingId => $usedAmount) {
            /** @var Ingredient|null $ingredient */
            $ingredient = $ingredients->get($ingId);
            if (! $ingredient) {
                continue;
            }

            $newStok = max(0, round((float) $ingredient->stok - $usedAmount, 2));
            $ingredient->update([
                'stok' => $newStok,
            ]);

            StockMovement::create([
                'ingredient_id' => $ingredient->id,
                'jenis' => StockMovementType::Keluar,
                'jumlah' => $usedAmount,
                'keterangan' => "Pemakaian resep pesanan {$order->kode}",
                'created_at' => now(),
            ]);
        }
    }
}

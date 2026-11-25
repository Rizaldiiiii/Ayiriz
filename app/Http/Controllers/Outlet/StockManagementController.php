<?php

namespace App\Http\Controllers\Outlet;

use App\Enums\StockMovementType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Outlet\StoreIngredientRequest;
use App\Http\Requests\Outlet\StoreStockMovementRequest;
use App\Http\Requests\Outlet\UpdateIngredientRequest;
use App\Http\Requests\Outlet\UpdateMenuRecipeRequest;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockManagementController extends Controller
{
    public function __construct(
        private readonly StockService $stockService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Ingredient::class);

        $outlet = $request->user()->outlet;
        abort_if(! $outlet, 403, 'Akun outlet Anda belum terhubung dengan data kantin.');

        $ingredients = Ingredient::where('outlet_id', $outlet->id)
            ->orderBy('nama')
            ->get();

        $stokMenipis = $ingredients->filter(fn (Ingredient $item) => $item->isMenipis())->values();

        $movements = StockMovement::with('ingredient')
            ->whereHas('ingredient', fn ($q) => $q->where('outlet_id', $outlet->id))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        $menus = Menu::with(['category', 'menuIngredients.ingredient'])
            ->where('outlet_id', $outlet->id)
            ->orderBy('nama')
            ->get();

        return view('outlet.stock-index', [
            'outlet' => $outlet,
            'ingredients' => $ingredients,
            'stokMenipis' => $stokMenipis,
            'movements' => $movements,
            'menus' => $menus,
        ]);
    }

    public function storeIngredient(StoreIngredientRequest $request): RedirectResponse
    {
        $this->authorize('create', Ingredient::class);

        $outlet = $request->user()->outlet;
        $ingredient = $this->stockService->createIngredient($outlet, $request->validated());

        return redirect()->route('outlet.stok')
            ->with('toast_success', "Bahan baku \"{$ingredient->nama}\" berhasil ditambahkan.");
    }

    public function updateIngredient(UpdateIngredientRequest $request, Ingredient $ingredient): RedirectResponse
    {
        $this->authorize('update', $ingredient);

        $outlet = $request->user()->outlet;
        $owned = Ingredient::where('outlet_id', $outlet->id)->findOrFail($ingredient->id);
        $validated = $request->validated();

        $owned->update([
            'nama' => trim($validated['nama']),
            'satuan' => trim($validated['satuan']),
            'batas_minimum' => round((float) $validated['batas_minimum'], 2),
        ]);

        return redirect()->route('outlet.stok')
            ->with('toast_success', "Data bahan baku \"{$owned->nama}\" berhasil diperbarui.");
    }

    public function destroyIngredient(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $this->authorize('delete', $ingredient);

        $outlet = $request->user()->outlet;
        $owned = Ingredient::where('outlet_id', $outlet->id)->findOrFail($ingredient->id);
        $nama = $owned->nama;

        $owned->delete();

        return redirect()->route('outlet.stok')
            ->with('toast_info', "Bahan baku \"{$nama}\" telah dihapus.");
    }

    public function storeMovement(StoreStockMovementRequest $request, Ingredient $ingredient): RedirectResponse
    {
        $this->authorize('recordMovement', $ingredient);

        $outlet = $request->user()->outlet;
        $owned = Ingredient::where('outlet_id', $outlet->id)->findOrFail($ingredient->id);
        $validated = $request->validated();

        $jenis = StockMovementType::from($validated['jenis']);
        $this->stockService->recordMovement(
            $owned,
            $request->user(),
            $jenis,
            (float) $validated['jumlah'],
            $validated['keterangan'] ?? null
        );

        return redirect()->route('outlet.stok')
            ->with('toast_success', "{$jenis->label()} untuk \"{$owned->nama}\" berhasil dicatat.");
    }

    public function updateRecipe(UpdateMenuRecipeRequest $request, Menu $menu): RedirectResponse
    {
        $this->authorize('update', $menu);

        $outlet = $request->user()->outlet;
        $ownedMenu = Menu::where('outlet_id', $outlet->id)->findOrFail($menu->id);

        $rows = $request->validated()['resep'] ?? [];
        $this->stockService->syncMenuRecipe($ownedMenu, $request->user(), $rows);

        return redirect()->route('outlet.stok')
            ->with('toast_success', "Resep bahan baku untuk menu \"{$ownedMenu->nama}\" berhasil disimpan.");
    }
}

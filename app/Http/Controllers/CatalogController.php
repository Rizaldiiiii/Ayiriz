<?php

namespace App\Http\Controllers;

use App\Enums\OutletStatus;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Outlet;
use App\Services\CartService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category_id');
        $outletId = $request->query('outlet_id');
        $minHarga = $request->query('min_harga');
        $maxHarga = $request->query('max_harga');

        $outlets = Outlet::aktif()
            ->withCount(['menus' => fn (Builder $query) => $query->where('tersedia', true)])
            ->orderBy('nama')
            ->get();

        $categories = Category::orderBy('nama')->get();

        $menusQuery = Menu::with(['outlet', 'category'])
            ->katalogPublik();

        if ($q !== '') {
            $menusQuery->where(function (Builder $sub) use ($q): void {
                $sub->where('nama', 'like', '%' . $q . '%')
                    ->orWhere('deskripsi', 'like', '%' . $q . '%');
            });
        }

        if ($categoryId !== null && $categoryId !== '') {
            $menusQuery->where('category_id', (int) $categoryId);
        }

        if ($outletId !== null && $outletId !== '') {
            $menusQuery->where('outlet_id', (int) $outletId);
        }

        if ($minHarga !== null && $minHarga !== '' && is_numeric($minHarga)) {
            $menusQuery->where('harga', '>=', (int) $minHarga);
        }

        if ($maxHarga !== null && $maxHarga !== '' && is_numeric($maxHarga)) {
            $menusQuery->where('harga', '<=', (int) $maxHarga);
        }

        $menus = $menusQuery
            ->orderBy('outlet_id')
            ->orderBy('nama')
            ->paginate(12)
            ->withQueryString();

        $cart = $this->cartService->getCartDetails();

        return view('public.home', [
            'outlets' => $outlets,
            'categories' => $categories,
            'menus' => $menus,
            'cart' => $cart,
            'filters' => [
                'q' => $q,
                'category_id' => $categoryId,
                'outlet_id' => $outletId,
                'min_harga' => $minHarga,
                'max_harga' => $maxHarga,
            ],
        ]);
    }

    public function showOutlet(Request $request, string $slug): View
    {
        $outlet = Outlet::where('slug', $slug)
            ->where('status', OutletStatus::Terverifikasi)
            ->firstOrFail();

        $q = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category_id');

        $categories = Category::orderBy('nama')->get();

        $menusQuery = $outlet->menus()
            ->with('category')
            ->where('tersedia', true);

        if ($q !== '') {
            $menusQuery->where(function (Builder $sub) use ($q): void {
                $sub->where('nama', 'like', '%' . $q . '%')
                    ->orWhere('deskripsi', 'like', '%' . $q . '%');
            });
        }

        if ($categoryId !== null && $categoryId !== '') {
            $menusQuery->where('category_id', (int) $categoryId);
        }

        $menus = $menusQuery->orderBy('nama')->get();
        $cart = $this->cartService->getCartDetails();

        return view('public.outlet-show', [
            'outlet' => $outlet,
            'categories' => $categories,
            'menus' => $menus,
            'cart' => $cart,
            'filters' => [
                'q' => $q,
                'category_id' => $categoryId,
            ],
        ]);
    }

    public function showMenu(Menu $menu): View
    {
        $menu->load(['outlet', 'category']);

        if (! $menu->tersedia || ! $menu->outlet || $menu->outlet->status !== OutletStatus::Terverifikasi) {
            abort(404, 'Menu tidak tersedia di katalog publik.');
        }

        $relatedMenus = Menu::with('category')
            ->where('outlet_id', $menu->outlet_id)
            ->where('tersedia', true)
            ->where('id', '!=', $menu->id)
            ->limit(4)
            ->get();

        $cart = $this->cartService->getCartDetails();

        return view('public.menu-show', [
            'menu' => $menu,
            'relatedMenus' => $relatedMenus,
            'cart' => $cart,
        ]);
    }
}

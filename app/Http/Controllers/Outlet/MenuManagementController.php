<?php

namespace App\Http\Controllers\Outlet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Outlet\StoreMenuRequest;
use App\Http\Requests\Outlet\UpdateMenuRequest;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuManagementController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Menu::class);

        $outlet = $request->user()->outlet;
        $menus = Menu::with('category')
            ->where('outlet_id', $outlet->id)
            ->orderBy('nama')
            ->get();

        $categories = Category::orderBy('nama')->get();

        return view('outlet.menus-index', [
            'outlet' => $outlet,
            'menus' => $menus,
            'categories' => $categories,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Menu::class);

        $outlet = $request->user()->outlet;
        $categories = Category::orderBy('nama')->get();

        return view('outlet.menus-create', [
            'outlet' => $outlet,
            'categories' => $categories,
        ]);
    }

    public function store(StoreMenuRequest $request): RedirectResponse
    {
        $this->authorize('create', Menu::class);

        $outlet = $request->user()->outlet;
        $validated = $request->validated();

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('menu', 'public');
        }

        $menu = Menu::create([
            'outlet_id' => $outlet->id,
            'category_id' => (int) $validated['category_id'],
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'harga' => (int) $validated['harga'],
            'stok_porsi' => (int) $validated['stok_porsi'],
            'tersedia' => $request->boolean('tersedia', true),
            'foto' => $fotoPath,
            'is_sample' => false,
        ]);

        return redirect()->route('outlet.menus.index')
            ->with('toast_success', "Menu \"{$menu->nama}\" berhasil ditambahkan.");
    }

    public function edit(Request $request, Menu $menu): View
    {
        $this->authorize('update', $menu);

        $outlet = $request->user()->outlet;
        $ownedMenu = Menu::where('outlet_id', $outlet->id)->findOrFail($menu->id);
        $categories = Category::orderBy('nama')->get();

        return view('outlet.menus-edit', [
            'outlet' => $outlet,
            'menu' => $ownedMenu,
            'categories' => $categories,
        ]);
    }

    public function update(UpdateMenuRequest $request, Menu $menu): RedirectResponse
    {
        $this->authorize('update', $menu);

        $outlet = $request->user()->outlet;
        $ownedMenu = Menu::where('outlet_id', $outlet->id)->findOrFail($menu->id);
        $validated = $request->validated();

        $fotoPath = $ownedMenu->foto;
        if ($request->hasFile('foto')) {
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto')->store('menu', 'public');
        }

        $ownedMenu->update([
            'category_id' => (int) $validated['category_id'],
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'harga' => (int) $validated['harga'],
            'stok_porsi' => (int) $validated['stok_porsi'],
            'tersedia' => $request->boolean('tersedia'),
            'foto' => $fotoPath,
        ]);

        return redirect()->route('outlet.menus.index')
            ->with('toast_success', "Menu \"{$ownedMenu->nama}\" berhasil diperbarui.");
    }

    public function destroy(Request $request, Menu $menu): RedirectResponse
    {
        $this->authorize('delete', $menu);

        $outlet = $request->user()->outlet;
        $ownedMenu = Menu::where('outlet_id', $outlet->id)->findOrFail($menu->id);
        $nama = $ownedMenu->nama;

        if ($ownedMenu->foto && Storage::disk('public')->exists($ownedMenu->foto)) {
            Storage::disk('public')->delete($ownedMenu->foto);
        }

        $ownedMenu->delete();

        return redirect()->route('outlet.menus.index')
            ->with('toast_info', "Menu \"{$nama}\" telah dihapus.");
    }
}

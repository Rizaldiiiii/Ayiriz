<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\Outlet\CashierOrderController;
use App\Http\Controllers\Outlet\MenuManagementController;
use App\Http\Controllers\Outlet\OutletDashboardController;
use App\Http\Controllers\Pusat\OutletManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Autentikasi (KF-11: Hanya Login Pusat & Admin Outlet — Tanpa Register Pelanggan)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Panel Admin Outlet & Kasir (KF-02, KF-05 + Stub Tahap 2)
| Didefinisikan sebelum /outlet/{slug} agar rute statis tidak tertangkap slug
|--------------------------------------------------------------------------
*/
Route::prefix('outlet')
    ->name('outlet.')
    ->middleware(['auth', 'role:outlet'])
    ->group(function (): void {
        Route::get('/dashboard', [OutletDashboardController::class, 'index'])->name('dashboard');

        // KF-02 Manajemen Menu per Outlet
        Route::get('/menu', [MenuManagementController::class, 'index'])->name('menus.index');
        Route::get('/menu/tambah', [MenuManagementController::class, 'create'])->name('menus.create');
        Route::post('/menu', [MenuManagementController::class, 'store'])->name('menus.store');
        Route::get('/menu/{menu}/edit', [MenuManagementController::class, 'edit'])->name('menus.edit');
        Route::put('/menu/{menu}', [MenuManagementController::class, 'update'])->name('menus.update');
        Route::delete('/menu/{menu}', [MenuManagementController::class, 'destroy'])->name('menus.destroy');

        // KF-05 Kasir: Daftar Tunggu (Antrean) + Konfirmasi Bayar Tunai + Penyelesaian Pesanan
        Route::get('/pesanan', [CashierOrderController::class, 'index'])->name('orders.index');
        Route::get('/antrean/{antrean}', [CashierOrderController::class, 'showAntrean'])->name('antrean.show');
        Route::post('/antrean/{antrean}/konfirmasi-bayar', [CashierOrderController::class, 'confirmPayment'])->name('antrean.confirm');
        Route::delete('/antrean/{antrean}', [CashierOrderController::class, 'destroyAntrean'])->name('antrean.destroy');

        Route::get('/pesanan/{order}', [CashierOrderController::class, 'show'])->name('orders.show');
        Route::patch('/pesanan/{order}/selesai', [CashierOrderController::class, 'complete'])->name('orders.complete');

        // Stub Tahap 2 (KF-06 Stok, KF-09 Laporan Outlet)
        Route::get('/stok', [OutletDashboardController::class, 'stubStok'])->name('stok');
        Route::get('/laporan', [OutletDashboardController::class, 'stubLaporan'])->name('laporan');
    });

/*
|--------------------------------------------------------------------------
| Panel Pusat Ayiriz (KF-01 + Stub Tahap 3)
|--------------------------------------------------------------------------
*/
Route::prefix('pusat')
    ->name('pusat.')
    ->middleware(['auth', 'role:pusat'])
    ->group(function (): void {
        Route::get('/dashboard', [OutletManagementController::class, 'dashboard'])->name('dashboard');

        // KF-01 Manajemen Outlet
        Route::get('/outlet', [OutletManagementController::class, 'index'])->name('outlets.index');
        Route::get('/outlet/tambah', [OutletManagementController::class, 'create'])->name('outlets.create');
        Route::post('/outlet', [OutletManagementController::class, 'store'])->name('outlets.store');
        Route::get('/outlet/{outlet}/edit', [OutletManagementController::class, 'edit'])->name('outlets.edit');
        Route::put('/outlet/{outlet}', [OutletManagementController::class, 'update'])->name('outlets.update');
        Route::patch('/outlet/{outlet}/status', [OutletManagementController::class, 'updateStatus'])->name('outlets.status');

        // Stub Tahap 3 (KF-08 Laporan Pusat, KF-10 Audit Royalti)
        Route::get('/laporan', [OutletManagementController::class, 'stubLaporan'])->name('laporan');
        Route::get('/audit', [OutletManagementController::class, 'stubAudit'])->name('audit');
    });

/*
|--------------------------------------------------------------------------
| Pelanggan Tamu (Tanpa Login) — Keranjang Session & Submit ke Antrean (KF-04)
|--------------------------------------------------------------------------
*/
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/item', [CartController::class, 'addItem'])->name('cart.items.store');
Route::patch('/keranjang/item/{menu}', [CartController::class, 'updateItem'])->name('cart.items.update');
Route::delete('/keranjang/item/{menu}', [CartController::class, 'removeItem'])->name('cart.items.destroy');
Route::delete('/keranjang', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/keranjang/submit', [CartController::class, 'submit'])->name('cart.submit');

/*
|--------------------------------------------------------------------------
| Katalog Publik, Pencarian, dan Filter (KF-03 — Tanpa Login) + Stub Reaksi (KF-07)
|--------------------------------------------------------------------------
*/
Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/outlet/{slug}', [CatalogController::class, 'showOutlet'])
    ->where('slug', '^(?!dashboard$|menu$|pesanan$|antrean$|stok$|laporan$)[a-zA-Z0-9\-]+$')
    ->name('catalog.outlet');
Route::get('/menu/{menu}', [CatalogController::class, 'showMenu'])->name('catalog.menu');
Route::get('/reaksi/{kode}', [CatalogController::class, 'stubReaksi'])->name('catalog.reaksi');

<?php

use App\Enums\OrderStatus;
use App\Enums\OutletStatus;
use App\Enums\ReactionEmote;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Reaction;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\AntreanService;

test('KF-06: stok bahan masuk/keluar manual, peringatan stok menipis di dashboard outlet, dan isolasi stok antar-outlet', function () {
    $userA = User::factory()->outlet()->create();
    $outletA = Outlet::create([
        'user_id' => $userA->id,
        'nama' => 'Outlet Stok A',
        'slug' => 'outlet-stok-a',
        'pemilik' => 'Pemilik A',
        'no_hp' => '08111',
        'alamat' => 'Depan UNG A',
        'jam_buka' => '08:00-17:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    $userB = User::factory()->outlet()->create();
    $outletB = Outlet::create([
        'user_id' => $userB->id,
        'nama' => 'Outlet Stok B',
        'slug' => 'outlet-stok-b',
        'pemilik' => 'Pemilik B',
        'no_hp' => '08222',
        'alamat' => 'Depan UNG B',
        'jam_buka' => '08:00-17:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    // 1. Outlet A menambah bahan baku dengan stok awal 5 kg dan batas minimum 3 kg
    $this->actingAs($userA)->post(route('outlet.stok.ingredients.store'), [
        'nama' => 'Ayam Potong Segar',
        'satuan' => 'kg',
        'stok' => 5,
        'batas_minimum' => 3,
    ])->assertRedirect(route('outlet.stok'));

    $ayam = Ingredient::where('outlet_id', $outletA->id)->where('nama', 'Ayam Potong Segar')->firstOrFail();
    expect((float) $ayam->stok)->toBe(5.0)
        ->and($ayam->isMenipis())->toBeFalse()
        ->and(StockMovement::where('ingredient_id', $ayam->id)->count())->toBe(1);

    // 2. Outlet B tidak boleh mencatat mutasi atau mengubah bahan milik Outlet A (403 Forbidden)
    $this->actingAs($userB)->post(route('outlet.stok.movements.store', $ayam), [
        'jenis' => 'keluar',
        'jumlah' => 1,
        'keterangan' => 'Percobaan akses ilegal',
    ])->assertForbidden();

    $this->actingAs($userB)->put(route('outlet.stok.ingredients.update', $ayam), [
        'nama' => 'Dibajak B',
        'satuan' => 'kg',
        'batas_minimum' => 1,
    ])->assertForbidden();

    // 3. Outlet A mencatat stok keluar manual 2.5 kg -> sisa 2.5 kg (<= batas minimum 3 kg) -> muncul di peringatan dashboard
    $this->actingAs($userA)->post(route('outlet.stok.movements.store', $ayam), [
        'jenis' => 'keluar',
        'jumlah' => 2.5,
        'keterangan' => 'Pemakaian masak pagi',
    ])->assertRedirect(route('outlet.stok'));

    expect((float) $ayam->fresh()->stok)->toBe(2.5)
        ->and($ayam->fresh()->isMenipis())->toBeTrue();

    $this->actingAs($userA)->get(route('outlet.dashboard'))
        ->assertOk()
        ->assertSee('Peringatan Stok Bahan Baku Menipis')
        ->assertSee('Ayam Potong Segar');

    // 4. Mencatat stok keluar melebihi sisa stok ditolak validasi
    $this->actingAs($userA)->post(route('outlet.stok.movements.store', $ayam), [
        'jenis' => 'keluar',
        'jumlah' => 10,
        'keterangan' => 'Melebihi sisa stok',
    ])->assertSessionHasErrors('jumlah');

    // 5. Outlet A mencatat stok masuk 4 kg -> stok jadi 6.5 kg -> peringatan menipis hilang
    $this->actingAs($userA)->post(route('outlet.stok.movements.store', $ayam), [
        'jenis' => 'masuk',
        'jumlah' => 4,
        'keterangan' => 'Belanja pasar sore',
    ])->assertRedirect(route('outlet.stok'));

    expect((float) $ayam->fresh()->stok)->toBe(6.5)
        ->and($ayam->fresh()->isMenipis())->toBeFalse();

    $this->actingAs($userA)->get(route('outlet.dashboard'))
        ->assertOk()
        ->assertDontSee('Peringatan Stok Bahan Baku Menipis');
});

test('KF-06: resep sederhana mengurangi stok bahan otomatis HANYA saat pesanan Selesai (dan stok porsi berkurang sekali saat konfirmasi bayar)', function () {
    $cat = Category::create(['nama' => 'Makanan Berat']);
    $userOutlet = User::factory()->outlet()->create();
    $outlet = Outlet::create([
        'user_id' => $userOutlet->id,
        'nama' => 'Outlet Resep',
        'slug' => 'outlet-resep',
        'pemilik' => 'Pemilik Resep',
        'no_hp' => '08123',
        'alamat' => 'Gerbang UNG',
        'jam_buka' => '08:00-17:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    $ayam = Ingredient::create([
        'outlet_id' => $outlet->id,
        'nama' => 'Daging Ayam',
        'satuan' => 'kg',
        'stok' => 10.00,
        'batas_minimum' => 2.00,
    ]);

    $beras = Ingredient::create([
        'outlet_id' => $outlet->id,
        'nama' => 'Beras',
        'satuan' => 'kg',
        'stok' => 10.00,
        'batas_minimum' => 2.00,
    ]);

    $menuDenganResep = Menu::create([
        'outlet_id' => $outlet->id,
        'category_id' => $cat->id,
        'nama' => 'Paket Ayam Geprek Resep',
        'harga' => 15000,
        'stok_porsi' => 20,
        'tersedia' => true,
    ]);

    $menuTanpaResep = Menu::create([
        'outlet_id' => $outlet->id,
        'category_id' => $cat->id,
        'nama' => 'Kerupuk Kaleng Tanpa Resep',
        'harga' => 3000,
        'stok_porsi' => 20,
        'tersedia' => true,
    ]);

    // Atur resep untuk $menuDenganResep: 0.20 kg ayam + 0.15 kg beras per 1 porsi
    $this->actingAs($userOutlet)->put(route('outlet.stok.recipes.update', $menuDenganResep), [
        'resep' => [
            ['ingredient_id' => $ayam->id, 'takaran' => 0.20],
            ['ingredient_id' => $beras->id, 'takaran' => 0.15],
        ],
    ])->assertRedirect(route('outlet.stok'));

    $antreanService = app(AntreanService::class);

    // 1. Tamu submit antrean (2 porsi menuDenganResep + 3 porsi menuTanpaResep)
    // Stok porsi maupun stok bahan TIDAK berkurang saat antrean dibuat
    $antrean = $antreanService->submit('Mahasiswa Resep', [
        ['menu_id' => $menuDenganResep->id, 'jumlah' => 2],
        ['menu_id' => $menuTanpaResep->id, 'jumlah' => 3],
    ]);

    expect($menuDenganResep->fresh()->stok_porsi)->toBe(20)
        ->and($menuTanpaResep->fresh()->stok_porsi)->toBe(20)
        ->and((float) $ayam->fresh()->stok)->toBe(10.00)
        ->and((float) $beras->fresh()->stok)->toBe(10.00);

    // 2. Kasir mengonfirmasi pembayaran tunai -> pesanan menjadi Diproses
    // Stok porsi berkurang (20->18 dan 20->17), tetapi stok bahan baku BELUM berkurang
    $this->actingAs($userOutlet)
        ->post(route('outlet.antrean.confirm', $antrean))
        ->assertRedirect();

    $order = Order::where('kode', $antrean->kode)->firstOrFail();
    expect($order->status)->toBe(OrderStatus::Diproses)
        ->and($menuDenganResep->fresh()->stok_porsi)->toBe(18)
        ->and($menuTanpaResep->fresh()->stok_porsi)->toBe(17)
        ->and((float) $ayam->fresh()->stok)->toBe(10.00)
        ->and((float) $beras->fresh()->stok)->toBe(10.00);

    // 3. Kasir menandai pesanan Selesai -> stok porsi TIDAK berkurang lagi (tetap 18 & 17),
    // sedangkan stok bahan baku berkurang otomatis untuk menu yang punya resep:
    // Ayam: 10.00 - (2 * 0.20) = 9.60 kg; Beras: 10.00 - (2 * 0.15) = 9.70 kg
    $this->actingAs($userOutlet)
        ->patch(route('outlet.orders.complete', $order))
        ->assertRedirect(route('outlet.orders.show', $order));

    expect($order->fresh()->status)->toBe(OrderStatus::Selesai)
        ->and($menuDenganResep->fresh()->stok_porsi)->toBe(18)
        ->and($menuTanpaResep->fresh()->stok_porsi)->toBe(17)
        ->and((float) $ayam->fresh()->stok)->toBe(9.60)
        ->and((float) $beras->fresh()->stok)->toBe(9.70);

    // Tercatat di stock_movements dengan keterangan kode pesanan
    expect(
        StockMovement::where('ingredient_id', $ayam->id)
            ->where('jenis', 'keluar')
            ->where('keterangan', 'like', "%{$order->kode}%")
            ->exists()
    )->toBeTrue();
});

test('KF-07: reaksi emote tamu ditolak saat masih antrean, dibuka saat pesanan sudah dibayar (Diproses maupun Selesai), dan reaksi kedua menggantikan yang lama', function () {
    $cat = Category::create(['nama' => 'Makanan Berat']);
    $userOutlet = User::factory()->outlet()->create();
    $outlet = Outlet::create([
        'user_id' => $userOutlet->id,
        'nama' => 'Outlet Reaksi',
        'slug' => 'outlet-reaksi',
        'pemilik' => 'Pemilik Reaksi',
        'no_hp' => '08199',
        'alamat' => 'Gerbang UNG',
        'jam_buka' => '08:00-17:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    $menu = Menu::create([
        'outlet_id' => $outlet->id,
        'category_id' => $cat->id,
        'nama' => 'Ayam Geprek Reaksi',
        'harga' => 15000,
        'stok_porsi' => 15,
        'tersedia' => true,
    ]);

    $menuLain = Menu::create([
        'outlet_id' => $outlet->id,
        'category_id' => $cat->id,
        'nama' => 'Menu Tidak Dipesan',
        'harga' => 10000,
        'stok_porsi' => 15,
        'tersedia' => true,
    ]);

    $antreanService = app(AntreanService::class);
    $antrean = $antreanService->submit('Tamu Reaksi', [
        ['menu_id' => $menu->id, 'jumlah' => 1],
    ]);
    $kode = $antrean->kode;

    // 1. Saat masih antrean (belum dibayar), halaman reaksi menolak pemberian reaksi
    $this->get(route('catalog.reaksi', $kode))
        ->assertOk()
        ->assertSee('Belum Dapat Diberi Reaksi');

    $this->post(route('catalog.reaksi.store', $kode), [
        'menu_id' => $menu->id,
        'emote' => 'enak',
    ])->assertSessionHasErrors('kode');

    expect(Reaction::count())->toBe(0);

    // 2. Kasir konfirmasi bayar tunai -> pesanan berstatus Diproses (sudah dibayar!)
    $this->actingAs($userOutlet)
        ->post(route('outlet.antrean.confirm', $antrean))
        ->assertRedirect();

    $order = Order::where('kode', $kode)->firstOrFail();
    expect($order->status)->toBe(OrderStatus::Diproses);

    // Layar kasir menampilkan QR reaksi pesanan tersebut
    $this->actingAs($userOutlet)->get(route('outlet.orders.show', $order))
        ->assertOk()
        ->assertSee(route('catalog.reaksi', $order->kode));

    // 3. Pelanggan tamu langsung dapat memberi reaksi saat pesanan berstatus Diproses (sudah dibayar, belum harus Selesai)
    $this->post(route('catalog.reaksi.store', $kode), [
        'menu_id' => $menu->id,
        'emote' => 'enak',
    ])->assertRedirect(route('catalog.reaksi', $kode));

    expect(Reaction::where('order_id', $order->id)->where('menu_id', $menu->id)->count())->toBe(1)
        ->and(Reaction::where('order_id', $order->id)->where('menu_id', $menu->id)->first()->emote)->toBe(ReactionEmote::Enak);

    // 4. Reaksi kedua pada menu & pesanan yang sama MENGGANTIKAN yang lama (bukan menambah baris)
    $this->post(route('catalog.reaksi.store', $kode), [
        'menu_id' => $menu->id,
        'emote' => 'pedas',
    ])->assertRedirect(route('catalog.reaksi', $kode));

    expect(Reaction::where('order_id', $order->id)->where('menu_id', $menu->id)->count())->toBe(1)
        ->and(Reaction::where('order_id', $order->id)->where('menu_id', $menu->id)->first()->emote)->toBe(ReactionEmote::Pedas);

    // 5. Menolak reaksi untuk menu yang tidak ada di pesanan tersebut
    $this->post(route('catalog.reaksi.store', $kode), [
        'menu_id' => $menuLain->id,
        'emote' => 'enak',
    ])->assertSessionHasErrors('menu_id');

    // 6. Hitungan reaksi tampil di halaman detail menu publik
    $counts = $menu->fresh()->reactionCounts();
    expect($counts['pedas'])->toBe(1)
        ->and($counts['enak'])->toBe(0);
});

test('KF-09: laporan outlet menampilkan penjualan Selesai, menu terlaris, stok menipis, dan reaksi HANYA milik outletnya sendiri', function () {
    $cat = Category::create(['nama' => 'Makanan Berat']);

    $userA = User::factory()->outlet()->create();
    $outletA = Outlet::create([
        'user_id' => $userA->id,
        'nama' => 'Kantin Laporan A',
        'slug' => 'kantin-laporan-a',
        'pemilik' => 'Pemilik A',
        'no_hp' => '0811',
        'alamat' => 'Depan UNG A',
        'jam_buka' => '08:00-17:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    $userB = User::factory()->outlet()->create();
    $outletB = Outlet::create([
        'user_id' => $userB->id,
        'nama' => 'Kantin Laporan B',
        'slug' => 'kantin-laporan-b',
        'pemilik' => 'Pemilik B',
        'no_hp' => '0822',
        'alamat' => 'Depan UNG B',
        'jam_buka' => '08:00-17:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    $menuA = Menu::create([
        'outlet_id' => $outletA->id,
        'category_id' => $cat->id,
        'nama' => 'Menu Unggulan Kantin A',
        'harga' => 20000,
        'stok_porsi' => 20,
        'tersedia' => true,
    ]);

    $menuB = Menu::create([
        'outlet_id' => $outletB->id,
        'category_id' => $cat->id,
        'nama' => 'Menu Rahasia Kantin B',
        'harga' => 25000,
        'stok_porsi' => 20,
        'tersedia' => true,
    ]);

    Ingredient::create([
        'outlet_id' => $outletA->id,
        'nama' => 'Bahan Menipis Kantin A',
        'satuan' => 'kg',
        'stok' => 1.00,
        'batas_minimum' => 3.00,
    ]);

    Ingredient::create([
        'outlet_id' => $outletB->id,
        'nama' => 'Bahan Menipis Kantin B',
        'satuan' => 'kg',
        'stok' => 0.50,
        'batas_minimum' => 2.00,
    ]);

    $antreanService = app(AntreanService::class);

    // Transaksi Selesai di Kantin A (2 x 20.000 = 40.000, royalti 4.000)
    $antreanA = $antreanService->submit('Pelanggan A', [['menu_id' => $menuA->id, 'jumlah' => 2]]);
    $orderA = $antreanService->confirmCashPayment($antreanA, $userA);
    $this->actingAs($userA)->patch(route('outlet.orders.complete', $orderA));

    // Transaksi Selesai di Kantin B (3 x 25.000 = 75.000)
    $antreanB = $antreanService->submit('Pelanggan B', [['menu_id' => $menuB->id, 'jumlah' => 3]]);
    $orderB = $antreanService->confirmCashPayment($antreanB, $userB);
    $this->actingAs($userB)->patch(route('outlet.orders.complete', $orderB));

    // Laporan Kantin A hanya menampilkan data Kantin A dan TIDAK menampilkan data Kantin B
    $this->actingAs($userA)->get(route('outlet.laporan', ['periode' => 'harian']))
        ->assertOk()
        ->assertSee('Menu Unggulan Kantin A')
        ->assertSee('Bahan Menipis Kantin A')
        ->assertSee('Rp 40.000')
        ->assertSee('Rp 4.000')
        ->assertDontSee('Menu Rahasia Kantin B')
        ->assertDontSee('Bahan Menipis Kantin B')
        ->assertDontSee('Rp 75.000');
});

<?php

use App\Enums\OrderStatus;
use App\Enums\OutletStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\User;
use App\Services\OrderService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

test('KN-04: enum OrderStatus hanya memiliki tepat dua nilai yaitu diproses dan selesai', function () {
    $cases = array_map(fn (OrderStatus $case) => $case->value, OrderStatus::cases());

    expect($cases)->toBe(['diproses', 'selesai']);
});

test('Minimalism §11: tidak ada class backdrop-blur di seluruh direktori resources', function () {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(base_path('resources'))
    );

    $found = [];
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $contents = file_get_contents($file->getPathname());
            if (str_contains($contents, 'backdrop-blur')) {
                $found[] = $file->getPathname();
            }
        }
    }

    expect($found)->toBeEmpty();
});

test('KF-11: pelanggan dapat mendaftar, password disimpan ter-hash, dan halaman terproteksi butuh login serta sesuai role', function () {
    // Halaman terproteksi menolak pengunjung belum login
    $this->get('/keranjang')->assertRedirect('/login');
    $this->get('/outlet/dashboard')->assertRedirect('/login');
    $this->get('/pusat/dashboard')->assertRedirect('/login');

    // Registrasi pelanggan
    $response = $this->post('/register', [
        'name' => 'Mahasiswa Baru UNG',
        'email' => 'mhsbaru@ung.ac.id',
        'password' => 'Mahasiswa123!',
        'password_confirmation' => 'Mahasiswa123!',
    ]);

    $response->assertRedirect(route('catalog.index'));

    $user = User::where('email', 'mhsbaru@ung.ac.id')->firstOrFail();
    expect($user->role)->toBe(UserRole::Pelanggan)
        ->and(Hash::check('Mahasiswa123!', $user->password))->toBeTrue();

    // Pelanggan tidak boleh membuka panel pusat atau outlet
    $this->actingAs($user)->get('/pusat/dashboard')->assertForbidden();
    $this->actingAs($user)->get('/outlet/dashboard')->assertForbidden();
});

test('KF-01: pusat dapat mendaftarkan outlet baru (belum terverifikasi, royalti 10%), memverifikasi, dan menonaktifkan outlet', function () {
    $pusat = User::factory()->pusat()->create();

    $response = $this->actingAs($pusat)->post('/pusat/outlet', [
        'nama' => 'Ayiriz Bakar Kampus',
        'pemilik' => 'Pak Hendro',
        'email' => 'hendro@ayiriz.local',
        'password' => 'Outlet123!',
        'no_hp' => '0812-0000-1111',
        'alamat' => 'Depan Gerbang Kampus UNG',
        'jam_buka' => '08:00 - 18:00 WITA',
    ]);

    $response->assertRedirect(route('pusat.outlets.index'));

    $outlet = Outlet::where('nama', 'Ayiriz Bakar Kampus')->firstOrFail();
    expect($outlet->status)->toBe(OutletStatus::BelumTerverifikasi)
        ->and((float) $outlet->royalty_percent)->toBe(10.0);

    // Ikuti redirect ke halaman daftar outlet pusat terlebih dahulu agar flash toast dikonsumsi di panel pusat
    $this->actingAs($pusat)->get(route('pusat.outlets.index'))->assertOk();

    // Outlet belum terverifikasi tidak tampil di katalog publik
    $this->get('/')->assertDontSee('Ayiriz Bakar Kampus');
    $this->get('/outlet/' . $outlet->slug)->assertNotFound();

    // Pusat memverifikasi outlet -> tampil di katalog
    $this->actingAs($pusat)->patch(route('pusat.outlets.status', $outlet), [
        'status' => 'terverifikasi',
    ])->assertRedirect();
    $this->actingAs($pusat)->get(route('pusat.outlets.index'))->assertOk();

    expect($outlet->fresh()->status)->toBe(OutletStatus::Terverifikasi);
    $this->get('/')->assertSee('Ayiriz Bakar Kampus');
    $this->get('/outlet/' . $outlet->slug)->assertOk();

    // Pusat menonaktifkan outlet -> hilang dari katalog
    $this->actingAs($pusat)->patch(route('pusat.outlets.status', $outlet), [
        'status' => 'nonaktif',
    ])->assertRedirect();
    $this->actingAs($pusat)->get(route('pusat.outlets.index'))->assertOk();

    $this->get('/')->assertDontSee('Ayiriz Bakar Kampus');
    $this->get('/outlet/' . $outlet->slug)->assertNotFound();
});

test('Stub Tahap 2 & Tahap 3: halaman KF-06, KF-08, KF-09, KF-10 menampilkan informasi belum tersedia di tahap ini', function () {
    $this->seed(DatabaseSeeder::class);

    $pusat = User::where('email', 'pusat@ayiriz.local')->firstOrFail();
    $outletUser = User::where('email', 'outlet1@ayiriz.local')->firstOrFail();

    $this->actingAs($outletUser)->get(route('outlet.stok'))
        ->assertOk()
        ->assertSee('KF-06')
        ->assertSee('Belum Tersedia di Tahap Ini');

    $this->actingAs($outletUser)->get(route('outlet.laporan'))
        ->assertOk()
        ->assertSee('KF-09')
        ->assertSee('Belum Tersedia di Tahap Ini');

    $this->actingAs($pusat)->get(route('pusat.laporan'))
        ->assertOk()
        ->assertSee('KF-08')
        ->assertSee('Belum Tersedia di Tahap Ini');

    $this->actingAs($pusat)->get(route('pusat.audit'))
        ->assertOk()
        ->assertSee('KF-10')
        ->assertSee('Belum Tersedia di Tahap Ini');
});

test('Demo End-to-End Tahap 1: alur lengkap pesan online -> bayar di kasir -> Selesai serta pemeriksaan UI Bahasa Indonesia dan format Rp', function () {
    $this->seed(DatabaseSeeder::class);

    $pelanggan = User::where('email', 'pelanggan1@ayiriz.local')->firstOrFail();
    $outletUser = User::where('email', 'outlet1@ayiriz.local')->firstOrFail();
    $pusat = User::where('email', 'pusat@ayiriz.local')->firstOrFail();

    $outletGeprek = Outlet::where('slug', 'ayiriz-geprek')->firstOrFail();
    $menuGeprek = Menu::where('outlet_id', $outletGeprek->id)
        ->where('nama', 'Ayam Geprek Sambal Korek + Nasi')
        ->firstOrFail();

    $stokAwal = $menuGeprek->stok_porsi;

    // 1. Katalog publik, profil outlet, dan detail menu tampil dengan format Rp dan Bahasa Indonesia
    $this->get('/')
        ->assertOk()
        ->assertSee('Ayiriz.')
        ->assertSee('Data Contoh')
        ->assertSee('Rp 15.000');

    $this->get(route('catalog.outlet', $outletGeprek->slug))
        ->assertOk()
        ->assertSee('Ayiriz Geprek')
        ->assertSee('Rp 15.000');

    $this->get(route('catalog.menu', $menuGeprek))
        ->assertOk()
        ->assertSee('Ayam Geprek Sambal Korek + Nasi')
        ->assertSee('Rp 15.000');

    // 2. Pelanggan tambah 2 porsi ke keranjang lalu checkout
    $this->actingAs($pelanggan)->post(route('cart.items.store'), [
        'menu_id' => $menuGeprek->id,
        'jumlah' => 2,
    ])->assertRedirect();

    $this->actingAs($pelanggan)->get(route('cart.index'))
        ->assertOk()
        ->assertSee('KERANJANG PESANAN')
        ->assertSee('Bayar di Kasir')
        ->assertSee('Rp 30.000');

    $this->actingAs($pelanggan)->post(route('cart.checkout'))
        ->assertRedirect();

    $newOrder = Order::where('user_id', $pelanggan->id)
        ->latest('id')
        ->firstOrFail();

    expect($newOrder->status)->toBe(OrderStatus::Diproses)
        ->and($newOrder->total)->toBe(30000)
        ->and($newOrder->royalty_nominal)->toBe(0)
        ->and($menuGeprek->fresh()->stok_porsi)->toBe($stokAwal - 2);

    // Halaman detail pesanan pelanggan
    $this->actingAs($pelanggan)->get(route('pelanggan.orders.show', $newOrder->kode))
        ->assertOk()
        ->assertSee($newOrder->kode)
        ->assertSee('Diproses')
        ->assertSee('Rp 30.000');

    // 3. Admin Outlet membuka kasir & menyelesaikan pesanan (Diproses -> Selesai)
    $this->actingAs($outletUser)->get(route('outlet.dashboard'))
        ->assertOk()
        ->assertSee('ANTREAN KASIR');

    $this->actingAs($outletUser)->get(route('outlet.orders.show', $newOrder))
        ->assertOk()
        ->assertSee($newOrder->kode)
        ->assertSee('Terima Bayar & Tandai Selesai', false);

    $this->actingAs($outletUser)->patch(route('outlet.orders.complete', $newOrder))
        ->assertRedirect(route('outlet.orders.show', $newOrder));

    $newOrder->refresh();
    expect($newOrder->status)->toBe(OrderStatus::Selesai)
        ->and($newOrder->royalty_nominal)->toBe(3000);

    // Struk kasir menampilkan status Selesai dan Royalti Rp 3.000 (10%)
    $this->actingAs($outletUser)->get(route('outlet.orders.show', $newOrder))
        ->assertOk()
        ->assertSee('Selesai')
        ->assertSee('Rp 30.000')
        ->assertSee('Rp 3.000');

    // 4. Pusat melihat ringkasan penjualan & royalti di dashboard pusat
    $this->actingAs($pusat)->get(route('pusat.dashboard'))
        ->assertOk()
        ->assertSee('DASHBOARD PUSAT AYIRIZ')
        ->assertSee('Ayiriz Geprek');
});

test('KF-02: outlet dapat mengelola menunya sendiri dan tidak bisa mengubah atau menghapus menu outlet lain', function () {
    $cat = Category::create(['nama' => 'Makanan Berat']);

    $userA = User::factory()->outlet()->create();
    $outletA = Outlet::create([
        'user_id' => $userA->id,
        'nama' => 'Outlet A',
        'slug' => 'outlet-a',
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
        'nama' => 'Outlet B',
        'slug' => 'outlet-b',
        'pemilik' => 'Pemilik B',
        'no_hp' => '08222',
        'alamat' => 'Depan UNG B',
        'jam_buka' => '08:00-17:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    $menuB = Menu::create([
        'outlet_id' => $outletB->id,
        'category_id' => $cat->id,
        'nama' => 'Menu Rahasia Outlet B',
        'harga' => 15000,
        'stok_porsi' => 10,
        'tersedia' => true,
    ]);

    // Outlet A menambah menu sendiri
    $this->actingAs($userA)->post('/outlet/menu', [
        'nama' => 'Ayam Geprek Outlet A',
        'category_id' => $cat->id,
        'deskripsi' => 'Geprek pedas',
        'harga' => 14000,
        'stok_porsi' => 20,
        'tersedia' => 1,
    ])->assertRedirect(route('outlet.menus.index'));

    expect(Menu::where('outlet_id', $outletA->id)->where('nama', 'Ayam Geprek Outlet A')->exists())->toBeTrue();

    // Outlet A mencoba mengubah menu milik Outlet B -> 403 Forbidden
    $this->actingAs($userA)->put(route('outlet.menus.update', $menuB), [
        'nama' => 'Dibajak Outlet A',
        'category_id' => $cat->id,
        'harga' => 5000,
        'stok_porsi' => 1,
        'tersedia' => 1,
    ])->assertForbidden();

    // Outlet A mencoba menghapus menu milik Outlet B -> 403 Forbidden
    $this->actingAs($userA)->delete(route('outlet.menus.destroy', $menuB))
        ->assertForbidden();

    expect($menuB->fresh()->nama)->toBe('Menu Rahasia Outlet B');
});

test('KF-03: katalog publik mencari kata geprek dan menyaring berdasarkan kategori serta rentang harga', function () {
    $this->seed(DatabaseSeeder::class);

    // Pencarian "geprek" menemukan menu geprek dan tidak menampilkan Es Pisang Ijo
    $this->get('/?q=geprek')
        ->assertOk()
        ->assertSee('Ayam Geprek Sambal Korek + Nasi')
        ->assertDontSee('Es Pisang Ijo Sirup Merah');

    // Filter rentang harga Rp 5.000 - Rp 7.000
    $this->get('/?min_harga=5000&max_harga=7000')
        ->assertOk()
        ->assertSee('Es Jeruk Peras Segar')
        ->assertDontSee('Ayam Geprek Sambal Roa Khas Gorontalo');
});

test('KF-04: keranjang hanya untuk satu outlet, checkout mengunci harga dan mengurangi stok porsi, serta menolak menu habis', function () {
    $cat = Category::create(['nama' => 'Makanan Berat']);
    $pelanggan = User::factory()->pelanggan()->create();

    $userA = User::factory()->outlet()->create();
    $outletA = Outlet::create([
        'user_id' => $userA->id,
        'nama' => 'Kantin A',
        'slug' => 'kantin-a',
        'pemilik' => 'A',
        'no_hp' => '081',
        'alamat' => 'Gerbang UNG',
        'jam_buka' => '08:00-17:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    $userB = User::factory()->outlet()->create();
    $outletB = Outlet::create([
        'user_id' => $userB->id,
        'nama' => 'Kantin B',
        'slug' => 'kantin-b',
        'pemilik' => 'B',
        'no_hp' => '082',
        'alamat' => 'Gerbang UNG',
        'jam_buka' => '08:00-17:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    $menuA = Menu::create([
        'outlet_id' => $outletA->id,
        'category_id' => $cat->id,
        'nama' => 'Nasi Ayam Geprek A',
        'harga' => 15000,
        'stok_porsi' => 10,
        'tersedia' => true,
    ]);

    $menuHabis = Menu::create([
        'outlet_id' => $outletA->id,
        'category_id' => $cat->id,
        'nama' => 'Menu Habis A',
        'harga' => 10000,
        'stok_porsi' => 0,
        'tersedia' => true,
    ]);

    $menuB = Menu::create([
        'outlet_id' => $outletB->id,
        'category_id' => $cat->id,
        'nama' => 'Es Kopi B',
        'harga' => 12000,
        'stok_porsi' => 10,
        'tersedia' => true,
    ]);

    // Menu habis tidak bisa masuk keranjang
    $this->actingAs($pelanggan)->post('/keranjang/item', [
        'menu_id' => $menuHabis->id,
        'jumlah' => 1,
    ])->assertSessionHasErrors('menu_id');

    // Tambah menu Kantin A ke keranjang
    $this->actingAs($pelanggan)->post('/keranjang/item', [
        'menu_id' => $menuA->id,
        'jumlah' => 2,
    ])->assertSessionHasNoErrors();

    // Mencoba tambah menu Kantin B tanpa konfirmasi replace_outlet memicu cart_conflict
    $this->actingAs($pelanggan)->post('/keranjang/item', [
        'menu_id' => $menuB->id,
        'jumlah' => 1,
    ])->assertSessionHas('cart_conflict');

    // Service menolak bila dipaksa checkout dua outlet berbeda sekaligus
    $orderService = app(OrderService::class);
    expect(fn () => $orderService->checkout($pelanggan, [
        ['menu_id' => $menuA->id, 'jumlah' => 1],
        ['menu_id' => $menuB->id, 'jumlah' => 1],
    ]))->toThrow(ValidationException::class);

    // Checkout keranjang Kantin A (2x 15.000 = 30.000)
    $this->actingAs($pelanggan)->post('/keranjang/checkout')
        ->assertRedirect();

    $order = Order::where('user_id', $pelanggan->id)->firstOrFail();
    expect($order->status)->toBe(OrderStatus::Diproses)
        ->and($order->total)->toBe(30000)
        ->and($order->royalty_nominal)->toBe(0)
        ->and($menuA->fresh()->stok_porsi)->toBe(8);

    // Ubah harga menu setelah checkout -> harga_saat_checkout pada pesanan lama tetap 15.000
    $menuA->update(['harga' => 22000]);
    expect($order->fresh()->total)->toBe(30000)
        ->and($order->items()->first()->harga_saat_checkout)->toBe(15000);
});

test('KF-05: kasir outlet menyelesaikan pesanan (Diproses -> Selesai, royalti 10% tercatat) dan menghapus pesanan Diproses bila pelanggan tidak datang (stok porsi kembali)', function () {
    $cat = Category::create(['nama' => 'Makanan Berat']);
    $pelanggan = User::factory()->pelanggan()->create();

    $userOutlet1 = User::factory()->outlet()->create();
    $outlet1 = Outlet::create([
        'user_id' => $userOutlet1->id,
        'nama' => 'Outlet Kasir 1',
        'slug' => 'outlet-kasir-1',
        'pemilik' => 'Pemilik 1',
        'no_hp' => '081',
        'alamat' => 'Gerbang UNG',
        'jam_buka' => '08:00-17:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    $userOutlet2 = User::factory()->outlet()->create();
    $outlet2 = Outlet::create([
        'user_id' => $userOutlet2->id,
        'nama' => 'Outlet Kasir 2',
        'slug' => 'outlet-kasir-2',
        'pemilik' => 'Pemilik 2',
        'no_hp' => '082',
        'alamat' => 'Gerbang UNG',
        'jam_buka' => '08:00-17:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    $menu = Menu::create([
        'outlet_id' => $outlet1->id,
        'category_id' => $cat->id,
        'nama' => 'Paket Geprek Komplit',
        'harga' => 20000,
        'stok_porsi' => 10,
        'tersedia' => true,
    ]);

    $orderService = app(OrderService::class);

    // Buat Pesanan 1 (2 porsi = Rp 40.000, stok sisa 8)
    $order1 = $orderService->checkout($pelanggan, [
        ['menu_id' => $menu->id, 'jumlah' => 2],
    ]);
    expect($menu->fresh()->stok_porsi)->toBe(8);

    // Outlet 2 tidak boleh menyelesaikan pesanan milik Outlet 1
    $this->actingAs($userOutlet2)
        ->patch(route('outlet.orders.complete', $order1))
        ->assertForbidden();

    // Outlet 1 menerima bayar di kasir & menandai Selesai
    $this->actingAs($userOutlet1)
        ->patch(route('outlet.orders.complete', $order1))
        ->assertRedirect(route('outlet.orders.show', $order1));

    $order1->refresh();
    expect($order1->status)->toBe(OrderStatus::Selesai)
        ->and((float) $order1->royalty_percent_saat_selesai)->toBe(10.0)
        ->and($order1->royalty_nominal)->toBe(4000)
        ->and($order1->selesai_at)->not->toBeNull();

    // Pesanan yang sudah Selesai TIDAK BISA dihapus
    $this->actingAs($userOutlet1)
        ->delete(route('outlet.orders.destroy', $order1))
        ->assertForbidden();

    // Buat Pesanan 2 (3 porsi = Rp 60.000, stok berkurang dari 8 jadi 5)
    $order2 = $orderService->checkout($pelanggan, [
        ['menu_id' => $menu->id, 'jumlah' => 3],
    ]);
    expect($menu->fresh()->stok_porsi)->toBe(5);

    // Pelanggan tidak datang -> Outlet 1 menghapus pesanan Diproses -> stok porsi kembali jadi 8, tanpa royalti
    $this->actingAs($userOutlet1)
        ->delete(route('outlet.orders.destroy', $order2))
        ->assertRedirect(route('outlet.orders.index'));

    expect(Order::find($order2->id))->toBeNull()
        ->and($menu->fresh()->stok_porsi)->toBe(8);
});

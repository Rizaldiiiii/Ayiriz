<?php

use App\Enums\OrderStatus;
use App\Enums\OutletStatus;
use App\Enums\UserRole;
use App\Models\Antrean;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\User;
use App\Services\AntreanService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

test('KN-04 & Model B: enum OrderStatus hanya memiliki tepat dua nilai (diproses, selesai) dan UserRole hanya (pusat, outlet)', function () {
    $orderCases = array_map(fn (OrderStatus $case) => $case->value, OrderStatus::cases());
    $roleCases = array_map(fn (UserRole $case) => $case->value, UserRole::cases());

    expect($orderCases)->toBe(['diproses', 'selesai'])
        ->and($roleCases)->toBe(['pusat', 'outlet']);
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

test('KF-11 (Model B): login hanya untuk pusat dan outlet, tidak ada rute register pelanggan maupun akun pelanggan di seeder', function () {
    $this->seed(DatabaseSeeder::class);

    // Tidak ada rute /register dan tidak ada /pesanan-saya
    $this->get('/register')->assertNotFound();
    $this->post('/register', [])->assertNotFound();
    $this->get('/pesanan-saya')->assertNotFound();

    // Tidak ada akun pelanggan di database
    expect(User::where('email', 'like', 'pelanggan%')->count())->toBe(0);

    // Halaman panel pusat & outlet tetap wajib login
    $this->get('/outlet/dashboard')->assertRedirect('/login');
    $this->get('/pusat/dashboard')->assertRedirect('/login');

    // Keranjang bisa diakses langsung oleh tamu tanpa login
    $this->get('/keranjang')->assertOk();

    // Login pusat & outlet berfungsi dan password ter-hash
    $pusat = User::where('email', 'pusat@ayiriz.local')->firstOrFail();
    $outletUser = User::where('email', 'outlet1@ayiriz.local')->firstOrFail();

    expect(Hash::check('Pusat123!', $pusat->password))->toBeTrue()
        ->and(Hash::check('Outlet123!', $outletUser->password))->toBeTrue();

    // Outlet tidak boleh mengakses panel pusat dan sebaliknya
    $this->actingAs($outletUser)->get('/pusat/dashboard')->assertForbidden();
    $this->actingAs($pusat)->get('/outlet/dashboard')->assertForbidden();
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

test('Stub Tahap 3 & Halaman Tahap 2: seluruh halaman KF-06, KF-07, KF-08, KF-09, dan KF-10 sudah aktif penuh di Tahap 3', function () {
    $this->seed(DatabaseSeeder::class);

    $pusat = User::where('email', 'pusat@ayiriz.local')->firstOrFail();
    $outletUser = User::where('email', 'outlet1@ayiriz.local')->firstOrFail();

    // KF-06, KF-07, KF-09 aktif sejak Tahap 2
    $this->actingAs($outletUser)->get(route('outlet.stok'))
        ->assertOk()
        ->assertSee('MANAJEMEN STOK BAHAN BAKU & RESEP (KF-06)', false);

    $this->get(route('catalog.reaksi', 'AYZ-20261116-0001'))
        ->assertOk()
        ->assertSee('KF-07');

    $this->actingAs($outletUser)->get(route('outlet.laporan'))
        ->assertOk()
        ->assertSee('KF-09');

    // KF-08 dan KF-10 sekarang sudah aktif penuh di Tahap 3 (bukan stub lagi)
    $this->actingAs($pusat)->get(route('pusat.laporan'))
        ->assertOk()
        ->assertSee('LAPORAN & DASHBOARD GABUNGAN PUSAT (KF-08)', false)
        ->assertDontSee('Belum Tersedia di Tahap Ini');

    $this->actingAs($pusat)->get(route('pusat.audit'))
        ->assertOk()
        ->assertSee('AUDIT ROYALTI SEMESTER (KF-10)', false)
        ->assertDontSee('Belum Tersedia di Tahap Ini');
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

test('KF-04 (Model B): tamu tanpa login mengelola keranjang satu outlet, wajib isi nama_pemesan, submit masuk antrean dengan kunci harga_saat_submit TANPA mengurangi stok porsi, dan menolak menu habis', function () {
    $cat = Category::create(['nama' => 'Makanan Berat']);

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

    // Menu habis (stok_porsi = 0) ditolak masuk keranjang dan ditolak submit antrean
    $this->post('/keranjang/item', [
        'menu_id' => $menuHabis->id,
        'jumlah' => 1,
    ])->assertSessionHasErrors('menu_id');

    $antreanService = app(AntreanService::class);
    expect(fn () => $antreanService->submit('Mahasiswa Tamu', [
        ['menu_id' => $menuHabis->id, 'jumlah' => 1],
    ]))->toThrow(ValidationException::class);

    // Tamu tanpa login menambah menu Kantin A ke keranjang
    $this->post('/keranjang/item', [
        'menu_id' => $menuA->id,
        'jumlah' => 2,
    ])->assertSessionHasNoErrors();

    // Mencoba tambah menu Kantin B tanpa konfirmasi replace_outlet memicu cart_conflict
    $this->post('/keranjang/item', [
        'menu_id' => $menuB->id,
        'jumlah' => 1,
    ])->assertSessionHas('cart_conflict');

    // Service menolak bila dipaksa submit dua outlet berbeda sekaligus
    expect(fn () => $antreanService->submit('Mahasiswa Tamu', [
        ['menu_id' => $menuA->id, 'jumlah' => 1],
        ['menu_id' => $menuB->id, 'jumlah' => 1],
    ]))->toThrow(ValidationException::class);

    // Submit tanpa nama_pemesan ditolak validasi
    $this->post('/keranjang/submit', [
        'nama_pemesan' => '',
    ])->assertSessionHasErrors('nama_pemesan');

    // Submit keranjang Kantin A dengan nama_pemesan (2x 15.000 = 30.000)
    $this->post('/keranjang/submit', [
        'nama_pemesan' => 'Aldi Tamu UNG',
    ])->assertRedirect(route('cart.index'));

    // Pastikan masuk ke tabel antrean (BELUM menjadi Order) dan stok_porsi TIDAK berkurang (tetap 10)!
    expect(Order::count())->toBe(0);
    $antrean = Antrean::where('nama_pemesan', 'Aldi Tamu UNG')->firstOrFail();
    expect($antrean->total)->toBe(30000)
        ->and($antrean->kode)->toStartWith('AYZ-')
        ->and($menuA->fresh()->stok_porsi)->toBe(10);

    // Ubah harga menu setelah submit -> harga_saat_submit pada antrean tetap 15.000
    $menuA->update(['harga' => 22000]);
    expect($antrean->fresh()->total)->toBe(30000)
        ->and($antrean->items()->first()->harga_saat_submit)->toBe(15000);
});

test('KF-05 (Model B): kasir mengonfirmasi bayar tunai (antrean -> pesanan Diproses + stok porsi berkurang), menolak konfirmasi bila stok habis, menghapus antrean tanpa efek stok, menandai Selesai (+ royalti 10%), dan pesanan sudah dibayar tidak bisa dihapus', function () {
    $cat = Category::create(['nama' => 'Makanan Berat']);

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

    $antreanService = app(AntreanService::class);

    // 1. Tamu submit Antrean 1 (2 porsi = Rp 40.000) -> stok porsi tetap 10
    $antrean1 = $antreanService->submit('Budi Tamu', [
        ['menu_id' => $menu->id, 'jumlah' => 2],
    ]);
    $kode1 = $antrean1->kode;
    expect($menu->fresh()->stok_porsi)->toBe(10);

    // Outlet 2 tidak boleh mengonfirmasi atau menghapus antrean milik Outlet 1
    $this->actingAs($userOutlet2)
        ->post(route('outlet.antrean.confirm', $antrean1))
        ->assertForbidden();
    $this->actingAs($userOutlet2)
        ->delete(route('outlet.antrean.destroy', $antrean1))
        ->assertForbidden();

    // 2. Kasir Outlet 1 mengonfirmasi bayar tunai Antrean 1 -> lahir Order status Diproses dengan kode sama, stok porsi berkurang jadi 8, antrean terhapus
    $this->actingAs($userOutlet1)
        ->post(route('outlet.antrean.confirm', $antrean1))
        ->assertRedirect();

    expect(Antrean::find($antrean1->id))->toBeNull()
        ->and($menu->fresh()->stok_porsi)->toBe(8);

    $order1 = Order::where('kode', $kode1)->firstOrFail();
    expect($order1->status)->toBe(OrderStatus::Diproses)
        ->and($order1->nama_pemesan)->toBe('Budi Tamu')
        ->and($order1->total)->toBe(40000)
        ->and($order1->royalty_nominal)->toBe(0)
        ->and($order1->items()->first()->harga_saat_submit)->toBe(20000);

    // Pesanan yang sudah dibayar (Diproses maupun Selesai) TIDAK BISA dihapus (rute DELETE /outlet/pesanan/{order} tidak ada / 405 Method Not Allowed)
    $this->actingAs($userOutlet1)
        ->delete('/outlet/pesanan/' . $order1->id)
        ->assertStatus(405);

    // 3. Kasir Outlet 1 menandai pesanan Selesai saat diambil pelanggan -> royalti 10% (Rp 4.000) tercatat
    $this->actingAs($userOutlet1)
        ->patch(route('outlet.orders.complete', $order1))
        ->assertRedirect(route('outlet.orders.show', $order1));

    $order1->refresh();
    expect($order1->status)->toBe(OrderStatus::Selesai)
        ->and((float) $order1->royalty_percent_saat_selesai)->toBe(10.0)
        ->and($order1->royalty_nominal)->toBe(4000)
        ->and($order1->selesai_at)->not->toBeNull();

    // 4. Tamu submit Antrean 2 (3 porsi) lalu tidak datang -> Kasir menghapus Antrean 2 -> stok porsi tetap 8 (tidak berubah)
    $antrean2 = $antreanService->submit('Tamu Tidak Datang', [
        ['menu_id' => $menu->id, 'jumlah' => 3],
    ]);
    expect($menu->fresh()->stok_porsi)->toBe(8);

    $this->actingAs($userOutlet1)
        ->delete(route('outlet.antrean.destroy', $antrean2))
        ->assertRedirect(route('outlet.orders.index'));

    expect(Antrean::find($antrean2->id))->toBeNull()
        ->and($menu->fresh()->stok_porsi)->toBe(8);

    // 5. Kalau stok porsi habis di antara submit dan bayar, konfirmasi bayar ditolak dan antrean tetap ada
    $antrean3 = $antreanService->submit('Tamu Kehabisan Stok', [
        ['menu_id' => $menu->id, 'jumlah' => 2],
    ]);
    $menu->update(['stok_porsi' => 1]); // Tersisa 1 porsi padahal antrean butuh 2 porsi

    $this->actingAs($userOutlet1)
        ->from(route('outlet.antrean.show', $antrean3))
        ->post(route('outlet.antrean.confirm', $antrean3))
        ->assertRedirect(route('outlet.antrean.show', $antrean3))
        ->assertSessionHasErrors('stok_porsi');

    expect(Antrean::find($antrean3->id))->not->toBeNull()
        ->and(Order::where('kode', $antrean3->kode)->exists())->toBeFalse();
});

test('Demo End-to-End Tahap 1 Model B: alur lengkap tamu pesan -> masuk antrean -> kasir konfirmasi bayar tunai -> Diproses -> Selesai serta pemeriksaan UI Bahasa Indonesia dan format Rp', function () {
    $this->seed(DatabaseSeeder::class);

    $outletUser = User::where('email', 'outlet1@ayiriz.local')->firstOrFail();
    $pusat = User::where('email', 'pusat@ayiriz.local')->firstOrFail();

    $outletGeprek = Outlet::where('slug', 'ayiriz-geprek')->firstOrFail();
    $menuGeprek = Menu::where('outlet_id', $outletGeprek->id)
        ->where('nama', 'Ayam Geprek Sambal Korek + Nasi')
        ->firstOrFail();

    $stokAwal = $menuGeprek->stok_porsi;

    // 1. Katalog publik, profil outlet, dan detail menu tampil tanpa login dengan format Rp dan Bahasa Indonesia
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

    // 2. Pelanggan tamu (tanpa login) menambah 2 porsi ke keranjang lalu submit ke antrean dengan nama_pemesan
    $this->post(route('cart.items.store'), [
        'menu_id' => $menuGeprek->id,
        'jumlah' => 2,
    ])->assertRedirect();

    $this->get(route('cart.index'))
        ->assertOk()
        ->assertSee('KERANJANG & DAFTAR TUNGGU (ANTREAN)', false)
        ->assertSee('Bayar Tunai di Kasir')
        ->assertSee('Rp 30.000');

    $this->post(route('cart.submit'), [
        'nama_pemesan' => 'Mahasiswa Demo UNG',
    ])->assertRedirect(route('cart.index'));

    $antrean = Antrean::where('nama_pemesan', 'Mahasiswa Demo UNG')->firstOrFail();
    $kodeAntrean = $antrean->kode;

    // Stok porsi belum berkurang saat masih di antrean
    expect($menuGeprek->fresh()->stok_porsi)->toBe($stokAwal);

    // Halaman keranjang menampilkan bukti Kode Antrean, Nama Pemesan, dan Total
    $this->get(route('cart.index'))
        ->assertOk()
        ->assertSee($kodeAntrean)
        ->assertSee('Mahasiswa Demo UNG')
        ->assertSee('Rp 30.000');

    // 3. Admin Outlet melihat antrean di kasir & mengonfirmasi pembayaran tunai
    $this->actingAs($outletUser)->get(route('outlet.orders.index'))
        ->assertOk()
        ->assertSee($kodeAntrean)
        ->assertSee('Mahasiswa Demo UNG');

    $this->actingAs($outletUser)->get(route('outlet.antrean.show', $antrean))
        ->assertOk()
        ->assertSee($kodeAntrean)
        ->assertSee('Konfirmasi Bayar Tunai', false);

    $this->actingAs($outletUser)->post(route('outlet.antrean.confirm', $antrean))
        ->assertRedirect();

    // Antrean sudah menjadi Order berstatus Diproses dan stok porsi berkurang 2
    $newOrder = Order::where('kode', $kodeAntrean)->firstOrFail();
    expect($newOrder->status)->toBe(OrderStatus::Diproses)
        ->and($newOrder->nama_pemesan)->toBe('Mahasiswa Demo UNG')
        ->and($newOrder->total)->toBe(30000)
        ->and($newOrder->royalty_nominal)->toBe(0)
        ->and($menuGeprek->fresh()->stok_porsi)->toBe($stokAwal - 2);

    // 4. Admin Outlet melihat struk + QR Reaksi lalu menandai pesanan Selesai saat diambil pelanggan
    $this->actingAs($outletUser)->get(route('outlet.orders.show', $newOrder))
        ->assertOk()
        ->assertSee($newOrder->kode)
        ->assertSee('QR Reaksi Emote Pelanggan', false)
        ->assertSee('Tandai Selesai (Diambil Pelanggan)', false);

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

    // 5. Pusat melihat ringkasan penjualan & royalti di dashboard pusat
    $this->actingAs($pusat)->get(route('pusat.dashboard'))
        ->assertOk()
        ->assertSee('DASHBOARD PUSAT AYIRIZ')
        ->assertSee('Ayiriz Geprek');
});

<?php

use App\Enums\OrderStatus;
use App\Enums\OutletStatus;
use App\Enums\ReactionEmote;
use App\Enums\RoyaltyDecision;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Reaction;
use App\Models\RoyaltyAudit;
use App\Models\RoyaltyHistory;
use App\Models\User;
use App\Services\AntreanService;
use App\Services\OrderService;

test('KF-08: laporan dan dashboard gabungan pusat menghitung total penjualan & royalti HANYA dari pesanan Selesai semua outlet per periode, menampilkan menu terlaris PER OUTLET, menolak akses outlet, dan tanpa perbandingan/ranking reaksi', function () {
    $pusat = User::factory()->pusat()->create();
    $cat = Category::create(['nama' => 'Makanan Kampus']);

    $userA = User::factory()->outlet()->create();
    $outletA = Outlet::create([
        'user_id' => $userA->id,
        'nama' => 'Kantin Pusat Uji A',
        'slug' => 'kantin-pusat-uji-a',
        'pemilik' => 'Pemilik A',
        'no_hp' => '08111',
        'alamat' => 'Gerbang UNG A',
        'jam_buka' => '08:00-17:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    $userB = User::factory()->outlet()->create();
    $outletB = Outlet::create([
        'user_id' => $userB->id,
        'nama' => 'Kantin Pusat Uji B',
        'slug' => 'kantin-pusat-uji-b',
        'pemilik' => 'Pemilik B',
        'no_hp' => '08222',
        'alamat' => 'Gerbang UNG B',
        'jam_buka' => '08:00-17:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    $menuA1 = Menu::create([
        'outlet_id' => $outletA->id,
        'category_id' => $cat->id,
        'nama' => 'Ayam Bakar Spesial A',
        'harga' => 20000,
        'stok_porsi' => 30,
        'tersedia' => true,
    ]);

    $menuB1 = Menu::create([
        'outlet_id' => $outletB->id,
        'category_id' => $cat->id,
        'nama' => 'Mie Cakalang Spesial B',
        'harga' => 25000,
        'stok_porsi' => 30,
        'tersedia' => true,
    ]);

    $antreanService = app(AntreanService::class);
    $orderService = app(OrderService::class);

    // 1. Pesanan Selesai di Kantin A: 3 x 20.000 = Rp 60.000 (Royalti 10% = Rp 6.000)
    $antreanA1 = $antreanService->submit('Mahasiswa A1', [['menu_id' => $menuA1->id, 'jumlah' => 3]]);
    $orderA1 = $antreanService->confirmCashPayment($antreanA1, $userA);
    $orderService->completeOrder($orderA1, $userA);

    // 2. Pesanan Diproses (belum Selesai) di Kantin A: 2 x 20.000 = Rp 40.000 (TIDAK boleh masuk hitungan penjualan Selesai maupun royalti)
    $antreanA2 = $antreanService->submit('Mahasiswa A2', [['menu_id' => $menuA1->id, 'jumlah' => 2]]);
    $orderA2 = $antreanService->confirmCashPayment($antreanA2, $userA);
    expect($orderA2->status)->toBe(OrderStatus::Diproses);

    // 3. Pesanan Selesai di Kantin B: 2 x 25.000 = Rp 50.000 (Royalti 10% = Rp 5.000)
    $antreanB1 = $antreanService->submit('Mahasiswa B1', [['menu_id' => $menuB1->id, 'jumlah' => 2]]);
    $orderB1 = $antreanService->confirmCashPayment($antreanB1, $userB);
    $orderService->completeOrder($orderB1, $userB);

    // Tambahkan reaksi emote ke pesanan untuk memastikan laporan pusat TIDAK menampilkan perbandingan reaksi
    Reaction::create([
        'order_id' => $orderA1->id,
        'menu_id' => $menuA1->id,
        'emote' => ReactionEmote::Enak,
    ]);

    // Role outlet ditolak mengakses laporan pusat (403 Forbidden)
    $this->actingAs($userA)->get(route('pusat.laporan'))->assertForbidden();

    // Pusat membuka laporan gabungan untuk semua periode / harian / semester
    // Total penjualan gabungan Selesai = 60.000 + 50.000 = Rp 110.000 (bukan 150.000)
    // Total royalti gabungan Selesai = 6.000 + 5.000 = Rp 11.000
    foreach (['harian', 'mingguan', 'bulanan', 'semester'] as $periode) {
        $response = $this->actingAs($pusat)->get(route('pusat.laporan', ['periode' => $periode]));

        $response->assertOk()
            ->assertSee('LAPORAN & DASHBOARD GABUNGAN PUSAT (KF-08)', false)
            ->assertSee('Rp 110.000')
            ->assertSee('Rp 11.000')
            ->assertSee('Kantin Pusat Uji A')
            ->assertSee('Rp 60.000')
            ->assertSee('Rp 6.000')
            ->assertSee('Kantin Pusat Uji B')
            ->assertSee('Rp 50.000')
            ->assertSee('Rp 5.000')
            ->assertSee('MENU TERLARIS PER OUTLET (PESANAN SELESAI)', false)
            ->assertSee('Ayam Bakar Spesial A')
            ->assertSee('3 Porsi')
            ->assertSee('Mie Cakalang Spesial B')
            ->assertSee('2 Porsi')
            ->assertDontSee('Rp 150.000')
            ->assertDontSee('😋')
            ->assertDontSee('🥵')
            ->assertDontSee('🫠');
    }
});

test('KF-10: audit royalti semester mencatat keputusan Tetap (tanpa baris royalty_histories) dan Naik (+ baris royalty_histories), menolak duplikasi (outlet_id, semester), dan kenaikan royalti hanya berlaku pada pesanan Selesai berikutnya tanpa mengubah royalti pesanan lama', function () {
    $pusat = User::factory()->pusat()->create();
    $cat = Category::create(['nama' => 'Makanan Audit']);

    $userOutlet = User::factory()->outlet()->create();
    $outlet = Outlet::create([
        'user_id' => $userOutlet->id,
        'nama' => 'Kantin Audit UNG',
        'slug' => 'kantin-audit-ung',
        'pemilik' => 'Bang Audit',
        'no_hp' => '0812345678',
        'alamat' => 'Gerbang UNG',
        'jam_buka' => '08:00-18:00',
        'status' => OutletStatus::Terverifikasi,
        'royalty_percent' => 10.00,
    ]);

    $menu = Menu::create([
        'outlet_id' => $outlet->id,
        'category_id' => $cat->id,
        'nama' => 'Nasi Goreng Kampus',
        'harga' => 20000,
        'stok_porsi' => 50,
        'tersedia' => true,
    ]);

    $antreanService = app(AntreanService::class);
    $orderService = app(OrderService::class);

    // 1. Buat pesanan lama yang Selesai sebelum audit: 5 x 20.000 = Rp 100.000 -> royalti 10% = Rp 10.000
    $antreanLama = $antreanService->submit('Pemesan Lama', [['menu_id' => $menu->id, 'jumlah' => 5]]);
    $orderLama = $antreanService->confirmCashPayment($antreanLama, $userOutlet);
    $orderLama = $orderService->completeOrder($orderLama, $userOutlet);

    expect($orderLama->status)->toBe(OrderStatus::Selesai)
        ->and((float) $orderLama->royalty_percent_saat_selesai)->toBe(10.00)
        ->and($orderLama->royalty_nominal)->toBe(10000);

    // Role outlet tidak boleh membuka atau mengirim audit royalti (403 Forbidden)
    $this->actingAs($userOutlet)->get(route('pusat.audit'))->assertForbidden();
    $this->actingAs($userOutlet)->post(route('pusat.audit.store'), [
        'outlet_id' => $outlet->id,
        'semester' => '2025/2026 Ganjil',
        'keputusan' => 'tetap',
    ])->assertForbidden();

    // 2. Pusat membuat audit semester "2025/2026 Ganjil" dengan keputusan TETAP
    $this->actingAs($pusat)->post(route('pusat.audit.store'), [
        'outlet_id' => $outlet->id,
        'semester' => '2025/2026 Ganjil',
        'keputusan' => 'tetap',
        'catatan' => 'Penjualan stabil, royalti tetap 10%.',
    ])->assertRedirect();

    $auditTetap = RoyaltyAudit::where('outlet_id', $outlet->id)
        ->where('semester', '2025/2026 Ganjil')
        ->firstOrFail();

    expect($auditTetap->keputusan)->toBe(RoyaltyDecision::Tetap)
        ->and((float) $auditTetap->percent_baru)->toBe(10.00)
        ->and((float) $outlet->fresh()->royalty_percent)->toBe(10.00)
        // Keputusan Tetap TIDAK menambah baris di royalty_histories (ERD §2.7)
        ->and(RoyaltyHistory::where('outlet_id', $outlet->id)->count())->toBe(0);

    // 3. Audit semester yang sama ("2025/2026 Ganjil") untuk outlet yang sama TIDAK BISA dibuat dua kali
    $this->actingAs($pusat)
        ->from(route('pusat.audit'))
        ->post(route('pusat.audit.store'), [
            'outlet_id' => $outlet->id,
            'semester' => '2025/2026 Ganjil',
            'keputusan' => 'naik',
            'percent_baru' => 12.00,
            'catatan' => 'Percobaan audit ganda semester sama',
        ])
        ->assertRedirect(route('pusat.audit'))
        ->assertSessionHasErrors('semester');

    expect(RoyaltyAudit::where('outlet_id', $outlet->id)->where('semester', '2025/2026 Ganjil')->count())->toBe(1);

    // 4. Keputusan Naik tanpa alasan (catatan kosong) atau persen baru <= persen lama ditolak validasi
    $this->actingAs($pusat)
        ->from(route('pusat.audit'))
        ->post(route('pusat.audit.store'), [
            'outlet_id' => $outlet->id,
            'semester' => '2026/2027 Ganjil',
            'keputusan' => 'naik',
            'percent_baru' => 12.50,
            'catatan' => '',
        ])
        ->assertSessionHasErrors('catatan');

    $this->actingAs($pusat)
        ->from(route('pusat.audit'))
        ->post(route('pusat.audit.store'), [
            'outlet_id' => $outlet->id,
            'semester' => '2026/2027 Ganjil',
            'keputusan' => 'naik',
            'percent_baru' => 10.00,
            'catatan' => 'Persen tidak naik padahal memilih Naik',
        ])
        ->assertSessionHasErrors('percent_baru');

    // 5. Pusat membuat audit semester berjalan ("2026/2027 Ganjil") dengan keputusan NAIK menjadi 12.50% + alasan
    $this->actingAs($pusat)->post(route('pusat.audit.store'), [
        'outlet_id' => $outlet->id,
        'semester' => '2026/2027 Ganjil',
        'keputusan' => 'naik',
        'percent_baru' => 12.50,
        'catatan' => 'Omzet semester ganjil melampaui target, bagi hasil naik menjadi 12,5%.',
    ])->assertRedirect();

    $auditNaik = RoyaltyAudit::where('outlet_id', $outlet->id)
        ->where('semester', '2026/2027 Ganjil')
        ->firstOrFail();

    expect($auditNaik->keputusan)->toBe(RoyaltyDecision::Naik)
        ->and($auditNaik->total_penjualan)->toBe(100000)
        ->and($auditNaik->total_royalti_terhitung)->toBe(10000)
        ->and((float) $auditNaik->percent_baru)->toBe(12.50)
        ->and((float) $outlet->fresh()->royalty_percent)->toBe(12.50);

    // Keputusan Naik HARUS diikuti baris baru di royalty_histories
    $history = RoyaltyHistory::where('outlet_id', $outlet->id)->firstOrFail();
    expect((float) $history->percent_lama)->toBe(10.00)
        ->and((float) $history->percent_baru)->toBe(12.50)
        ->and($history->alasan)->toContain('Omzet semester ganjil melampaui target')
        ->and((int) $history->diubah_oleh)->toBe((int) $pusat->id);

    // 6. Pastikan pesanan lama yang sudah Selesai TIDAK berubah royaltinya (tetap 10% dan Rp 10.000)
    $orderLama->refresh();
    expect((float) $orderLama->royalty_percent_saat_selesai)->toBe(10.00)
        ->and($orderLama->royalty_nominal)->toBe(10000);

    // 7. Buat pesanan baru dan selesaikan sesudah audit -> memakai persen royalti baru 12.50% (Rp 100.000 -> Rp 12.500)
    $antreanBaru = $antreanService->submit('Pemesan Baru Sesudah Audit', [['menu_id' => $menu->id, 'jumlah' => 5]]);
    $orderBaru = $antreanService->confirmCashPayment($antreanBaru, $userOutlet);
    $orderBaru = $orderService->completeOrder($orderBaru, $userOutlet);

    expect($orderBaru->status)->toBe(OrderStatus::Selesai)
        ->and((float) $orderBaru->royalty_percent_saat_selesai)->toBe(12.50)
        ->and($orderBaru->royalty_nominal)->toBe(12500);

    // Halaman audit pusat menampilkan riwayat perubahan royalti dan daftar audit
    $this->actingAs($pusat)->get(route('pusat.audit'))
        ->assertOk()
        ->assertSee('Kantin Audit UNG')
        ->assertSee('2026/2027 Ganjil')
        ->assertSee('12,50%')
        ->assertSee('Omzet semester ganjil melampaui target');
});

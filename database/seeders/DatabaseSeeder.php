<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\OutletStatus;
use App\Enums\ReactionEmote;
use App\Enums\RoyaltyDecision;
use App\Enums\StockMovementType;
use App\Enums\UserRole;
use App\Models\Antrean;
use App\Models\AntreanItem;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\MenuIngredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\Reaction;
use App\Models\RoyaltyAudit;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database (Data Contoh Fiktif — PRD v1.1 §6 & ERD v1.1 §4, Model B Tahap 1 & Tahap 2).
     * Tanpa akun pelanggan — pelanggan adalah tamu.
     */
    public function run(): void
    {
        // 1. Akun Pusat Ayiriz
        $pusatUser = User::updateOrCreate(
            ['email' => 'pusat@ayiriz.local'],
            [
                'name' => 'Pusat Ayiriz (Owner Brand)',
                'password' => Hash::make('Pusat123!'),
                'role' => UserRole::Pusat,
            ]
        );

        // 2. Kategori Menu Global (ERD §2.3)
        $catMakananBerat = Category::updateOrCreate(['nama' => 'Makanan Berat']);
        $catMinuman = Category::updateOrCreate(['nama' => 'Minuman']);
        $catCemilan = Category::updateOrCreate(['nama' => 'Cemilan & Dessert']);
        $catPaket = Category::updateOrCreate(['nama' => 'Paket Anak Kuliahan']);

        // 3. Akun & Data 4 Outlet Contoh di Depan Gerbang Kampus UNG (3 Terverifikasi + 1 Belum Terverifikasi)
        $outletsData = [
            [
                'email' => 'outlet1@ayiriz.local',
                'user_name' => 'Bang Hendra (Ayiriz Geprek)',
                'nama' => 'Ayiriz Geprek',
                'slug' => 'ayiriz-geprek',
                'pemilik' => 'Hendra Saputra',
                'no_hp' => '0812-4310-1001',
                'alamat' => 'Kantin Blok A1 Depan Gerbang Utama UNG, Jl. Jend. Sudirman',
                'jam_buka' => 'Senin–Sabtu, 08:00–20:00 WITA',
                'status' => OutletStatus::Terverifikasi,
                'menus' => [
                    ['nama' => 'Ayam Geprek Sambal Korek + Nasi', 'category_id' => $catMakananBerat->id, 'harga' => 15000, 'stok_porsi' => 35, 'tersedia' => true, 'deskripsi' => 'Ayam krispi geprek hangat dengan sambal korek bawang segar dan nasi putih pulen.'],
                    ['nama' => 'Ayam Geprek Sambal Roa Khas Gorontalo', 'category_id' => $catMakananBerat->id, 'harga' => 18000, 'stok_porsi' => 25, 'tersedia' => true, 'deskripsi' => 'Perpaduan ayam geprek renyah dengan sambal ikan roa asap khas Gorontalo.'],
                    ['nama' => 'Paket Hemat Geprek + Es Teh Manis', 'category_id' => $catPaket->id, 'harga' => 17000, 'stok_porsi' => 30, 'tersedia' => true, 'deskripsi' => 'Paket kenyang anak kuliahan: nasi ayam geprek level 1–5 lengkap dengan es teh manis jumbo.'],
                    ['nama' => 'Mie Goreng Geprek Telur Ceplok', 'category_id' => $catMakananBerat->id, 'harga' => 16000, 'stok_porsi' => 20, 'tersedia' => true, 'deskripsi' => 'Mie goreng rempah gurih dengan potongan ayam geprek dan telur mata sapi.'],
                    ['nama' => 'Tahu Tempe Crispy Sambal Bawang', 'category_id' => $catCemilan->id, 'harga' => 8000, 'stok_porsi' => 40, 'tersedia' => true, 'deskripsi' => 'Cemilan tahu dan tempe tepung renyah dengan cocolan sambal bawang.'],
                    ['nama' => 'Es Jeruk Peras Segar', 'category_id' => $catMinuman->id, 'harga' => 6000, 'stok_porsi' => 50, 'tersedia' => true, 'deskripsi' => 'Perasan jeruk manis segar dengan es batu, cocok penawar pedas.'],
                ],
                'ingredients' => [
                    ['nama' => 'Daging Ayam Potong', 'satuan' => 'kg', 'stok' => 12.50, 'batas_minimum' => 4.00],
                    ['nama' => 'Beras Pulen', 'satuan' => 'kg', 'stok' => 18.00, 'batas_minimum' => 5.00],
                    ['nama' => 'Cabai Rawit Merah', 'satuan' => 'kg', 'stok' => 1.50, 'batas_minimum' => 2.00], // Menipis!
                ],
                'recipes' => [
                    // Menu 0: Ayam Geprek Sambal Korek + Nasi
                    0 => [
                        ['ingredient_index' => 0, 'takaran' => 0.15],
                        ['ingredient_index' => 1, 'takaran' => 0.12],
                        ['ingredient_index' => 2, 'takaran' => 0.03],
                    ],
                    // Menu 1: Ayam Geprek Sambal Roa
                    1 => [
                        ['ingredient_index' => 0, 'takaran' => 0.15],
                        ['ingredient_index' => 1, 'takaran' => 0.12],
                    ],
                ],
            ],
            [
                'email' => 'outlet2@ayiriz.local',
                'user_name' => 'Kak Nanda (Ayiriz Kopi & Roti)',
                'nama' => 'Ayiriz Kopi & Roti',
                'slug' => 'ayiriz-kopi-roti',
                'pemilik' => 'Nanda Pratama',
                'no_hp' => '0812-4310-1002',
                'alamat' => 'Kantin Blok B2 Seberang Gerbang Kampus UNG',
                'jam_buka' => 'Senin–Sabtu, 07:30–21:00 WITA',
                'status' => OutletStatus::Terverifikasi,
                'menus' => [
                    ['nama' => 'Es Kopi Susu Gula Aren Kampus', 'category_id' => $catMinuman->id, 'harga' => 12000, 'stok_porsi' => 40, 'tersedia' => true, 'deskripsi' => 'Espresso house blend dengan susu segar dan gula aren asli teman nugas kuliah.'],
                    ['nama' => 'Roti Bakar Cokelat Keju Lumer', 'category_id' => $catCemilan->id, 'harga' => 13000, 'stok_porsi' => 25, 'tersedia' => true, 'deskripsi' => 'Roti tawar tebal panggang mentega dengan isian cokelat dan parutan keju melimpah.'],
                    ['nama' => 'Roti Bakar Srikaya Pandan', 'category_id' => $catCemilan->id, 'harga' => 10000, 'stok_porsi' => 20, 'tersedia' => true, 'deskripsi' => 'Roti bakar lembut dengan selai srikaya pandan buatan sendiri.'],
                    ['nama' => 'Paket Ngampus (Es Kopi Aren + Roti Bakar)', 'category_id' => $catPaket->id, 'harga' => 20000, 'stok_porsi' => 18, 'tersedia' => true, 'deskripsi' => 'Kombinasi hemat es kopi susu gula aren dan roti bakar pilihan.'],
                    ['nama' => 'Matcha Latte Dingin', 'category_id' => $catMinuman->id, 'harga' => 14000, 'stok_porsi' => 22, 'tersedia' => true, 'deskripsi' => 'Minuman teh hijau Jepang creamy dengan susu segar dingin.'],
                    ['nama' => 'Kentang Goreng Bumbu Balado', 'category_id' => $catCemilan->id, 'harga' => 11000, 'stok_porsi' => 15, 'tersedia' => true, 'deskripsi' => 'Kentang goreng renyah tabur bumbu balado manis pedas.'],
                ],
                'ingredients' => [
                    ['nama' => 'Biji Kopi House Blend', 'satuan' => 'kg', 'stok' => 4.50, 'batas_minimum' => 1.50],
                    ['nama' => 'Susu Segar Full Cream', 'satuan' => 'liter', 'stok' => 2.50, 'batas_minimum' => 3.00], // Menipis!
                    ['nama' => 'Roti Tawar Tebal', 'satuan' => 'pcs', 'stok' => 30.00, 'batas_minimum' => 10.00],
                ],
                'recipes' => [
                    // Menu 0: Es Kopi Susu Gula Aren Kampus
                    0 => [
                        ['ingredient_index' => 0, 'takaran' => 0.02],
                        ['ingredient_index' => 1, 'takaran' => 0.15],
                    ],
                    // Menu 1: Roti Bakar Cokelat Keju Lumer
                    1 => [
                        ['ingredient_index' => 2, 'takaran' => 1.00],
                    ],
                ],
            ],
            [
                'email' => 'outlet3@ayiriz.local',
                'user_name' => 'Ibu Dewi (Ayiriz Es & Dessert)',
                'nama' => 'Ayiriz Es & Dessert',
                'slug' => 'ayiriz-es-dessert',
                'pemilik' => 'Dewi Maharani',
                'no_hp' => '0812-4310-1003',
                'alamat' => 'Kantin Blok C1 Samping Gerbang Fakultas Teknik UNG',
                'jam_buka' => 'Senin–Sabtu, 09:00–18:00 WITA',
                'status' => OutletStatus::Terverifikasi,
                'menus' => [
                    ['nama' => 'Es Pisang Ijo Sirup Merah', 'category_id' => $catCemilan->id, 'harga' => 12000, 'stok_porsi' => 30, 'tersedia' => true, 'deskripsi' => 'Pisang raja balut adonan pandan hijau dengan bubur sumsum gurih dan sirup merah dingin.'],
                    ['nama' => 'Es Brenebon Kacang Merah Susu', 'category_id' => $catMinuman->id, 'harga' => 13000, 'stok_porsi' => 25, 'tersedia' => true, 'deskripsi' => 'Es kacang merah empuk khas Sulawesi dengan susu kental manis cokelat dan es serut.'],
                    ['nama' => 'Salad Buah Keju Parut Cup Besar', 'category_id' => $catCemilan->id, 'harga' => 15000, 'stok_porsi' => 20, 'tersedia' => true, 'deskripsi' => 'Potongan buah segar saus yogurt manis dengan taburan keju parut penuh.'],
                    ['nama' => 'Puding Mangga Vla Susu', 'category_id' => $catCemilan->id, 'harga' => 8000, 'stok_porsi' => 28, 'tersedia' => true, 'deskripsi' => 'Puding mangga dingin lembut disiram vla susu vanila.'],
                    ['nama' => 'Es Cincau Hijau Gula Merah', 'category_id' => $catMinuman->id, 'harga' => 7000, 'stok_porsi' => 35, 'tersedia' => true, 'deskripsi' => 'Minuman cincau hijau segar dengan santan ringan dan gula aren cair.'],
                    ['nama' => 'Es Teh Lemon Madu', 'category_id' => $catMinuman->id, 'harga' => 5000, 'stok_porsi' => 45, 'tersedia' => true, 'deskripsi' => 'Es teh segar perasan lemon asli dengan sentuhan madu.'],
                ],
                'ingredients' => [
                    ['nama' => 'Pisang Raja Matang', 'satuan' => 'kg', 'stok' => 8.00, 'batas_minimum' => 2.50],
                    ['nama' => 'Sirup Merah Coco Pandan', 'satuan' => 'liter', 'stok' => 1.20, 'batas_minimum' => 1.50], // Menipis!
                    ['nama' => 'Kacang Merah Brenebon', 'satuan' => 'kg', 'stok' => 5.00, 'batas_minimum' => 1.50],
                ],
                'recipes' => [
                    // Menu 0: Es Pisang Ijo Sirup Merah
                    0 => [
                        ['ingredient_index' => 0, 'takaran' => 0.15],
                        ['ingredient_index' => 1, 'takaran' => 0.04],
                    ],
                    // Menu 1: Es Brenebon Kacang Merah Susu
                    1 => [
                        ['ingredient_index' => 2, 'takaran' => 0.10],
                    ],
                ],
            ],
            [
                'email' => 'outlet4@ayiriz.local',
                'user_name' => 'Pak Rudi (Ayiriz Mie & Dimsum)',
                'nama' => 'Ayiriz Mie & Dimsum',
                'slug' => 'ayiriz-mie-dimsum',
                'pemilik' => 'Rudi Hartono',
                'no_hp' => '0812-4310-1004',
                'alamat' => 'Kantin Blok D3 Depan Gerbang Barat UNG',
                'jam_buka' => 'Senin–Sabtu, 09:00–19:00 WITA',
                'status' => OutletStatus::BelumTerverifikasi,
                'menus' => [
                    ['nama' => 'Mie Pedas Kampus Level 1', 'category_id' => $catMakananBerat->id, 'harga' => 12000, 'stok_porsi' => 30, 'tersedia' => true, 'deskripsi' => 'Mie kenyal pedas gurih dengan taburan ayam cincang dan pangsit goreng.'],
                    ['nama' => 'Dimsum Ayam Udang (Isi 4)', 'category_id' => $catCemilan->id, 'harga' => 14000, 'stok_porsi' => 25, 'tersedia' => true, 'deskripsi' => 'Dimsum kukus hangat daging ayam dan udang dengan saus cabai.'],
                    ['nama' => 'Pangsit Kuah Kaldu Ayam', 'category_id' => $catMakananBerat->id, 'harga' => 13000, 'stok_porsi' => 20, 'tersedia' => true, 'deskripsi' => 'Pangsit lembut kuah kaldu ayam hangat dan daun bawang.'],
                    ['nama' => 'Lumpia Kulit Tahu Goreng', 'category_id' => $catCemilan->id, 'harga' => 10000, 'stok_porsi' => 25, 'tersedia' => true, 'deskripsi' => 'Lumpia isi ayam gulung kulit tahu goreng renyah.'],
                    ['nama' => 'Es Liang Teh Kampus', 'category_id' => $catMinuman->id, 'harga' => 6000, 'stok_porsi' => 40, 'tersedia' => true, 'deskripsi' => 'Minuman liang teh manis dingin penyegar dahaga.'],
                ],
                'ingredients' => [
                    ['nama' => 'Mie Telur Basah', 'satuan' => 'kg', 'stok' => 7.00, 'batas_minimum' => 2.00],
                    ['nama' => 'Kulit Dimsum', 'satuan' => 'pcs', 'stok' => 120.00, 'batas_minimum' => 40.00],
                ],
                'recipes' => [
                    0 => [
                        ['ingredient_index' => 0, 'takaran' => 0.14],
                    ],
                ],
            ],
        ];

        $createdOutlets = [];
        $createdMenusByOutlet = [];

        foreach ($outletsData as $outletDef) {
            $outletUser = User::updateOrCreate(
                ['email' => $outletDef['email']],
                [
                    'name' => $outletDef['user_name'],
                    'password' => Hash::make('Outlet123!'),
                    'role' => UserRole::Outlet,
                ]
            );

            $outlet = Outlet::updateOrCreate(
                ['slug' => $outletDef['slug']],
                [
                    'user_id' => $outletUser->id,
                    'nama' => $outletDef['nama'],
                    'pemilik' => $outletDef['pemilik'],
                    'no_hp' => $outletDef['no_hp'],
                    'alamat' => $outletDef['alamat'],
                    'jam_buka' => $outletDef['jam_buka'],
                    'status' => $outletDef['status'],
                    'royalty_percent' => 10.00,
                    'is_sample' => true,
                ]
            );

            $createdOutlets[$outlet->slug] = $outlet;
            $createdMenusByOutlet[$outlet->slug] = [];

            foreach ($outletDef['menus'] as $menuDef) {
                $menu = Menu::updateOrCreate(
                    [
                        'outlet_id' => $outlet->id,
                        'nama' => $menuDef['nama'],
                    ],
                    [
                        'category_id' => $menuDef['category_id'],
                        'deskripsi' => $menuDef['deskripsi'],
                        'harga' => $menuDef['harga'],
                        'stok_porsi' => $menuDef['stok_porsi'],
                        'tersedia' => $menuDef['tersedia'],
                        'foto' => null,
                        'is_sample' => true,
                    ]
                );

                $createdMenusByOutlet[$outlet->slug][] = $menu;
            }

            // Bahan Baku Contoh per Outlet (KF-06)
            $createdIngredients = [];
            foreach (($outletDef['ingredients'] ?? []) as $ingDef) {
                $ingredient = Ingredient::updateOrCreate(
                    [
                        'outlet_id' => $outlet->id,
                        'nama' => $ingDef['nama'],
                    ],
                    [
                        'satuan' => $ingDef['satuan'],
                        'stok' => $ingDef['stok'],
                        'batas_minimum' => $ingDef['batas_minimum'],
                        'is_sample' => true,
                    ]
                );

                $createdIngredients[] = $ingredient;

                if ($ingredient->movements()->count() === 0) {
                    StockMovement::create([
                        'ingredient_id' => $ingredient->id,
                        'jenis' => StockMovementType::Masuk,
                        'jumlah' => $ingDef['stok'],
                        'keterangan' => 'Stok awal bahan baku (Data Contoh)',
                        'created_at' => now()->subDays(2),
                    ]);
                }
            }

            // Resep Sederhana Contoh per Menu (KF-06)
            foreach (($outletDef['recipes'] ?? []) as $menuIdx => $recipeList) {
                $menu = $createdMenusByOutlet[$outlet->slug][$menuIdx] ?? null;
                if (! $menu) {
                    continue;
                }

                MenuIngredient::where('menu_id', $menu->id)->delete();
                foreach ($recipeList as $rRow) {
                    $ing = $createdIngredients[$rRow['ingredient_index']] ?? null;
                    if ($ing) {
                        MenuIngredient::create([
                            'menu_id' => $menu->id,
                            'ingredient_id' => $ing->id,
                            'takaran' => $rRow['takaran'],
                        ]);
                    }
                }
            }
        }

        // 4. Pesanan Contoh (8 pesanan yang sudah dibayar tunai: sebagian Diproses, sebagian Selesai dengan royalti 10% terhitung)
        $sampleOrders = [
            [
                'kode' => 'AYZ-20261116-0001',
                'nama_pemesan' => 'Aldi Pratama',
                'outlet_slug' => 'ayiriz-geprek',
                'status' => OrderStatus::Selesai,
                'items' => [
                    ['menu_index' => 0, 'jumlah' => 2, 'reaction' => ReactionEmote::Enak],
                    ['menu_index' => 5, 'jumlah' => 2, 'reaction' => ReactionEmote::Manis],
                ],
                'created_at' => now()->subHours(5),
                'selesai_at' => now()->subHours(4),
            ],
            [
                'kode' => 'AYZ-20261116-0002',
                'nama_pemesan' => 'Siti Nabila',
                'outlet_slug' => 'ayiriz-geprek',
                'status' => OrderStatus::Selesai,
                'items' => [
                    ['menu_index' => 1, 'jumlah' => 1, 'reaction' => ReactionEmote::Pedas],
                    ['menu_index' => 4, 'jumlah' => 1, 'reaction' => ReactionEmote::Enak],
                ],
                'created_at' => now()->subHours(3),
                'selesai_at' => now()->subHours(2),
            ],
            [
                'kode' => 'AYZ-20261116-0003',
                'nama_pemesan' => 'Rizky Ramadhan',
                'outlet_slug' => 'ayiriz-geprek',
                'status' => OrderStatus::Diproses,
                'items' => [
                    ['menu_index' => 0, 'jumlah' => 1, 'reaction' => ReactionEmote::Pedas],
                    ['menu_index' => 2, 'jumlah' => 1, 'reaction' => ReactionEmote::Enak],
                ],
                'created_at' => now()->subMinutes(40),
                'selesai_at' => null,
            ],
            [
                'kode' => 'AYZ-20261116-0004',
                'nama_pemesan' => 'Nadia Putri',
                'outlet_slug' => 'ayiriz-kopi-roti',
                'status' => OrderStatus::Selesai,
                'items' => [
                    ['menu_index' => 0, 'jumlah' => 2, 'reaction' => ReactionEmote::Manis],
                    ['menu_index' => 1, 'jumlah' => 1, 'reaction' => ReactionEmote::Enak],
                ],
                'created_at' => now()->subHours(4),
                'selesai_at' => now()->subHours(3),
            ],
            [
                'kode' => 'AYZ-20261116-0005',
                'nama_pemesan' => 'Fajar Hidayat',
                'outlet_slug' => 'ayiriz-kopi-roti',
                'status' => OrderStatus::Diproses,
                'items' => [
                    ['menu_index' => 3, 'jumlah' => 1, 'reaction' => ReactionEmote::Enak],
                    ['menu_index' => 5, 'jumlah' => 1, 'reaction' => ReactionEmote::Pedas],
                ],
                'created_at' => now()->subMinutes(25),
                'selesai_at' => null,
            ],
            [
                'kode' => 'AYZ-20261116-0006',
                'nama_pemesan' => 'Farhan Akbar',
                'outlet_slug' => 'ayiriz-es-dessert',
                'status' => OrderStatus::Selesai,
                'items' => [
                    ['menu_index' => 0, 'jumlah' => 2, 'reaction' => ReactionEmote::Manis],
                    ['menu_index' => 5, 'jumlah' => 2, 'reaction' => ReactionEmote::Manis],
                ],
                'created_at' => now()->subHours(6),
                'selesai_at' => now()->subHours(5),
            ],
            [
                'kode' => 'AYZ-20261116-0007',
                'nama_pemesan' => 'Aulia Rahma',
                'outlet_slug' => 'ayiriz-es-dessert',
                'status' => OrderStatus::Selesai,
                'items' => [
                    ['menu_index' => 1, 'jumlah' => 1, 'reaction' => ReactionEmote::Enak],
                    ['menu_index' => 2, 'jumlah' => 1, 'reaction' => ReactionEmote::Manis],
                ],
                'created_at' => now()->subHours(2),
                'selesai_at' => now()->subHour(),
            ],
            [
                'kode' => 'AYZ-20261116-0008',
                'nama_pemesan' => 'Taufik Ismail',
                'outlet_slug' => 'ayiriz-es-dessert',
                'status' => OrderStatus::Diproses,
                'items' => [
                    ['menu_index' => 3, 'jumlah' => 2, 'reaction' => ReactionEmote::Manis],
                ],
                'created_at' => now()->subMinutes(15),
                'selesai_at' => null,
            ],
        ];

        foreach ($sampleOrders as $orderDef) {
            $outlet = $createdOutlets[$orderDef['outlet_slug']];
            $menus = $createdMenusByOutlet[$orderDef['outlet_slug']];

            $total = 0;
            $itemsPayload = [];
            $reactionsPayload = [];

            foreach ($orderDef['items'] as $itemRow) {
                /** @var Menu $menu */
                $menu = $menus[$itemRow['menu_index']];
                $qty = $itemRow['jumlah'];
                $subtotal = $menu->harga * $qty;
                $total += $subtotal;

                $itemsPayload[] = [
                    'menu_id' => $menu->id,
                    'nama_menu_saat_submit' => $menu->nama,
                    'harga_saat_submit' => $menu->harga,
                    'jumlah' => $qty,
                    'subtotal' => $subtotal,
                ];

                if (isset($itemRow['reaction'])) {
                    $reactionsPayload[$menu->id] = $itemRow['reaction'];
                }
            }

            $isSelesai = $orderDef['status'] === OrderStatus::Selesai;
            $royaltyPercent = $isSelesai ? 10.00 : null;
            $royaltyNominal = $isSelesai ? (int) round($total * 0.10) : 0;

            $order = Order::updateOrCreate(
                ['kode' => $orderDef['kode']],
                [
                    'nama_pemesan' => $orderDef['nama_pemesan'],
                    'outlet_id' => $outlet->id,
                    'status' => $orderDef['status'],
                    'total' => $total,
                    'royalty_percent_saat_selesai' => $royaltyPercent,
                    'royalty_nominal' => $royaltyNominal,
                    'metode_bayar' => 'bayar_di_kasir',
                    'selesai_at' => $orderDef['selesai_at'],
                    'is_sample' => true,
                    'created_at' => $orderDef['created_at'],
                    'updated_at' => $orderDef['selesai_at'] ?? $orderDef['created_at'],
                ]
            );

            $order->items()->delete();
            foreach ($itemsPayload as $payload) {
                OrderItem::create(array_merge($payload, [
                    'order_id' => $order->id,
                ]));
            }

            // Reaksi Emote Contoh (KF-07 — tersebar di ketiga emote untuk pesanan yang sudah dibayar)
            foreach ($reactionsPayload as $menuId => $emoteCase) {
                Reaction::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'menu_id' => $menuId,
                    ],
                    [
                        'emote' => $emoteCase,
                        'is_sample' => true,
                    ]
                );
            }
        }

        // 5. Antrean Contoh (2 antrean di daftar tunggu — belum dibayar, belum mengurangi stok, tanpa status)
        $sampleAntreans = [
            [
                'kode' => 'AYZ-20261116-0009',
                'nama_pemesan' => 'Budi Santoso',
                'outlet_slug' => 'ayiriz-geprek',
                'items' => [
                    ['menu_index' => 0, 'jumlah' => 1], // 15.000
                    ['menu_index' => 5, 'jumlah' => 1], // 6.000
                ],
                'created_at' => now()->subMinutes(10),
            ],
            [
                'kode' => 'AYZ-20261116-0010',
                'nama_pemesan' => 'Dina Lestari',
                'outlet_slug' => 'ayiriz-kopi-roti',
                'items' => [
                    ['menu_index' => 0, 'jumlah' => 1], // 12.000
                    ['menu_index' => 2, 'jumlah' => 1], // 10.000
                ],
                'created_at' => now()->subMinutes(5),
            ],
        ];

        foreach ($sampleAntreans as $antreanDef) {
            $outlet = $createdOutlets[$antreanDef['outlet_slug']];
            $menus = $createdMenusByOutlet[$antreanDef['outlet_slug']];

            $total = 0;
            $itemsPayload = [];

            foreach ($antreanDef['items'] as $itemRow) {
                /** @var Menu $menu */
                $menu = $menus[$itemRow['menu_index']];
                $qty = $itemRow['jumlah'];
                $subtotal = $menu->harga * $qty;
                $total += $subtotal;

                $itemsPayload[] = [
                    'menu_id' => $menu->id,
                    'nama_menu_saat_submit' => $menu->nama,
                    'harga_saat_submit' => $menu->harga,
                    'jumlah' => $qty,
                    'subtotal' => $subtotal,
                ];
            }

            $antrean = Antrean::updateOrCreate(
                ['kode' => $antreanDef['kode']],
                [
                    'outlet_id' => $outlet->id,
                    'nama_pemesan' => $antreanDef['nama_pemesan'],
                    'total' => $total,
                    'is_sample' => true,
                    'created_at' => $antreanDef['created_at'],
                    'updated_at' => $antreanDef['created_at'],
                ]
            );

            $antrean->items()->delete();
            foreach ($itemsPayload as $payload) {
                AntreanItem::create(array_merge($payload, [
                    'antrean_id' => $antrean->id,
                ]));
            }
        }

        // 6. Contoh Audit Royalti Semester Lalu (KF-10 — Data Contoh Fiktif)
        // Semester lalu "2025/2026 Genap" sudah diaudit dengan keputusan Tetap 10% sehingga royalti berjalan tetap 10.00%,
        // sedangkan semester berjalan "2026/2027 Ganjil" siap diaudit saat demo.
        foreach (['ayiriz-geprek', 'ayiriz-kopi-roti'] as $slug) {
            $outlet = $createdOutlets[$slug] ?? null;
            if (! $outlet) {
                continue;
            }

            RoyaltyAudit::updateOrCreate(
                [
                    'outlet_id' => $outlet->id,
                    'semester' => '2025/2026 Genap',
                ],
                [
                    'total_penjualan' => 450000,
                    'total_royalti_terhitung' => 45000,
                    'keputusan' => RoyaltyDecision::Tetap,
                    'percent_baru' => 10.00,
                    'catatan' => 'Evaluasi awal kemitraan semester 2025/2026 Genap: bagi hasil royalti dipertahankan tetap 10% (Data Contoh).',
                    'dibuat_oleh' => $pusatUser->id,
                    'is_sample' => true,
                ]
            );
        }
    }
}

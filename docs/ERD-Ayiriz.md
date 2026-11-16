# ERD — Entity Relationship Diagram

## Sistem Informasi Pengelolaan UMKM Lokal Ayiriz

**Pendamping:** `PRD-Ayiriz.md` (apa yang dibangun) dan
`blueprint-Ayiriz.md` (bagaimana membangunnya)
**Database:** MySQL, Laragon lokal, timezone Asia/Makassar
**Status:** Draf v1.0 — fondasi dikunci 2026-11-16

---

## 1. Diagram (Mermaid)

```mermaid
erDiagram
    users ||--o{ outlets : "mengelola (outlet)"
    users ||--o{ orders : "memesan (pelanggan)"
    users ||--o{ reactions : "memberi"

    outlets ||--o{ menus : "punya"
    outlets ||--o{ orders : "menerima"
    outlets ||--o{ ingredients : "punya"
    outlets ||--o{ royalty_histories : "punya"
    outlets ||--o{ royalty_audits : "diaudit"

    categories ||--o{ menus : "mengelompokkan"

    menus ||--o{ order_items : "dipesan"
    menus ||--o{ menu_ingredients : "resep"
    menus ||--o{ reactions : "mendapat"

    orders ||--o{ order_items : "berisi"
    orders ||--o{ reactions : "dinilai"

    ingredients ||--o{ menu_ingredients : "dipakai"
    ingredients ||--o{ stock_movements : "bergerak"

    users {
        bigint id PK
        string name
        string email UK
        string password
        enum role "pusat | outlet | pelanggan"
        datetime created_at
        datetime updated_at
    }

    outlets {
        bigint id PK
        bigint user_id FK
        string nama
        string slug UK
        string pemilik
        string no_hp
        string alamat
        string jam_buka
        enum status "terverifikasi | belum_terverifikasi | nonaktif"
        decimal royalty_percent "default 10.00"
        datetime created_at
        datetime updated_at
    }

    categories {
        bigint id PK
        string nama UK
        datetime created_at
        datetime updated_at
    }

    menus {
        bigint id PK
        bigint outlet_id FK
        bigint category_id FK
        string nama
        text deskripsi
        integer harga
        integer stok_porsi "default 0"
        boolean tersedia "default true"
        string foto
        datetime created_at
        datetime updated_at
    }

    orders {
        bigint id PK
        string kode UK "misal AYZ-20261116-0001"
        bigint user_id FK "pelanggan"
        bigint outlet_id FK
        enum status "diproses | selesai"
        integer total
        decimal royalty_percent_saat_selesai
        integer royalty_nominal
        string metode_bayar "bayar_di_kasir"
        datetime selesai_at
        datetime created_at
        datetime updated_at
    }

    order_items {
        bigint id PK
        bigint order_id FK
        bigint menu_id FK
        string nama_menu_saat_checkout
        integer harga_saat_checkout
        integer jumlah
        integer subtotal
    }

    ingredients {
        bigint id PK
        bigint outlet_id FK
        string nama
        string satuan "kg, liter, pcs"
        decimal stok
        decimal batas_minimum
        datetime created_at
        datetime updated_at
    }

    menu_ingredients {
        bigint id PK
        bigint menu_id FK
        bigint ingredient_id FK
        decimal takaran "per 1 porsi"
    }

    stock_movements {
        bigint id PK
        bigint ingredient_id FK
        enum jenis "masuk | keluar | penyesuaian"
        decimal jumlah
        string keterangan
        datetime created_at
    }

    reactions {
        bigint id PK
        bigint user_id FK "pelanggan"
        bigint order_id FK
        bigint menu_id FK
        enum emote "enak | pedas | manis"
        datetime created_at
        datetime updated_at
    }

    royalty_histories {
        bigint id PK
        bigint outlet_id FK
        decimal percent_lama
        decimal percent_baru
        string alasan
        bigint diubah_oleh FK "users pusat"
        datetime berlaku_mulai
        datetime created_at
    }

    royalty_audits {
        bigint id PK
        bigint outlet_id FK
        string semester "misal 2026-ganjil"
        integer total_penjualan
        integer total_royalti_terhitung
        enum keputusan "tetap | naik"
        decimal percent_baru
        text catatan
        bigint dibuat_oleh FK "users pusat"
        datetime created_at
        datetime updated_at
    }
```

---

## 2. Tabel dan Aturan Penting

### 2.1 `users` — Akun

- `role`: `pusat | outlet | pelanggan`. Satu user satu role.
- Satu user Outlet mengelola satu outlet (`outlets.user_id`).
  Kalau nanti satu pemilik punya dua kantin, tambah baris outlet
  dengan user berbeda — jangan ubah relasi ini di MVP.
- Password ter-hash (bcrypt). Email unik.

### 2.2 `outlets` — Kantin mitra

- `status`: outlet baru = `belum_terverifikasi`, tidak tampil di
  katalog. Pusat mengubahnya menjadi `terverifikasi` (tampil) atau
  `nonaktif` (disembunyikan).
- `royalty_percent` default `10.00`. Hanya pusat yang boleh
  mengubahnya, dan hanya lewat audit (KF-10) — perubahan langsung
  tanpa audit tidak ada di UI.
- `slug` unik untuk URL `/outlet/{slug}`.

### 2.3 `categories` dan `menus` — Menu

- Kategori bersifat global (misal Makanan Berat, Minuman,
  Cemilan/Dessert), bukan per outlet, supaya filter katalog konsisten.
- `menus.outlet_id` + `menus.category_id`. Harga dalam rupiah
  (integer, tanpa desimal).
- `stok_porsi`: jumlah porsi yang bisa dipesan. Berkurang saat
  pesanan dibuat. `tersedia = false` atau `stok_porsi = 0` berarti
  tidak bisa dipesan.
- `foto` boleh kosong; tampilkan placeholder kalau kosong.

### 2.4 `orders` dan `order_items` — Pesanan

- `kode` unik, dibuat server saat checkout (misal
  `AYZ-20261116-0001`).
- `status` hanya `diproses | selesai` — enum di database, bukan
  string bebas. Transisi hanya Diproses → Selesai.
- Satu pesanan hanya satu `outlet_id`. Ini ditegakkan di service,
  bukan hanya di keranjang.
- `order_items` mengunci `nama_menu_saat_checkout` dan
  `harga_saat_checkout` — harga menu boleh berubah sesudahnya tanpa
  mengubah pesanan lama.
- `royalty_percent_saat_selesai` dan `royalty_nominal` diisi saat
  pesanan menjadi Selesai (persen royalti outlet saat itu × total).
  Pesanan Diproses: kedua kolom ini kosong/nol.
- `metode_bayar` selalu `bayar_di_kasir` di MVP.

### 2.5 `ingredients`, `menu_ingredients`, `stock_movements` — Stok

- `ingredients` milik satu outlet (`outlet_id`). Nama bahan boleh
  sama antar outlet — tidak ada bahan global.
- `menu_ingredients` adalah resep sederhana: satu menu memakai
  0..n bahan, masing-masing dengan `takaran` per 1 porsi.
- `stock_movements` adalah jejak perubahan stok bahan: masuk
  (outlet beli/tambah stok), keluar (pemakaian manual), penyesuaian.
  Stok bahan saat ini = jumlah seluruh movement; atau simpan
  `ingredients.stok` sebagai hasil hitung yang diperbarui tiap
  movement — pilih satu, jangan dua-duanya tanpa sinkronisasi.
  Disarankan: `ingredients.stok` diperbarui dalam transaksi yang
  sama dengan penulisan `stock_movements`.
- Pengurangan otomatis dari resep hanya terjadi saat pesanan
  Selesai, dan hanya untuk menu yang punya resep. Pengurangan itu
  juga ditulis sebagai `stock_movements` jenis `keluar` dengan
  keterangan kode pesanan.

### 2.6 `reactions` — Reaksi emote

- `emote`: `enak | pedas | manis` — pemetaan tampilannya:
  `enak = 😋 Enak`, `pedas = 🥵 Pedas`,
  `manis = 🫠 Manis / menyejukkan`.
- **Unique key: `(user_id, order_id, menu_id)`.** Satu pelanggan
  hanya punya satu reaksi per menu per pesanan. Reaksi kedua adalah
  UPDATE, bukan INSERT baru.
- Aturan service (bukan hanya database): reaksi hanya boleh dibuat
  kalau `orders.status = selesai` dan `orders.user_id` sama dengan
  pelanggan yang bereaksi, dan `menu_id` memang ada di pesanan itu.
- **Tidak ada kolom skor, rata-rata, atau ranking.** Hitungan yang
  tampil dihitung langsung: `SELECT emote, COUNT(*) ... GROUP BY
  emote`. Jangan tambahkan kolom agregat ke tabel ini.

### 2.7 `royalty_histories` dan `royalty_audits` — Royalti

- `royalty_histories`: setiap perubahan persen royalti outlet
  dicatat (lama → baru, alasan, siapa yang mengubah, berlaku mulai
  kapan). Ini jejak auditnya.
- `royalty_audits`: satu baris per (outlet, semester) — tambah
  unique key `(outlet_id, semester)`. Berisi snapshot total
  penjualan semester itu, total royalti terhitung, keputusan
  (`tetap | naik`), persen baru, dan catatan pusat.
- Keputusan `naik` di audit harus diikuti baris baru di
  `royalty_histories`; keputusan `tetap` tidak perlu baris baru.

---

## 3. Indeks yang Perlu Ada

- `orders (outlet_id, status, created_at)` — daftar pesanan outlet
  dan laporan.
- `orders (user_id, created_at)` — riwayat pesanan pelanggan.
- `order_items (order_id)` dan `(menu_id)`.
- `menus (outlet_id, tersedia)` — katalog per outlet.
- `reactions (menu_id, emote)` — hitungan reaksi per menu.
- `reactions` unique `(user_id, order_id, menu_id)`.
- `royalty_audits` unique `(outlet_id, semester)`.
- `stock_movements (ingredient_id, created_at)`.

---

## 4. Contoh Data (Seeder, Fiktif)

- 3 outlet contoh: misal "Ayiriz Geprek", "Ayiriz Kopi & Roti",
  "Ayiriz Es & Dessert" — nama contoh, bukan UMKM sungguhan.
- Tiap outlet: 5–8 menu, 2–3 bahan, 1–2 resep sederhana.
- 8–12 pesanan contoh: sebagian Diproses, sebagian Selesai (dengan
  royalti terhitung).
- Reaksi contoh tersebar di ketiga emote, supaya hitungan
  `😋 n · 🥵 n · 🫠 n` terlihat di demo — tanpa ada outlet yang
  dibuat "menang".

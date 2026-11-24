# Blueprint — Gambaran Besar Arsitektur Sistem

## Sistem Informasi Pengelolaan UMKM Lokal Ayiriz

**Nama sistem:** Sistem Informasi Pengelolaan UMKM Lokal Ayiriz
**Brand:** Ayiriz — kantin-kantin mitra di depan gerbang kampus UNG
**Produk:** Website pengelolaan banyak UMKM/kantin F&B dalam satu brand franchise (definisi lengkap di `PRD-Ayiriz.md`)
**Stack yang ditetapkan:** Laravel + Blade + Tailwind CSS + Alpine.js
**Database:** MySQL, Laragon lokal, timezone Asia/Makassar
**Status dokumen:** Draf v1.1, pendamping `PRD-Ayiriz.md` — revisi Model B 2026-11-24
**Bahasa dokumen:** Bahasa Indonesia

**Cara memakai dokumen ini:** Dokumen ini menjawab *bagaimana* sistem
dibangun dan *bagaimana tiap fitur di PRD bekerja*, dari halaman yang
dilihat pengguna sampai tabel database dan alur di server. Definisi
fungsi, alasan, dan kriteria penerimaannya ada di `PRD-Ayiriz.md`;
pemetaan fitur ke modul, halaman, dan alur di sini ditulis lengkap
supaya pembaca tidak harus membuka PRD untuk memahami gambaran
besarnya. Skema tabel lengkap ada di `ERD-Ayiriz.md`.

> **Batas yang sama dengan PRD, diulang supaya tidak hilang saat
> dokumen ini dibaca terpisah:** Ayiriz adalah **prototipe tugas
> kuliah, bukan franchise sungguhan**. Data seeder adalah contoh
> fiktif. Tidak ada payment gateway nyata, tidak ada kurir/delivery,
> tidak ada gudang pusat. Pembayaran dicatat sebagai "Bayar di Kasir".

---

## 1. Gambaran Besar dalam Satu Gambar

Satu aplikasi web **monolitik Laravel** (bukan SPA, bukan
microservices). Browser meminta halaman, server Laravel memeriksa
login, peran, dan kepemilikan data, mengambil data dari MySQL,
menghitung bila perlu lewat service, lalu me-render Blade.
Interaktivitas ringan (keranjang, filter, modal, tab) ditangani
Alpine.js di browser.

```mermaid
flowchart LR
    subgraph Pengguna
        PLG[Pelanggan (tamu)\ntanpa login]
        OUT[Admin Outlet\nlogin]
        PST[Pusat Ayiriz\nlogin]
    end

    subgraph Aplikasi_Laravel["Aplikasi Laravel (satu codebase)"]
        R[Routes + Middleware\nauth, role, ownership]
        C[Controllers\ntipis, validasi di Form Request]
        P[Policies\nsiapa boleh lihat / ubah apa]
        S[Services\nOrder, Royalty,\nStock, Reaction,\nAntrean]
        V[Blade Views\n+ Tailwind + Alpine.js]
    end

    DB[(MySQL\nLaragon)]

    PLG --> R
    OUT --> R
    PST --> R
    R --> C
    C --> P
    C --> S
    S --> DB
    C --> V
```

---

## 2. Modul dan Pemetaan Fitur

| Modul | Fitur PRD | Isi |
|---|---|---|
| Auth & Akun | KF-11 | Login pusat & outlet (tanpa register pelanggan, tanpa register outlet mandiri), middleware role, policy kepemilikan |
| Outlet | KF-01 | CRUD + verifikasi outlet oleh pusat, status Aktif/Nonaktif, persen royalti outlet |
| Katalog & Menu | KF-02, KF-03 | CRUD menu per outlet, kategori, pencarian, filter harga/kategori, halaman outlet |
| Pemesanan | KF-04 | Keranjang session satu outlet, submit ke antrean (nama pemesan wajib), kode antrean, kunci harga saat submit; TIDAK kurangi stok |
| Kasir | KF-05 | Daftar tunggu (antrean) + pesanan Diproses, konfirmasi bayar tunai manual (antrean → pesanan Diproses, kurangi stok porsi, tampilkan QR reaksi), tandai Selesai saat ambil, struk sederhana + royalti |
| Stok | KF-06 | Stok porsi menu (berkurang saat konfirmasi bayar, bukan saat antrean dibuat), stok bahan (masuk/keluar manual), batas minimum, resep sederhana |
| Reaksi | KF-07 | 1 reaksi per menu per pesanan yang sudah dibayar, ganti reaksi, QR reaksi di layar kasir, hitungan per emote (tanpa skor) |
| Laporan | KF-08, KF-09 | Dashboard pusat (gabungan), laporan outlet, filter periode |
| Audit Royalti | KF-10 | Audit per semester per outlet, keputusan Tetap/Naik + alasan, riwayat royalti |

---

## 3. Halaman Utama

### 3.1 Publik (tanpa login)

- `/` — beranda: daftar outlet Aktif, menu populer per outlet (tanpa
  ranking antar outlet), pencarian.
- `/outlet/{slug}` — profil outlet + daftar menunya + hitungan reaksi
  per menu.
- `/menu/{id}` — detail menu + hitungan reaksi (`😋 n · 🥵 n · 🫠 n`).
- `/keranjang` — keranjang session satu outlet, ubah jumlah, tulis
  nama pemesan, submit ke antrean (tampil kode antrean + total).
- `/reaksi/{kode}` — halaman reaksi pesanan (tanpa login, dibuka lewat
  QR kasir atau kode pesanan); hanya untuk pesanan yang sudah dibayar.

### 3.2 Outlet (login)

- `/outlet/dashboard` — antrean + pesanan Diproses, stok menipis,
  penjualan hari ini.
- `/outlet/menu` — CRUD menu + kategori + stok porsi.
- `/outlet/pesanan` — daftar tunggu + pesanan, detail, konfirmasi
  bayar tunai, tombol Selesai, QR reaksi.
- `/outlet/stok` — stok bahan, stok masuk/keluar, batas minimum, resep.
- `/outlet/laporan` — laporan outlet sendiri per periode.

### 3.3 Pusat (login)

- `/pusat/dashboard` — total penjualan, per outlet, royalti.
- `/pusat/outlet` — daftar/verifikasi outlet.
- `/pusat/laporan` — laporan gabungan per periode.
- `/pusat/audit` — audit semester per outlet + putuskan royalti baru.

---

## 4. Aturan Inti di Server (Jangan Hanya di Tampilan)

Aturan-aturan ini ditegakkan di model/service/policy, bukan cuma
disembunyikan tombolnya di Blade:

1. **Satu pesanan = satu outlet.** Submit antrean menolak keranjang
   yang berisi menu dari dua outlet. Validasi di `AntreanService`.
2. **Harga dikunci saat submit antrean.** `antrean_items` menyimpan
   `harga_saat_submit` dan `nama_menu_saat_submit`; saat antrean
   menjadi pesanan, nilainya disalin ke `order_items`. Mengubah harga
   menu sesudahnya tidak mengubah antrean/pesanan lama.
3. **Status hanya Diproses dan Selesai.** Enum di database dan di
   model hanya berisi dua nilai itu. Transisi yang diizinkan hanya
   Diproses → Selesai, satu arah. Tidak ada status lain di kode.
   Antrean bukan status — antrean tidak punya kolom status. Pesanan
   dibuat langsung berstatus Diproses saat kasir mengonfirmasi
   pembayaran.
4. **Kepemilikan outlet.** Policy memastikan outlet hanya bisa
   membaca/mengubah menu, pesanan, stok, dan laporan milik outletnya.
   `outlet_id` di tiap baris dicek di query, bukan hanya di URL.
5. **Royalti hanya dari pesanan Selesai.** `RoyaltyService` menghitung
   royalti dari pesanan Selesai pada periode itu. Pesanan Diproses
   tidak masuk hitungan.
6. **Reaksi hanya untuk pesanan yang sudah dibayar, satu per menu
   per pesanan.** `ReactionService` menolak reaksi kalau pesanan belum
   dibayar (masih antrean) atau `menu_id` tidak ada di pesanan itu.
   Tidak ada pengecekan akun pelanggan. Reaksi kedua menggantikan
   (update), bukan menambah baris. QR reaksi di layar kasir hanya
   tautan ke halaman reaksi pesanan itu (bukan QRIS bayar). Tidak ada
   fungsi skor/rata-rata/ranking reaksi di codebase — hitungan per
   emote diambil langsung dengan `GROUP BY emote`.
7. **Royalti baru tidak berlaku surut.** Mengubah persen royalti
   outlet hanya mengubah pesanan Selesai sesudah perubahan. Riwayat
   perubahan disimpan di `royalty_histories`; audit semester
   menyimpan snapshot total penjualan dan keputusan.
8. **Antrean tidak mengurangi stok.** Submit antrean tidak mengubah
   `stok_porsi` maupun stok bahan. Stok porsi berkurang saat kasir
   mengonfirmasi pembayaran (antrean → pesanan Diproses). Stok bahan
   berkurang saat pesanan Selesai (lihat §5.1). Antrean yang dihapus
   outlet tidak mengubah stok apa pun.
9. **Pembayaran hanya tunai di kasir, manual.** Tidak ada payment
   gateway, tidak ada pembayaran online, tidak ada QRIS nyata.
   Satu-satunya metode bayar adalah tunai di kasir; kasir
   mengonfirmasi pembayaran secara manual di sistem
   (`metode_bayar = bayar_di_kasir`).

---

## 5. Alur Fitur (Ringkasan)

### 5.1 Pesan sampai Selesai

```
Pelanggan (tamu) pilih outlet → tambah menu ke keranjang (satu outlet, session)
  → tulis nama pemesan → submit → masuk daftar tunggu (antrean), tanpa status, tanpa kurangi stok
  → pelanggan datang ke kantin, bayar tunai di kasir
  → kasir konfirmasi bayar manual → antrean jadi pesanan Diproses, stok porsi berkurang, QR reaksi tampil
  → outlet masak pesanan → pelanggan ambil → kasir tandai Selesai
  → royalti 10% tercatat untuk pesanan itu
  → (kalau menu punya resep) stok bahan berkurang
  → pelanggan boleh beri reaksi emote per menu (lewat QR atau kode pesanan)
```

### 5.2 Audit royalti semester

```
Pusat buka /pusat/audit → pilih semester + outlet
  → sistem tampilkan total penjualan Selesai + royalti terhitung
  → pusat putuskan: Tetap atau Naik (+ alasan)
  → simpan ke royalty_histories + audit record
  → persen baru berlaku untuk pesanan Selesai semester berikutnya
```

### 5.3 Stok menipis

```
Outlet set batas minimum per bahan
  → stok bahan <= batas minimum → muncul di dashboard outlet
  → outlet catat stok masuk → peringatan hilang
```

---

## 6. Skema Database (Ringkasan — Lengkap di ERD-Ayiriz.md)

Tabel inti: `users`, `outlets`, `menus`, `categories`,
`antrean`, `antrean_items`, `orders`, `order_items`,
`ingredients`, `menu_ingredients` (resep), `stock_movements`,
`reactions`, `royalty_histories`, `royalty_audits`. Relasi kuncinya:
satu outlet punya banyak menu, antrean, pesanan, dan bahan; satu
antrean punya banyak item dan hanya satu outlet; satu pesanan lahir
dari satu antrean dan memakai kode yang sama, punya banyak item dan
hanya satu outlet; satu reaksi milik satu pesanan dan satu menu
(tanpa akun pelanggan), dengan emote `enak | pedas | manis`; satu
audit milik satu outlet dan satu semester.

---

## 7. Teknologi dan Konfigurasi Lokal

- **Tema visual:** ikuti `Minimalism-Ayiriz.md` dan `Motion-Ayiriz.md` — palet Indomie, Lobster untuk judul/brand, Coolvetica Condensed/Coolvetica dengan fallback Oswald + Archivo (file `woff2` Coolvetica belum ada, jangan mengarang file font itu).
- **Laravel** (Blade + Tailwind + Alpine.js), **MySQL** via Laragon.
- `.env` lokal: `DB_HOST=127.0.0.1`, database `ayiriz`,
  username `root`, tanpa password, port `3306` — aman untuk lokal
  saja, jangan dipakai di production dan jangan commit `.env`.
- Timezone `Asia/Makassar`, bahasa aplikasi `id`.
- Foto menu disimpan di `storage/app/public/menu` dan diakses lewat
  `storage:link`; kalau tidak ada foto, tampilkan placeholder.
- Seeder: `DatabaseSeeder` memanggil seeder outlet, menu, antrean,
  pesanan, stok, dan reaksi contoh (lihat PRD §6); tanpa akun
  pelanggan.

---

## 8. Yang Sengaja Tidak Dibangun

Payment gateway, pembayaran online apa pun, kurir/delivery, gudang
pusat, voucher/promo, poin loyalitas, teks ulasan, bintang, ranking
antar outlet berdasarkan reaksi, pendaftaran/login pelanggan, dan
status pesanan selain Diproses/Selesai. Kalau salah satu dari ini
muncul di kode "buat jaga-jaga", hapus — menambahnya nanti lebih
murah daripada menjelaskan ke dosen kenapa ada fitur yang tidak
sesuai PRD.

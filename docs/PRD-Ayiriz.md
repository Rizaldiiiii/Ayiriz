# PRD — Product Requirements Document

## Sistem Informasi Pengelolaan UMKM Lokal Ayiriz

**Nama sistem:** Sistem Informasi Pengelolaan UMKM Lokal Ayiriz
**Nama brand / franchise:** Ayiriz
**Lokasi:** Kantin-kantin mitra di depan gerbang kampus Universitas Negeri Gorontalo (UNG)
**Produk:** Website dinamis dan interaktif untuk mengelola banyak UMKM/kantin F&B standar anak kuliahan dalam satu brand franchise
**Pengguna utama:** Pusat Ayiriz (owner brand), Admin Outlet (pemilik UMKM/kantin mitra), Pelanggan (mahasiswa, tamu tanpa akun)
**Tugas:** Tugas Besar Pemrograman Web — pengganti UAS, kelompok 2 orang, presentasi + laporan, penilaian per orang
**Stack yang ditetapkan:** Laravel + MySQL, Laragon lokal
**Tema visual:** `Minimalism-Ayiriz.md` + `Motion-Ayiriz.md` (palet Indomie; font Lobster/Coolvetica dengan fallback Oswald + Archivo karena file `woff2` Coolvetica belum ada)
**Status dokumen:** Draf v1.1 untuk pembangunan — fondasi dikunci 2026-11-16; revisi Model B 2026-11-24 (pelanggan tamu tanpa akun, antrean, bayar tunai, Diproses setelah bayar)
**Bahasa dokumen:** Bahasa Indonesia

**Cara memakai dokumen ini:** Dokumen ini berdiri sendiri untuk urusan
*apa* yang dibangun dan *mengapa*. File yang menyertainya adalah
`blueprint-Ayiriz.md` (cara membangunnya) dan `ERD-Ayiriz.md` (skema
database). Bila ada hal yang tidak diatur di sini atau di file penyerta
tersebut, ikuti arahan tugas dosen di
`workspace/user/files/Tugas_Besar_Pemrograman_Web.docx` — jangan mengarang
fitur, tenggat, atau rubrik dari luar.

> **Batas paling penting, dibaca dulu:** Ayiriz di dokumen ini adalah
> **prototipe tugas kuliah, bukan franchise sungguhan**. Kantin, menu,
> harga, dan angka penjualan di seeder adalah **data contoh fiktif**,
> bukan data resmi UMKM mana pun. Tidak ada kerja sama franchise nyata,
> tidak ada payment gateway nyata, dan tidak ada kurir/delivery.
> Pembayaran dilakukan di kasir saat ambil pesanan.

---

## 1. Ringkasan Produk

### 1.1 Masalah yang diselesaikan

Di depan gerbang kampus UNG ada banyak kantin/UMKM F&B kecil yang
jualan sendiri-sendiri. Akibatnya:

- Mahasiswa harus datang dan antre ke tiap kantin untuk tahu menu dan
  stok hari itu; tidak ada satu tempat untuk melihat semua kantin Ayiriz.
- Pemilik kantin mencatat pesanan dan stok secara manual; pesanan yang
  masuk lewat pesan tersebar dan mudah terlewat.
- Pusat brand tidak punya gambaran gabungan: kantin mana yang ramai,
  menu apa yang laku, dan berapa bagi hasil yang menjadi haknya.
- Penilaian pelanggan, kalau ada, biasanya berupa bintang yang gampang
  dibaca sebagai ranking dan membuat kantin saling dibandingkan.

### 1.2 Solusi dalam satu kalimat

Satu website Ayiriz tempat mahasiswa melihat semua kantin mitra dan
menunya, pesan online tanpa daftar/login lalu bayar tunai di kasir saat
ambil; tempat tiap outlet mengelola menu, stok, dan pesanannya sendiri;
dan tempat pusat memantau penjualan semua outlet, menghitung royalti
10%, dan mengauditnya tiap semester.

### 1.3 Tujuan produk

1. Mahasiswa bisa melihat katalog semua kantin Ayiriz dan pesan tanpa
   daftar/login dan tanpa antre panjang.
2. Tiap outlet punya kasir dan pencatatan sendiri yang rapi: pesanan,
   stok, dan laporan outletnya.
3. Pusat punya laporan gabungan dan perhitungan bagi hasil yang bisa
   ditelusuri per outlet dan per periode.
4. Reaksi pelanggan dibaca sebagai kesan terhadap satu menu, bukan
   sebagai ranking antar kantin.
5. Sistem bisa didemokan end-to-end oleh 2 orang dalam satu semester:
   bangun, isi data contoh, presentasikan.

### 1.4 Bukan tujuan produk

- Bukan marketplace umum dan bukan aplikasi delivery — tidak ada kurir,
  tidak ada ongkir, tidak ada lacak pengiriman.
- Bukan payment gateway — tidak ada pembayaran online apa pun, QRIS
  nyata, atau dompet digital. Satu-satunya cara bayar adalah tunai di
  kasir, dikonfirmasi manual oleh kasir.
- Bukan sistem gudang pusat — tidak ada stok pusat yang didistribusikan
  ke outlet.
- Bukan sistem akuntansi lengkap — hanya penjualan, royalti, dan
  laporan sederhana.
- Bukan penentu kualitas UMKM secara resmi — reaksi emote bukan skor
  dan bukan peringkat.

---

## 2. Konsep Franchise Ayiriz (Dikunci 2026-11-16)

| Keputusan | Isi yang dikunci |
|---|---|
| Model franchise | **(b) Outlet bebas menu lokal.** Pusat tidak menentukan menu dan harga tiap outlet. Pusat hanya memantau penjualan dan menerima bagi hasil. |
| Pemesanan | **(a) Pesan online tanpa daftar/login, bayar tunai di kasir saat ambil.** Tidak ada bayar online, tidak ada payment gateway. |
| Antrean | Keranjang yang disubmit masuk **daftar tunggu (antrean)** di database agar kasir bisa melihatnya — tanpa status, tanpa mengurangi stok, belum menjadi pesanan. |
| Stok | **Masing-masing outlet.** Tiap outlet kelola stok bahan dan stok porsi menunya sendiri. Tidak ada gudang pusat. |
| Royalti | **Mulai 10% dari penjualan outlet.** Naik atau tetap diputuskan pusat dari hasil audit penjualan tiap semester. Bukan naik otomatis dan bukan rumus tersembunyi. |
| Lokasi | Kantin di depan gerbang kampus UNG. |
| Status pesanan | Hanya **2: Diproses dan Selesai**. Tidak ada Baru, Siap Diambil, atau Batal di MVP. Diproses ada setelah pelanggan membayar; sebelum bayar, pesanan hanya antrean tanpa status. |
| Reaksi pelanggan | Hanya emote **😋 / 🥵 / 🫠 per menu dalam satu pesanan yang sudah dibayar**. Bukan bintang, bukan teks ulasan, bukan ranking antar UMKM. |
| Stack | Laravel + MySQL, Laragon lokal. |
| Pembagian kerja | Teman sekelompok pegang **laporan dokumentasi tugas saja**. Aldi mengerjakan seluruh kode, termasuk modul Laporan di sistem. |

**Aturan yang mengikuti keputusan di atas:**

- Satu pesanan hanya untuk **satu outlet**. Keranjang tidak boleh
  mencampur menu dari dua kantin berbeda, karena ambil dan bayarnya
  di kasir kantin itu.
- Pelanggan memesan sebagai **tamu tanpa daftar/login**: pilih menu,
  tulis **nama pemesan** (wajib), lalu submit keranjang. Submit itu
  masuk **daftar tunggu (antrean)** di database agar kasir bisa
  melihat nama, isi, dan totalnya — tetapi antrean **bukan
  pesanan**: tanpa status, tanpa mengurangi stok porsi maupun stok
  bahan, dan tanpa royalti.
- Saat pelanggan datang dan membayar **tunai** di kasir, kasir
  mengonfirmasi pembayaran secara manual — antrean itu menjadi
  pesanan berstatus **Diproses** (stok porsi berkurang saat itu),
  outlet memasaknya, lalu mengubahnya menjadi **Selesai** saat
  pelanggan mengambil pesanannya. Pelanggan tidak bisa membatalkan
  lewat sistem; antrean yang tidak datang bisa dihapus outlet secara
  manual (lihat KF-05) — tanpa efek apa pun pada stok maupun royalti,
  karena antrean memang tidak pernah mengurangi stok.
- Royalti dihitung per pesanan yang **Selesai** saja. Pesanan yang masih
  Diproses belum dihitung royaltinya.
- Arti emote dikunci: **😋 = Enak, 🥵 = Pedas, 🫠 = Manis / menyejukkan**
  (cocok untuk cemilan/dessert dingin seperti es dan dessert). Arti ini
  tampil di samping pilihan emote, supaya tidak ditafsirkan bebas.

---

## 3. Aktor dan Kewenangan

| Aktor | Bisa | Tidak bisa |
|---|---|---|
| **Pusat Ayiriz** (owner brand, 1 akun pusat) | Daftarkan dan verifikasi outlet; aktif/nonaktifkan outlet; lihat dashboard gabungan semua outlet; lihat laporan penjualan dan royalti per outlet; buat audit semester dan putuskan royalti baru; lihat hitungan reaksi per menu (tanpa ranking) | Mengubah menu, harga, atau stok milik outlet; mengubah status pesanan milik outlet; melihat data pribadi pelanggan di luar yang perlu untuk pesanan |
| **Admin Outlet** (pemilik kantin mitra) | Kelola menu, kategori, harga, dan stok outletnya sendiri; lihat dan selesaikan pesanan outletnya; kelola stok bahan outletnya; lihat laporan dan reaksi menu outletnya sendiri | Melihat atau mengubah data outlet lain; melihat laporan gabungan pusat; mengubah persen royalti outletnya sendiri |
| **Pelanggan** (mahasiswa, tamu tanpa akun) | Lihat katalog semua outlet aktif dan menunya; pesan tanpa daftar/login (tulis nama pemesan); beri 1 reaksi emote per menu dalam 1 pesanan yang sudah dibayar (lewat QR di kasir atau kode pesanan) | Melihat pesanan pelanggan lain; memberi reaksi untuk pesanan yang belum dibayar; memberi reaksi lebih dari 1 per menu per pesanan |

Kasir **bukan role terpisah** di MVP: fungsi kasir (lihat antrean,
terima bayar tunai, konfirmasi pembayaran, tandai Selesai, cetak/lihat
struk sederhana) dikerjakan lewat akun Admin Outlet. Kalau nanti ada
pegawai kasir, tinggal tambah role — jangan dibuat sekarang.

---

## 4. Kebutuhan Fungsional

### KF-01 — Manajemen Outlet (Pusat)

Pusat mendaftarkan kantin mitra: nama outlet, nama pemilik, nomor HP,
alamat/lokasi (depan gerbang UNG), jam buka, dan persen royalti awal
(default 10%). Pusat memverifikasi outlet sebelum outlet itu tampil
di katalog publik; outlet yang belum terverifikasi atau dinonaktifkan
tidak tampil dan tidak bisa menerima pesanan. Pusat bisa mengubah
status outlet (Aktif / Nonaktif) dan melihat daftar outlet beserta
total penjualannya. Status di database ada tiga (lihat ERD §2.2):
`belum_terverifikasi`, `terverifikasi`, `nonaktif` — "Aktif" di
dokumen ini berarti `terverifikasi`.

**Kriteria terima:** outlet baru tidak tampil di katalog sebelum
diverifikasi; outlet Nonaktif hilang dari katalog dan tombol pesannya
tidak bisa dipakai.

### KF-02 — Manajemen Menu per Outlet

Tiap outlet mengelola menunya sendiri: nama menu, kategori (misal
Makanan Berat, Minuman, Cemilan/Dessert, dll.), harga, foto (opsional),
deskripsi singkat, dan status tersedia. Harga dan menu sepenuhnya
keputusan outlet — pusat tidak ikut campur, sesuai model franchise (b).
Menu yang stok porsinya 0 atau berstatus tidak tersedia tidak bisa
dipesan.

**Kriteria terima:** outlet A tidak bisa mengubah menu outlet B;
menu habis tidak bisa masuk keranjang.

### KF-03 — Katalog Publik, Pencarian, dan Filter

Pengunjung bisa melihat daftar outlet aktif dan menu tiap outlet,
mencari menu berdasarkan nama, dan memfilter berdasarkan kategori dan
rentang harga. Halaman ini bisa dibuka tanpa login. Hitungan reaksi
per menu tampil di sini (lihat KF-07) — tanpa skor total dan tanpa
urutan ranking.

**Kriteria terima:** katalog hanya menampilkan outlet Aktif dan menu
Tersedia; pencarian "geprek" menemukan menu yang namanya mengandung
kata itu.

### KF-04 — Pemesanan Online (Pelanggan Tamu)

Pelanggan **tanpa daftar/login** memilih satu outlet, memasukkan menu
ke keranjang (keranjang disimpan di session browser), menentukan
jumlah, menulis **nama pemesan** (wajib), lalu submit. Submit itu
masuk **daftar tunggu (antrean)** dengan kode antrean — bukan pesanan:
tanpa status, tanpa mengurangi stok, tetapi tercatat di database agar
kasir bisa melihat nama, isi, dan totalnya. Harga item dikunci saat
submit (`harga_saat_submit`); harga yang tersimpan tidak berubah kalau
harga menu diubah sesudahnya. Setelah submit, sistem menampilkan kode
antrean, nama pemesan, dan total — pelanggan menunjukkan kode/nama itu
di kasir. Keranjang hanya untuk satu outlet; pindah outlet berarti
keranjang lama dikosongkan — dan sistem harus memberi tahu itu dengan
jelas sebelum mengosongkan. Tidak ada riwayat pesanan pelanggan,
karena pelanggan tidak punya akun.

**Kriteria terima:** satu antrean/pesanan tidak pernah berisi menu dari
dua outlet; total sama dengan jumlah (harga saat submit × jumlah);
**stok porsi TIDAK berkurang saat antrean dibuat — baru berkurang saat
kasir mengonfirmasi pembayaran** (lihat KF-05); nama pemesan wajib
diisi; antrean yang disubmit untuk menu yang stok porsinya 0 ditolak.

### KF-05 — Kasir dan Penyelesaian Pesanan (Outlet)

Outlet melihat **daftar tunggu (antrean)** outletnya (paling baru
dulu) beserta pesanan Diproses-nya. Saat pelanggan datang dan membayar
**tunai** di kasir, kasir membuka antrean itu, memeriksa nama dan
totalnya, lalu mengonfirmasi pembayaran secara manual (dicatat sebagai
"Bayar di Kasir" — tanpa payment gateway, tanpa QRIS nyata): antrean
menjadi pesanan berstatus **Diproses** (stok porsi berkurang saat itu),
dan sistem menampilkan **QR reaksi** pesanan itu di layar kasir (lihat
KF-07). Outlet memasak pesanan, lalu mengubah status
**Diproses → Selesai** saat pelanggan mengambil pesanannya. Sistem
menampilkan struk sederhana: kode pesanan, item, total, dan royalti
10% dari total itu. Status tidak bisa kembali dari Selesai ke Diproses.
Outlet juga bisa **menghapus antrean yang tidak datang** (misal
pelanggan tidak datang): antrean hilang dari daftar tunggu, tanpa efek
apa pun pada stok maupun royalti — karena antrean memang tidak pernah
mengurangi stok. Pesanan yang sudah Diproses atau Selesai tidak bisa
dihapus.

**Kriteria terima:** hanya outlet pemilik antrean/pesanan yang bisa
mengonfirmasi pembayaran dan menyelesaikannya; pesanan Selesai tidak
bisa diubah lagi; royalti yang tampil = 10% (atau persen royalti
outlet saat itu) × total pesanan Selesai; antrean yang dihapus tidak
mengubah stok dan tidak menghitung royalti; antrean tidak bisa diubah
menjadi pesanan tanpa konfirmasi pembayaran; kalau stok porsi habis di
antara submit dan bayar, kasir melihatnya saat konfirmasi dan tidak
bisa mengonfirmasi antrean itu.

### KF-06 — Manajemen Stok (Outlet)

Ada dua stok yang dikelola outlet, dan keduanya per outlet:

1. **Stok porsi menu** — jumlah porsi yang bisa dipesan hari itu.
   Berkurang saat **kasir mengonfirmasi pembayaran** (antrean menjadi
   pesanan Diproses). Antrean tidak mengurangi stok. Menu dengan stok
   0 otomatis tidak bisa masuk antrean. Tidak ada pengembalian stok,
   karena stok hanya berkurang setelah bayar — pesanan yang sudah
   dibayar tidak bisa dihapus.
2. **Stok bahan baku** — misal ayam (kg), beras (kg), es batu, dan
   sejenisnya. Outlet mencatat stok masuk dan keluar secara manual,
   menetapkan batas minimum, dan mendapat peringatan saat stok menipis.

Resep sederhana (opsional per menu): satu menu boleh dihubungkan ke
0..n bahan dengan takaran. Kalau menu punya resep, stok bahan berkurang
otomatis saat pesanan berstatus Selesai. Kalau tidak punya resep, hanya
stok porsi yang bergerak. Tidak ada resep otomatis yang dikarang sistem.

**Kriteria terima:** stok bahan di bawah batas minimum muncul di
peringatan outlet; pesanan Selesai mengurangi stok bahan hanya untuk
menu yang punya resep; stok porsi berkurang saat konfirmasi bayar,
bukan saat antrean dibuat.

### KF-07 — Reaksi Emote (Pelanggan)

Setelah pesanan **dibayar** (pesanan berstatus Diproses atau Selesai),
pelanggan boleh memberi reaksi untuk tiap menu dalam pesanan itu:
pilih satu dari **😋 Enak, 🥵 Pedas, 🫠 Manis / menyejukkan**. Satu
pesanan = satu reaksi per menu; reaksi boleh diganti, tetapi tidak bisa
ditambah jadi dua. Caranya: saat kasir mengonfirmasi pembayaran, layar
kasir menampilkan **QR reaksi** pesanan itu — pelanggan memindai QR
itu dan langsung masuk ke halaman reaksi pesanan tersebut (tanpa
login). QR bisa dipindai sebelum makan; reaksinya boleh diberikan
setelah makan, karena QR itu hanya tautan ke halaman reaksi pesanan
tersebut. Reaksi juga bisa diberikan lewat kode pesanan di halaman
reaksi, tanpa QR. Tidak ada teks ulasan dan tidak ada bintang di MVP.

Yang tampil di halaman menu adalah **hitungan per emote** — misal
`😋 38 · 🥵 12 · 🫠 4` — tanpa total skor, tanpa rata-rata, tanpa
persen, dan tanpa pengurutan. Secara khusus dilarang: halaman
"kantin terbaik", "kantin terlaris berdasarkan reaksi", atau
pengurutan outlet berdasarkan reaksi. Reaksi adalah kesan terhadap
satu menu, bukan perbandingan antar UMKM.

**Kriteria terima:** pelanggan tidak bisa bereaksi sebelum pesanan
dibayar (antrean belum bisa diberi reaksi); reaksi kedua untuk
menu-pesanan yang sama menggantikan yang lama, bukan menambah; tidak
ada satu pun halaman yang mengurutkan outlet berdasarkan reaksi.

### KF-08 — Laporan dan Dashboard Pusat

Pusat melihat dashboard gabungan: total penjualan semua outlet (hanya
pesanan Selesai), penjualan per outlet per periode (harian, mingguan,
bulanan, per semester), menu terlaris **per outlet** (bukan antar
outlet), dan total royalti per outlet. Perbandingan penjualan antar
outlet di sini boleh, karena ini untuk audit bisnis oleh pusat — yang
dilarang adalah perbandingan berdasarkan reaksi pelanggan (KF-07).

**Kriteria terima:** total penjualan pusat = jumlah total pesanan
Selesai semua outlet pada periode itu; royalti per outlet = persen
royalti outlet × total penjualannya pada periode itu.

### KF-09 — Laporan Outlet

Tiap outlet melihat laporannya sendiri: penjualan harian/mingguan/
bulanan, menu terlaris di outletnya, stok menipis, dan hitungan reaksi
per menu di outletnya. Outlet tidak melihat laporan outlet lain.

### KF-10 — Audit Royalti Semester (Pusat)

Tiap semester, pusat membuat satu audit per outlet: sistem menampilkan
total penjualan outlet pada semester itu dan royalti yang sudah
terhitung. Pusat lalu memutuskan royalti periode berikutnya — **Tetap
10% atau Naik** (besaran kenaikan diputuskan pusat saat audit, ditulis
beserta alasannya) — dan keputusan itu dicatat dengan riwayatnya.
Royalti baru berlaku untuk pesanan Selesai pada semester berikutnya,
bukan mengubah royalti pesanan yang sudah Selesai. Tidak ada rumus
otomatis yang menaikkan royalti; keputusan dibuat manusia berdasarkan
angka audit.

**Kriteria terima:** mengubah royalti tidak mengubah royalti pesanan
lama; riwayat perubahan royalti per outlet bisa dilihat pusat; audit
semester yang sama untuk outlet yang sama tidak bisa dibuat dua kali.

### KF-11 — Akun dan Keamanan Dasar

Login hanya untuk **Pusat** dan **Admin Outlet** — akun outlet dibuat
langsung oleh pusat lewat sistem (lihat KF-01); tidak ada pendaftaran
mandiri outlet dan tidak ada pendaftaran pelanggan sama sekali.
Pelanggan memesan sebagai tamu tanpa akun. Outlet hanya melihat
outletnya sendiri; pusat melihat rekap. Password disimpan ter-hash;
halaman yang butuh login tidak bisa dibuka tanpa login.

---

## 5. Kebutuhan Non-Fungsional

| Kode | Kebutuhan |
|---|---|
| KN-01 | Laravel + Blade + Tailwind CSS; MySQL di Laragon; timezone Asia/Makassar. |
| KN-02 | Mobile-first: mahasiswa pesan dari HP. Katalog, keranjang, dan submit antrean harus nyaman di layar kecil. |
| KN-03 | Satu pesanan = satu outlet; harga saat submit dikunci di rincian pesanan. |
| KN-04 | Status pesanan hanya Diproses dan Selesai — tidak ada status lain di kode, bukan hanya disembunyikan di tampilan. Antrean bukan status pesanan — antrean tidak punya kolom status. |
| KN-05 | Reaksi tidak pernah dihitung menjadi skor, rata-rata, atau ranking antar outlet. |
| KN-06 | Semua data contoh adalah seeder fiktif dan diberi label sebagai data contoh di halaman yang relevan. |
| KN-07 | Laporan teman sekelompok (dokumentasi tugas) memakai dokumen ini dan `blueprint-Ayiriz.md` sebagai sumber spesifikasi — angka dan nama fitur di laporan harus sama dengan di sini. |

---

## 6. Data Contoh (Seeder, Fiktif)

Seeder berisi 3–4 outlet contoh di depan gerbang UNG, masing-masing
dengan 5–8 menu contoh harga standar anak kuliahan (contoh rentang
Rp5.000–Rp25.000), beberapa pesanan contoh berstatus Diproses dan
Selesai (masing-masing dengan **nama pemesan**), 1–2 antrean contoh
(belum dibayar), stok bahan contoh, dan reaksi contoh. Nama outlet dan
menu di seeder adalah contoh — bukan nama UMKM sungguhan. Akun contoh:
1 pusat, 2–3 outlet. **Tidak ada akun pelanggan** — pelanggan adalah
tamu.

---

## 7. Di Luar Lingkup (Tidak Dibangun)

Delivery/kurir, ongkir, lacak pengiriman, payment gateway/QRIS nyata,
pembayaran online apa pun, voucher/promo, poin loyalitas, gudang pusat,
resep otomatis tanpa input outlet, teks ulasan, bintang/ranking antar
outlet, multi-cabang per outlet, pendaftaran/login pelanggan, dan
integrasi ke sistem kampus mana pun.

---

## 8. Tahapan (3 Tahap)

Karena kelompok hanya 2 orang dan Aldi mengerjakan seluruh kode,
pengembangan dibagi 3 tahap. Ke-11 KF muncul tepat sekali.

### Tahap 1 — Inti (Transaksi)

KF-11 Akun dan Keamanan Dasar → KF-01 Manajemen Outlet →
KF-02 Manajemen Menu → KF-03 Katalog → KF-04 Pemesanan Online →
KF-05 Kasir dan Penyelesaian Pesanan.

Demo end-to-end sudah bisa setelah tahap ini: pesan (tamu) → antre →
bayar tunai di kasir → Diproses → ambil → Selesai.

**Revisi Model B 2026-11-24:** Tahap 1 dikerjakan ulang (rework) ke
model tamu + antrean + bayar tunai di atas sebelum Tahap 2 dimulai —
KF-nya tetap sama (KF-11, KF-01–KF-05), yang berubah adalah cara
pemesanan, pembayaran, dan statusnya.

### Tahap 2 — Operasional Outlet

KF-06 Manajemen Stok → KF-07 Reaksi Emote → KF-09 Laporan Outlet.

### Tahap 3 — Pusat dan Audit

KF-08 Laporan dan Dashboard Pusat → KF-10 Audit Royalti Semester.

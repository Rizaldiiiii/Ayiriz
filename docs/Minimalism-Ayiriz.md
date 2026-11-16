# Minimalism UI Design System — Ayiriz

Color Palette, Typography & Visual Guidelines

**Untuk:** Sistem Informasi Pengelolaan UMKM Lokal Ayiriz
**Brand / franchise:** Ayiriz — kantin-kantin mitra di depan gerbang kampus Universitas Negeri Gorontalo (UNG)
**Pengguna:** Pelanggan (mahasiswa), Admin Outlet (pemilik kantin mitra), Pusat Ayiriz (owner brand)
**Tema:** Light saja (tidak ada dark mode)
**Stack:** Laravel + Blade + Tailwind CSS + Alpine.js
**Dokumen pendamping:** `Motion-Ayiriz.md` (aturan animasi; ringkasannya ada di Bagian 9 dokumen ini), `PRD-Ayiriz.md`, `blueprint-Ayiriz.md`, `ERD-Ayiriz.md`, `AGENTS.md`, `DESIGN.md`

**Arah visual:** Minimalism yang hangat dan lapar-mata — kanvas krem yang
lega, satu warna tindakan yang jelas, tipografi besar yang berani, dan
hampir tanpa dekorasi. Permukaannya datar dan solid: tidak ada permukaan
transparan, tidak ada `backdrop-blur`, tidak ada shadow ganda.

**Referensi visual yang dipakai Aldi:**

1. **Palet Indomie** — 6 warna di Bagian 2, diambil dari warna mi instan Indomie.
2. **Hard Lines** (`docs/referensi/hard-lines.jpeg`) —
   kanvas krem yang lega, judul dan harga merah yang tegas, kartu produk
   yang rapi sejajar, dan strip marquee merah berisi teks berulang.
3. **Plately** (`docs/referensi/plately.jpeg`) — satu bidang warna penuh
   (oranye), kartu krem di tengahnya, judul raksasa satu kata, dan
   kartu menu dengan blok warna solid (hijau / oranye / kuning) di
   belakang foto makanan.

**Cara memakai dokumen ini:** Dokumen ini berdiri sendiri untuk urusan
desain visual Ayiriz. Bila ada hal yang tidak diatur di sini atau di
file penyerta tersebut, ikuti spesifikasi sistem di `PRD-Ayiriz.md` —
jangan mencari sumber desain lain di luar daftar itu. Contoh outlet,
menu, dan data di dokumen ini adalah contoh; sesuaikan dengan
spesifikasi sistem tersebut sebelum dipakai produksi.

**Yang dilarang (wajib):** permukaan transparan, `backdrop-blur`, dan
shadow ganda (dua bayangan berlawanan arah). Daftar class yang dipakai
ada di Bagian 11.4. Setelah implementasi, pencarian `backdrop-blur` di
seluruh `resources/` harus menghasilkan 0.

## 1. Design Philosophy

Desain menggunakan pendekatan Minimalism: sedikit elemen, tiap elemen
punya alasan. Kanvas krem yang luas, permukaan putih yang datar, garis
tipis yang rapi, dan warna-warna Indomie yang hanya muncul di tempat
yang memang perlu menarik mata — tombol utama, harga, status, dan satu
bidang hero. Bukan dekoratif berlebihan, bukan pula polos sampai hambar.

Karakteristik utama:

- Permukaan datar: putih solid di atas krem, bukan kaca transparan.
- Satu warna tindakan yang dominan (merah cabai), satu warna sekunder
  (biru gelap), dan kuning/oranye/hijau hanya sebagai aksen.
- Tipografi sebagai karakter utama: judul Lobster yang playful untuk
  brand, heading condensed yang tegas, dan isi yang mudah dibaca.
- Ruang yang lega: jarak antarkelompok lebih besar daripada jarak di
  dalam kelompok, dan satu elemen dominan per layar.
- Kartu menu yang foto makanannya jadi bintang — seperti kartu produk
  Hard Lines dan kartu menu Plately — bukan kartu yang penuh teks.

Prinsip utama: **lega dilihat, tegas dibaca, jelas ditekan.** Warna
dan huruf besar boleh berani, tetapi hanya di sedikit tempat.

### 1.1 Aturan anti-ramai

Satu halaman tidak boleh dipadatkan sampai semua informasi muat tanpa
scroll dengan cara mengecilkan font, merapatkan padding, atau menumpuk
kartu.

Bila konten terasa penuh, lakukan ini sesuai urutan:

1. Tentukan satu tugas utama halaman itu, dan letakkan di posisi paling
   dominan.
2. Pindahkan konten sekunder ke tab, accordion, halaman detail, atau
   di bawah lipatan (scroll vertikal itu wajar).
3. Gunakan pagination atau filter pada tabel, bukan menampilkan semua
   baris sekaligus.
4. Sembunyikan kolom sekunder di layar sempit, bukan mengecilkan
   semuanya.
5. Terakhir, kurangi dekorasi — bukan kurangi keterbacaan. Bila warna
   membuat halaman terasa ramai, kurangi jumlah bidang warna dulu,
   jangan kurangi ukuran teks.

Scroll vertikal bukan kegagalan desain. Halaman yang sesak dan sulit
dipindai adalah kegagalan desain.

### 1.2 Aturan minimalism yang tidak boleh dilanggar

1. Teks tidak pernah diletakkan langsung di atas foto makanan tanpa
   panel solid di belakangnya. Judul di atas foto memakai panel krem/
   putih solid, bukan teks yang menimpa foto.
2. Satu layar di dalam aplikasi (dashboard, kasir, laporan) memakai
   maksimal **satu** bidang warna penuh. Bidang warna penuh hanya
   boleh ada di hero publik dan strip marquee — lihat Bagian 2.5.
3. Teks kecil (12–13px) hanya boleh ada di atas permukaan solid
   (krem, putih, atau tint warna), bukan di atas bidang warna kuat.
4. Tombol utama selalu solid. Ia tidak transparan, tidak memakai blur,
   dan tidak memakai gradien.
5. Jangan memakai gradien pelangi, gradien gelap, atau gradien pada
   teks judul. Gradien hanya boleh tipis dan satu arah di hero publik
   bila benar-benar perlu — dan lebih baik tidak dipakai sama sekali.
6. Jangan meniru foto referensi secara harfiah: jangan menyalin logo,
   nama, atau trade dress Hard Lines / Plately / Indomie. Yang diambil
   adalah polanya — kanvas lega, judul besar, kartu sejajar, strip
   marquee — bukan mereknya.

---

## 2. Color Palette

Palet warna adalah **palet Indomie** — 6 warna dari gambar referensi
Aldi. Tidak ada warna brand lain yang ditambahkan, kecuali netral
hangat turunan krem untuk teks dan garis.

### 2.1 Warna Utama

| Token | HEX | Peran | Penggunaan |
|---|---|---|---|
| Krem | `#FFF8E7` | Background utama | Kanvas halaman, latar hero |
| Merah Cabai | `#B71F30` | Tindakan utama | Tombol utama, harga, link, navigasi aktif, judul brand |
| Biru Gelap | `#28397F` | Sekunder / gelap | Heading gelap, link sekunder, tombol sekunder, teks di atas kuning |
| Hijau Gelap | `#14502E` | Sukses / selesai | Status Selesai, sukses, badge hijau |
| Kuning | `#FEC345` | Highlight | Badge, harga promo (bila ada), blok kartu menu, strip aksen |
| Oranye | `#DE652E` | Aksen hangat | Status Diproses, CTA sekunder, bidang hero, strip marquee |

Aturan peran warna:

- **Merah cabai adalah satu-satunya warna tombol utama.** Jangan
  membuat tombol utama biru, hijau, atau oranye di halaman yang sama.
- **Biru gelap adalah warna gelap kedua** — untuk heading, teks di
  atas kuning, dan tombol sekunder. Ia bukan background halaman.
- **Kuning dan oranye adalah aksen, bukan warna teks utama.** Kuning
  hanya dipakai dengan teks gelap di atasnya; oranye hanya dipakai
  dengan teks gelap di atasnya untuk teks kecil (lihat Bagian 2.6).
- **Hijau hanya untuk makna sukses/selesai** (dan tag hijau di kartu
  menu, meniru Plately). Jangan memakai hijau sebagai warna brand
  umum.

### 2.2 Background & Surface Colors

| Token | Nilai | Penggunaan |
|---|---|---|
| Background | `#FFF8E7` | Base halaman |
| Surface | `#FFFFFF` | Kartu, panel, modal, dropdown |
| Surface Krem | `#FFFDF4` | Kartu sekunder di atas krem, area form |
| Surface Tint Merah | `#FDECEC` | Latar badge/reaksi 😋, latar error ringan |
| Surface Tint Biru | `#E6EAF6` | Latar badge info, latar info |
| Surface Tint Hijau | `#E2EFE6` | Latar badge Selesai/sukses |
| Surface Tint Kuning | `#FFF3D6` | Latar badge/reaksi 🫠, highlight ringan |
| Surface Tint Oranye | `#FDEDE3` | Latar badge Diproses, latar reaksi 🥵 |
| Surface Gelap Biru | `#28397F` | Bidang gelap sesekali (footer, band statistik) — teks di atasnya putih |
| Surface Gelap Hijau | `#14502E` | Bidang gelap alternatif — teks di atasnya putih |

Catatan: tidak ada permukaan transparan. Semua surface di atas adalah
solid. Bila sebuah kartu terasa "menempel" ke background krem, yang
ditambahkan adalah border 1px dan shadow SM — bukan transparansi.

### 2.3 Neutral Colors

Netral di sini diturunkan dari krem (hangat), bukan abu-abu dingin.

| Token | HEX | Penggunaan |
|---|---|---|
| Text Primary | `#211D18` | Judul dan teks utama |
| Text Secondary | `#5A5245` | Deskripsi dan informasi pendukung |
| Text Muted | `#8A8171` | Placeholder dan metadata (bukan untuk teks penting yang kecil) |
| Text Disabled | `#B8AE9C` | Teks nonaktif saja; jangan dipakai untuk informasi bermakna |
| Border | `#EADFC2` | Border utama kartu dan pemisah |
| Border Kuat | `#D9C89E` | Border input bila harus terlihat jelas |
| Divider | `#EFE6CC` | Garis pemisah antarbagian |

### 2.4 Status Colors

Warna status memakai pasangan teks + background solid (bukan warna saja,
bukan teks berwarna di atas krem polos).

| Token | Teks | Background | Dot | Penggunaan |
|---|---|---|---|---|
| Diproses | `#8A3410` | `#FDEDE3` | `#DE652E` | Pesanan Diproses |
| Selesai | `#14502E` | `#E2EFE6` | `#14502E` | Pesanan Selesai, sukses |
| Habis / Nonaktif | `#5A5245` | `#EFE6CC` | `#8A8171` | Menu habis, outlet nonaktif |
| Error | `#B71F30` | `#FDECEC` | `#B71F30` | Gagal, error, hapus |
| Info | `#28397F` | `#E6EAF6` | `#28397F` | Informasi |
| Peringatan Stok | `#7C4A03` | `#FFF3D6` | `#FEC345` | Stok menipis |

Status pesanan Ayiriz hanya ada dua (Diproses dan Selesai, lihat
`PRD-Ayiriz.md` KF-04/KF-05). Jangan menambah status lain hanya karena
tabel ini punya baris lain — baris lain adalah untuk stok, error, dan
info.

### 2.5 Bidang Warna Penuh dan Strip Marquee (hanya publik)

Dua pola dari referensi yang **hanya boleh ada di halaman publik**
(beranda/katalog), bukan di dalam aplikasi:

- **Bidang warna penuh (Plately):** satu section hero memakai satu
  warna penuh — oranye `#DE652E` atau merah `#B71F30` — dengan kartu
  krem `#FFF8E7` di tengahnya dan judul Lobster di atas kartu itu.
  Teks di atas bidang oranye memakai krem/putih hanya untuk judul
  besar (Lobster, ≥ 32px, bold visual); teks kecil di atas bidang
  oranye memakai panel krem solid di belakangnya.
- **Strip marquee (Hard Lines):** satu strip tipis (36–44px) merah
  `#B71F30` dengan teks krem berulang, misal
  `AYIRIZ • DEPAN GERBANG UNG • PESAN ONLINE • BAYAR DI KASIR •`
  yang bergerak pelan dari kanan ke kiri. Hanya satu strip per
  halaman publik, hanya di bawah hero atau di atas footer. Gerakannya
  diatur di `Motion-Ayiriz.md` Bagian 13 — pelan, berhenti saat
  hover, dan mati total saat `prefers-reduced-motion`.
- **Blok warna kartu menu (Plately):** tiap kartu menu boleh punya
  blok warna solid di belakang foto — hijau `#14502E`, oranye
  `#DE652E`, atau kuning `#FEC345`, bergiliran — tetapi foto dan
  nama/harga menu tetap di atas putih/krem solid, bukan di atas blok
  warna. Jangan memberi tiap kartu warna yang berbeda-beda tanpa pola
  bergiliran; maksimal 3 warna blok dalam satu grid.

Di dalam aplikasi (dashboard outlet/pusat, kasir, laporan, form),
tidak ada bidang warna penuh dan tidak ada marquee. Kanvasnya krem,
kartunya putih, aksennya hanya tombol, harga, dan status.

### 2.6 Pemeriksaan Kontras (WCAG AA)

- Teks normal `#211D18` di atas `#FFF8E7` dan `#FFFFFF` memenuhi
  kontras minimal 4,5:1. Teks sekunder `#5A5245` hanya untuk teks
  14px ke atas.
- Teks putih hanya boleh di atas merah `#B71F30`, biru `#28397F`,
  dan hijau `#14502E` (ketiganya lolos AA untuk teks normal).
- **Teks putih tidak boleh di atas kuning `#FEC345` dan oranye
  `#DE652E` untuk teks normal** — kontrasnya tidak mencapai 4,5:1.
  Di atas kuning/oranye, teks memakai `#211D18` (kuning) atau
  `#211D18` / putih hanya untuk judul besar ≥ 24px bold (minimal
  3:1). Tombol oranye, bila ada, memakai teks `#211D18`, bukan putih.
- Teks muted `#8A8171` tidak boleh dipakai untuk informasi penting.
- Link memakai merah `#B71F30` dengan underline saat hover, atau
  biru `#28397F` untuk link sekunder. Jangan memakai warna saja
  sebagai satu-satunya penanda link di dalam paragraf — tambahkan
  underline.

Target kontras: teks normal minimal 4,5:1; teks besar minimal 3:1;
komponen UI dan indikator grafis penting minimal 3:1 terhadap warna
di sekitarnya.

---

## 3. Shadow System (Datar)

Shadow di sini adalah satu bayangan lembut ke bawah, tipis, dan
berwarna biru gelap yang sangat transparan.

### 3.1 Standard Shadows

| Token | CSS Value | Penggunaan |
|---|---|---|
| Shadow XS | `0 1px 2px rgba(40, 57, 127, 0.06)` | Chip, badge kecil |
| Shadow SM | `0 1px 3px rgba(40, 57, 127, 0.08), 0 1px 2px rgba(40, 57, 127, 0.06)` | Kartu biasa |
| Shadow MD | `0 4px 12px rgba(40, 57, 127, 0.10)` | Kartu hover, dropdown |
| Shadow LG | `0 12px 32px rgba(40, 57, 127, 0.14)` | Modal |

Kartu yang tidak bisa diklik memakai Shadow SM dan tidak berubah saat
hover. Kartu yang bisa diklik memakai Shadow SM, lalu Shadow MD saat
hover (lihat Bagian 9).

### 3.2 Button States

Tombol utama tetap solid, jadi state-nya sederhana:

- Default: merah `#B71F30`, teks putih, shadow XS.
- Hover: merah lebih gelap `#9E1B29`, naik 1px (`translateY(-1px)`),
  shadow SM.
- Active / Pressed: merah paling gelap `#7F1520`, skala 0.97,
  shadow XS.

Tombol sekunder:

- Default: putih, border `#EADFC2`, teks biru `#28397F`, shadow XS.
- Hover: latar `#E6EAF6`, naik 1px, shadow SM.
- Active: skala 0.97, shadow XS.

Catatan: shadow tidak boleh menjadi satu-satunya penanda interaksi.
Selalu ada perubahan lain yang terlihat: warna, ikon, label, atau
outline fokus (lihat Bagian 10).

---

## 4. Typography System

Font di sini adalah keputusan Aldi — tiga peran yang berbeda, jangan
dicampur.

### 4.1 Recommended Font Families

**Judul Brand / Display: Lobster**

Digunakan untuk:

- Wordmark "Ayiriz" di header dan hero.
- Judul besar hero publik (satu baris, misal "Ayiriz.").
- Judul besar sesekali di halaman publik (bukan di dalam aplikasi).

Karakteristik: script/display yang playful dan lapar-mata — cocok
untuk brand F&B, tetapi sulit dibaca untuk teks panjang. Karena itu
Lobster **hanya untuk judul besar dan brand**, tidak untuk heading
isi, tidak untuk tombol, dan tidak untuk body.

Google Fonts: https://fonts.google.com/specimen/Lobster

**Heading: Coolvetica Condensed**

Digunakan untuk:

- H1–H3 di semua halaman (publik dan aplikasi).
- Judul kartu, judul section, nama outlet di katalog.
- Angka statistik yang ditonjolkan (boleh, bila perlu karakter
  condensed yang tegas).

Karakteristik: condensed, tegas, dan hemat tempat — cocok untuk judul
menu dan angka, serta meniru judul besar di referensi Plately/Hard
Lines tanpa harus memakai serif.

**Isi: Coolvetica**

Digunakan untuk:

- Body text, form labels, input, isi tabel, deskripsi menu, metadata.

Karakteristik: geometric sans yang bersih dan mudah dibaca, satu
keluarga dengan heading sehingga halaman terasa menyatu.

**Ketersediaan dan fallback (penting):**

- Lobster tersedia di Google Fonts — muat dari sana.
- Status saat ini (2026-11-16): proyek Laravel Ayiriz belum dibuat
  dan file `woff2` Coolvetica belum ada — pilihan font ini masih
  sebatas konsep. Sampai file itu disediakan, fallback di bawah yang
  dipakai sebagai font utama saat implementasi awal.
- Coolvetica dan Coolvetica Condensed **tidak ada di Google Fonts**.
  Sediakan file font-nya sendiri (`woff2`) di `public/fonts/` dan
  daftarkan lewat `@font-face` (lihat Bagian 11.1). Bila file itu
  belum ada, fallback berikut yang dipakai — dan ini sudah disetujui
  Aldi ("yang penting dia font condensed aja" untuk heading):
  - Heading fallback: `"Coolvetica Condensed", "Oswald", "Archivo Narrow", "Arial Narrow", sans-serif` — Oswald dari Google Fonts adalah pengganti condensed yang paling dekat.
  - Body fallback: `"Coolvetica", "Archivo", "Inter", ui-sans-serif, system-ui, sans-serif`.
  - Jangan memakai fallback serif (misal Georgia) untuk heading atau
    body — itu mengubah karakter desain.
- Periksa lisensi file Coolvetica sebelum dipakai produksi. Bila
  lisensinya tidak mengizinkan pemakaian web, pakai fallback Oswald
  (heading) + Archivo/Inter (body) sebagai font utama dan catat itu
  di `DESIGN.md` proyek.

### 4.2 Typography Scale

| Elemen | Font | Size | Weight | Line Height |
|---|---|---|---|---|
| Display (hero/brand) | Lobster | 48–64px (mobile 40px) | 400 | 1.05 |
| H1 | Coolvetica Condensed | 32px | 700 | 1.15 |
| H2 | Coolvetica Condensed | 26px | 700 | 1.2 |
| H3 | Coolvetica Condensed | 21px | 700 | 1.25 |
| H4 | Coolvetica Condensed | 18px | 700 | 1.3 |
| Body Large | Coolvetica | 16px | 400 | 1.6 |
| Body | Coolvetica | 14px | 400 | 1.6 |
| Body Small | Coolvetica | 13px | 400 | 1.5 |
| Caption | Coolvetica | 12px | 400 | 1.4 |
| Button | Coolvetica Condensed | 15px | 700 | 1.2 |
| Harga / Statistic | Coolvetica Condensed | 24–28px | 700 | 1.2 |

Catatan:

- Lobster hanya punya weight 400 — jangan memaksakan `font-weight:
  700` pada Lobster; besarnya yang membuatnya dominan, bukan
  weight-nya.
- Heading Coolvetica Condensed boleh memakai huruf kapital di awal
  kata (title case) atau seluruhnya kapital untuk judul section
  pendek (misal "MENU HARI INI", "PESANAN MASUK"). Jangan memakai
  all-caps untuk kalimat panjang.
- Teks dasar adalah 14px (Body), dengan 16px untuk Body Large pada
  paragraf penjelas yang penting. Jangan mengecilkan body di bawah
  14px untuk memuat lebih banyak konten dalam satu layar.

### 4.3 Typography Color

| Elemen | Color |
|---|---|
| Display / Brand | `#B71F30` (di atas krem/putih); krem `#FFF8E7` bila di atas bidang merah/oranye |
| Main Heading | `#211D18`, atau `#28397F` untuk heading section sekunder |
| Body Text | `#211D18` |
| Secondary Text | `#5A5245` |
| Placeholder | `#8A8171` |
| Link | `#B71F30` (primer), `#28397F` (sekunder) |
| Harga | `#B71F30` |
| Disabled | `#B8AE9C` |

---

## 5. Component Design Guidelines

Aturan umum: **jangan memaksakan komponen.** Bagian 5.1–5.10 adalah
komponen inti yang hampir pasti dipakai di Ayiriz. Bagian 5.11 adalah
komponen kondisional: hanya didokumentasikan dan dibangun bila ada
fitur yang benar-benar memakainya. Komponen pajangan tanpa data dan
tanpa aksi tidak dibuat.

Setiap komponen inti harus memiliki state yang dirancang: default,
hover, focus-visible, active/pressed, disabled, loading (bila ada
aksi), error (bila ada input), dan empty (bila menampilkan data).

### 5.1 Cards

- Border radius: 16px untuk kartu utama, 12px untuk kartu kecil di
  dalamnya.
- Background: putih `#FFFFFF` solid, border 1px `#EADFC2`.
- Shadow: SM (Bagian 3.1).
- Padding: 20–24px (lega).
- **Kartu menu (katalog):** foto makanan di atas (rasio 4:3, sudut
  atas membulat mengikuti kartu), blok warna solid di belakang foto
  boleh dipakai bergiliran (hijau/oranye/kuning, Bagian 2.5), lalu
  nama menu (Coolvetica Condensed 21px), harga (merah, 24px), dan
  tombol pill "Tambah" merah. Hitungan reaksi tampil di bawah nama
  menu sebagai tiga chip kecil (😋 n · 🥵 n · 🫠 n) — tanpa skor total.
- **Kartu outlet:** logo/inisial outlet di kotak warna solid, nama
  outlet (Condensed 21px), jam buka + status Aktif/Nonaktif, dan
  tombol "Lihat Menu".
- Satu kartu utama per layar boleh lebih besar daripada kartu
  pendukung.
- Jangan menumpuk lebih dari 4 kartu menu setara dalam satu baris di
  desktop tanpa alasan; bila daftarnya panjang, pakai pagination
  atau filter kategori.

### 5.2 Buttons

- Border radius: **pill (999px)** untuk tombol utama dan tombol
  "Tambah" di katalog (meniru tombol pill Plately); 10px untuk tombol
  kecil di dalam tabel/form bila pill terasa terlalu membulat.
- Height: 44–48px (tombol utama), 36–40px (tombol kecil).
- Primary button: merah `#B71F30`, teks putih, tanpa gradien, tanpa
  blur. Hover `#9E1B29`, active `#7F1520` + skala 0.97.
- Secondary button: putih, border `#EADFC2`, teks biru `#28397F`.
  Hover latar `#E6EAF6`.
- Tombol "Tambah" di kartu menu: pill merah kecil (36px) dengan ikon
  `+` dan label "Tambah"; setelah ditambah, berubah menjadi stepper
  jumlah (− 1 +) dengan warna yang sama.
- Ghost/text button hanya untuk aksi tersier; warnanya biru.
- Destructive button memakai merah `#B71F30` juga — bedakan dari
  tombol utama lewat label yang jelas ("Hapus") dan konfirmasi; jangan
  membuat warna destruktif baru.
- Tombol loading mempertahankan lebarnya, menampilkan spinner, dan
  menonaktifkan klik ganda.

### 5.3 Input Fields

- Border radius: 12px.
- Background: putih `#FFFFFF` solid.
- Border: 1px solid `#D9C89E`; saat focus, border biru `#28397F`
  2px + outline merah tipis tidak perlu — cukup border biru + shadow
  SM.
- Height: 44–48px.
- Focus state harus terlihat jelas, bukan hanya perubahan warna
  border: tambahkan outline `#B71F30` 2px dengan offset 2px untuk
  keyboard focus (`:focus-visible`).
- Label selalu terlihat di atas field, bukan hanya placeholder.
- Pesan error menempel pada field-nya, memakai warna Error dan ikon,
  di atas latar `#FDECEC`.

### 5.4 Modal

- Border radius: 16px.
- Background: putih `#FFFFFF` solid.
- Shadow: LG.
- Overlay: `rgba(33, 29, 24, 0.45)` (hitam hangat transparan).
- Pastikan fokus keyboard tidak hilang dan bisa kembali ke pemicu
  modal saat modal ditutup. Lihat animasi modal di
  `Motion-Ayiriz.md` Bagian 7.
- Modal dipakai untuk konfirmasi (misal konfirmasi Selesai, konfirmasi
  kosongkan keranjang saat pindah outlet) dan form pendek saja.
  Form yang panjang memakai halaman sendiri.

### 5.5 Navigation

**Publik: top bar sticky.**

- Top bar krem `#FFF8E7` solid + border bawah `#EADFC2`, menempel di
  atas (`sticky`), tinggi 64px.
- Kiri: wordmark "Ayiriz" Lobster 28px merah `#B71F30`.
- Tengah/kanan: pencarian, link Katalog, dan ikon keranjang dengan
  badge jumlah (badge merah, teks putih).
- Saat scroll, top bar tetap krem solid — tidak ada blur yang membuat
  isi di belakangnya tembus.

**Aplikasi (outlet/pusat/pelanggan): sidebar kiri di desktop.**

- Sidebar putih solid + border kanan `#EADFC2`.
- Navigasi aktif: latar `#FDECEC` + teks merah `#B71F30` + garis kiri
  merah 3px; nonaktif: teks `#5A5245`.
- Icon navigasi aktif merah; nonaktif teks sekunder.
- Label memakai Coolvetica Condensed untuk judul section sidebar dan
  Coolvetica untuk item menu.
- Badge role (Pusat / Outlet / Pelanggan) tampil di area profil
  sidebar.
- Contoh menu (sesuaikan dengan fitur nyata, bukan daftar wajib):
  pelanggan — Beranda, Katalog, Pesanan Saya; outlet — Beranda,
  Pesanan Masuk, Menu, Stok, Laporan; pusat — Beranda, Outlet,
  Laporan, Audit Royalti. Menu yang tidak dimiliki suatu role
  disembunyikan, bukan hanya dinonaktifkan tanpa penjelasan.
- Mobile: pelanggan memakai bottom tab bar (maksimal 4: Beranda,
  Katalog, Pesanan, Profil); outlet/pusat memakai drawer.

### 5.6 Tables

- Background area tabel: putih `#FFFFFF` solid.
- Divider halus (`#EFE6CC`).
- Header: teks Text Primary, font Coolvetica Condensed 600,
  background `#FFF3D6` (kuning tint) atau `#FFF8E7` — pilih satu dan
  pakai konsisten di semua tabel.
- Tinggi baris 52–56px.
- Hover state ringan (`#FFFDF4`).
- Status badge dengan warna status solid (Bagian 2.4).
- Kolom angka (harga, total, royalti) rata kanan dengan angka tabular
  (Coolvetica Condensed, tabular).
- Format locale Indonesia: `Rp 12.000`, tanggal `12 Okt 2026`.
- Di mobile, tabel diubah menjadi kartu (satu baris = satu kartu),
  bukan tabel yang mengecil.

### 5.7 Dropdown

- Radius 12px, background putih solid, shadow MD, border `#EADFC2`.
- Item aktif/hover memakai `#FDECEC` (merah tint) atau `#E6EAF6`.
- Panah indikator berputar saat terbuka (lihat `Motion-Ayiriz.md`
  Bagian 6).
- Dropdown tidak boleh menutupi field pemicunya tanpa alasan.

### 5.8 Toast / Notifikasi

- Radius 12px, background putih solid, shadow LG, border kiri 4px
  warna status (merah/hijau/biru/oranye).
- Ikon dan label status wajib ada; warna saja tidak cukup.
- Posisi konsisten di seluruh aplikasi (kanan atas desktop, atas
  tengah mobile); durasi tampil cukup untuk dibaca, dan bisa ditutup
  manual.
- Contoh pemakaian yang sah: pesanan berhasil dibuat, pesanan ditandai
  Selesai, stok berhasil disimpan. Toast dekoratif tidak dibuat.

### 5.9 Tabs

- Tab aktif memakai teks merah `#B71F30` dengan garis bawah merah 2px
  atau background `#FDECEC`; tab nonaktif memakai Text Secondary.
- Tab dipakai untuk memecah halaman yang ramai (misal detail outlet:
  Menu, Ulasan Reaksi, Info) daripada menumpuk semuanya dalam satu
  scroll panjang.
- Panel isi tab memakai kartu putih biasa.

### 5.10 Brand: Logo dan Nama Ayiriz

Wajib ada, dan perlakuannya khusus:

- **Logo:** wordmark "Ayiriz" dengan font Lobster, warna merah
  `#B71F30` di atas krem/putih. Tidak ada logo gambar UNG di Ayiriz —
  jangan memakai `LOGO UNG (1).png` di proyek ini. Bila nanti ada
  logo gambar Ayiriz, ia menggantikan wordmark hanya bila sudah ada
  file resminya; sampai saat itu wordmark Lobster adalah logonya.
- **Penempatan:** kiri top bar (publik) dan atas sidebar (aplikasi).
  Di sidebar, wordmark 24–28px. Di halaman login, wordmark 40–48px
  di atas form.
- **Nama:** tulis "Ayiriz" saja sebagai brand. Nama lengkap sistem
  (`Sistem Informasi Pengelolaan UMKM Lokal Ayiriz`) hanya ditulis di
  footer dan halaman login sebagai subjudul kecil, bukan di samping
  logo setiap saat.
- **Latar logo:** selalu di atas permukaan terang solid (`#FFF8E7`
  atau putih). Jangan menaruh wordmark merah di atas bidang oranye/
  kuning tanpa panel krem solid di belakangnya, dan jangan menaruhnya
  di atas foto.
- **Penempatan di proyek Laravel:** font Lobster dimuat dari Google
  Fonts; Coolvetica/Coolvetica Condensed dari `public/fonts/` (lihat
  Bagian 11.1).

### 5.11 Komponen Kondisional (hanya bila fiturnya ada)

| Komponen | Dibuat bila | Contoh di Ayiriz |
|---|---|---|
| Stepper jumlah | Keranjang / tambah menu | − 1 + di kartu menu dan keranjang |
| Badge reaksi | Ada reaksi emote | 😋 n · 🥵 n · 🫠 n di kartu menu |
| Stepper alur | Alur multi-langkah yang berurutan | Checkout (Keranjang → Konfirmasi → Selesai dipesan) |
| Breadcrumb | Halaman detail yang dalam | Beranda / Outlet / Detail Menu |
| Pagination | Daftar melebihi satu layar | Katalog menu, tabel pesanan |
| Grafik | Ada kebutuhan rekap/visualisasi | Penjualan per outlet untuk pusat (bila diminta) |
| Kalender | Ada tenggat atau jadwal yang dipilih pengguna | Filter laporan per periode (bila diminta) |
| Upload foto | Ada foto menu | Upload foto menu outlet |

Bila fitur di kolom tengah tidak ada, komponennya tidak dibuat dan
tidak perlu dicari-cari alasannya.

### 5.12 Panel Data Contoh (wajib, khusus Ayiriz)

Ayiriz adalah prototipe tugas kuliah dan datanya adalah seeder fiktif.
Penandanya harus ada, tetapi ringan — bukan panel besar:

- Outlet/menu/pesanan contoh memakai chip kecil **"Data Contoh"**
  (background `#FFF3D6`, teks `#7C4A03`, radius pill) di dekat nama
  outlet atau di footer halaman katalog — cukup satu chip per halaman
  yang datanya seeder, bukan di tiap baris.
- Isi chip tidak diubah oleh pergantian tema. Jangan menambah
  penjelasan panjang di tiap baris tabel.
- Jangan memindahkan penanda ini ke modal, toast, atau tooltip.

---

## 6. Border Radius System

Kepribadian sudut: **membulat ramah, tetapi tegas** — meniru kartu
Plately dan tombol pill, tanpa menjadi bulat berlebihan.

| Token | Value | Penggunaan |
|---|---|---|
| Radius XS | 6px | Elemen sangat kecil (checkbox, dot) |
| Radius SM | 8px | Badge, chip kecil |
| Radius MD | 12px | Input, dropdown, kartu kecil |
| Radius LG | 16px | Kartu utama, modal, panel besar |
| Radius Full | 999px | Tombol utama/katalog, pill, avatar |

Aturan sudut bersarang: radius dalam = radius luar − padding. Jangan
memakai radius 20px ke atas pada kartu, kecuali kartu hero publik.

---

## 7. Spacing System

Menggunakan kelipatan 4px sebagai basis.

| Token | Value |
|---|---|
| 1 | 4px |
| 2 | 8px |
| 3 | 12px |
| 4 | 16px |
| 5 | 20px |
| 6 | 24px |
| 8 | 32px |
| 10 | 40px |
| 12 | 48px |
| 16 | 64px |

Rekomendasi (kepadatan seimbang — tidak selega halaman publik, tidak sepadat
tabel kasir):

- Card padding: 20–24px.
- Section spacing: 32–48px.
- Form field spacing: 16–20px.
- Component internal spacing: 12–16px.
- Page padding desktop: 32px; tablet: 24px; mobile: 16px.
- Gap di dalam kelompok < gap antarkelompok < gap antarbagian.
- Grid katalog: gap 16px (mobile) / 24px (desktop).

---

## 8. Iconography

Rekomendasi: Lucide Icons.

Website: https://lucide.dev/

Panduan:

- Default size: 20px; small 16px; large 24px.
- Stroke width: 1.8–2px.
- Gunakan warna merah `#B71F30` untuk ikon aksi utama, biru
  `#28397F` untuk ikon sekunder, dan Text Secondary untuk ikon
  netral.
- Ikon dipakai pada navigasi, aksi utama, status, dan empty state.
  Tidak perlu ikon di setiap baris tabel bila tidak menambah
  kejelasan.
- **Jangan memakai emoji sebagai ikon UI produk** — kecuali reaksi
  emote 😋 / 🥵 / 🫠 itu sendiri, yang memang adalah isi fitur
  KF-07, bukan ikon dekoratif. Ikon keranjang, cari, dan menu tetap
  memakai Lucide, bukan emoji.

Hindari mencampurkan berbagai gaya ikon dalam satu halaman.

---

## 9. Motion & Interaction

Ringkasan saja; spesifikasi lengkap ada di `Motion-Ayiriz.md`.
`Motion-Ayiriz.md` tetap berlaku penuh untuk tema ini.

| Interaction | Duration | Behavior |
|---|---|---|
| Hover | 150ms | Shadow SM → MD, naik 1px |
| Button Press | 100ms | Skala 0.97 |
| Modal | 200ms | Fade + scale |
| Dropdown | 150ms | Fade + translate |
| Card Hover (menu) | 200ms | Naik 4px + shadow MD |
| Reaksi emote | 150ms | Pop skala 1.15 lalu kembali |
| Marquee publik | 30 detik per putaran | Bergerak pelan, berhenti saat hover |

Penyesuaian khusus minimalism:

- Tidak ada animasi blur — tidak ada blur yang dianimasikan, karena
  tidak ada blur sama sekali.
- Hover kartu menu: `transform: translateY(-4px)` + shadow MD.
  Jangan mengubah warna background kartu saat hover lebih dari satu
  tingkat tint.
- Jangan menganimasikan bidang warna hero atau strip marquee selain
  gerakan marquee itu sendiri.

Rekomendasi easing:

```css
transition-timing-function:
  cubic-bezier(0.2, 0.8, 0.2, 1);
```

Motion harus membantu pengguna memahami perubahan state, bukan sekadar
dekorasi. Hormati preferensi `prefers-reduced-motion` (lihat
`Motion-Ayiriz.md` Bagian 15). Pada mode itu, marquee berhenti total
dan transisi diganti fade singkat atau tidak ada.

---

## 10. Accessibility Guidelines

1. Jangan menggunakan shadow atau warna sebagai satu-satunya penanda
   interaksi.
2. Pastikan teks memenuhi standar kontras WCAG AA (Bagian 2.6). Bila
   ragu, gelapkan teks atau pakai panel solid — jangan turunkan
   ukuran teks.
3. Gunakan outline fokus keyboard yang jelas: merah `#B71F30` 2px
   dengan offset 2px.
4. Bedakan disabled, hover, active, dan selected state dengan lebih
   dari satu isyarat visual.
5. Jangan hanya mengandalkan warna untuk menunjukkan status; selalu
   ada ikon dan label (misal "Diproses" tertulis, bukan hanya dot
   oranye).
6. Pastikan input memiliki label yang terlihat.
7. Hindari teks terlalu kecil pada background berwarna.
8. Target sentuh minimal 44×44px, terutama pada tampilan mobile —
   tombol "Tambah" dan stepper jumlah di kartu menu harus memenuhi
   ini.
9. Gunakan elemen semantik (`nav`, `main`, `table`, `button`,
   `label`) dan nama aksesibel untuk kontrol ikon saja.
10. Jangan menaruh teks penting di atas foto tanpa panel solid, di
    atas bidang warna kuat tanpa panel, atau di atas marquee yang
    bergerak.

Target kontras: teks normal minimal 4,5:1; teks besar minimal 3:1;
komponen UI dan indikator grafis penting minimal 3:1 terhadap warna
di sekitarnya.

---

## 11. CSS Design Tokens & Tailwind Config

Proyek ini memakai Tailwind v4, jadi token didefinisikan di `@theme`
di `resources/css/app.css`, bukan di `tailwind.config.js`. Jangan
membuat `tailwind.config.js` baru dan jangan mempertahankan dua sumber
token yang berbeda.

### 11.1 Font Loading

```css
/* Lobster: Google Fonts */
@import url("https://fonts.googleapis.com/css2?family=Lobster&display=swap");

/* Coolvetica + Coolvetica Condensed: self-host, file woff2 di public/fonts/ */
@font-face {
  font-family: "Coolvetica";
  src: url("/fonts/Coolvetica.woff2") format("woff2");
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}

@font-face {
  font-family: "Coolvetica Condensed";
  src: url("/fonts/CoolveticaCondensed.woff2") format("woff2");
  font-weight: 700;
  font-style: normal;
  font-display: swap;
}
```

Bila file `public/fonts/Coolvetica*.woff2` belum ada, fallback di
Bagian 4.1 yang dipakai — jangan membiarkan heading jatuh ke font
default browser tanpa condensed.

### 11.2 CSS Variables

```css
:root {
  /* Palet Indomie */
  --color-cream: #FFF8E7;
  --color-chili: #B71F30;
  --color-chili-hover: #9E1B29;
  --color-chili-active: #7F1520;
  --color-navy: #28397F;
  --color-leaf: #14502E;
  --color-sunny: #FEC345;
  --color-tang: #DE652E;

  /* Surface tint */
  --color-surface: #FFFFFF;
  --color-surface-cream: #FFFDF4;
  --color-tint-chili: #FDECEC;
  --color-tint-navy: #E6EAF6;
  --color-tint-leaf: #E2EFE6;
  --color-tint-sunny: #FFF3D6;
  --color-tint-tang: #FDEDE3;

  /* Text */
  --color-text-primary: #211D18;
  --color-text-secondary: #5A5245;
  --color-text-muted: #8A8171;
  --color-text-disabled: #B8AE9C;

  /* Border */
  --color-border: #EADFC2;
  --color-border-strong: #D9C89E;
  --color-divider: #EFE6CC;

  /* Status: teks + background (solid) */
  --color-success: #14502E;
  --color-success-bg: #E2EFE6;
  --color-warning: #7C4A03;
  --color-warning-bg: #FFF3D6;
  --color-error: #B71F30;
  --color-error-bg: #FDECEC;
  --color-info: #28397F;
  --color-info-bg: #E6EAF6;

  /* Shadows (datar) */
  --shadow-ayi-xs: 0 1px 2px rgba(40, 57, 127, 0.06);
  --shadow-ayi-sm: 0 1px 3px rgba(40, 57, 127, 0.08), 0 1px 2px rgba(40, 57, 127, 0.06);
  --shadow-ayi-md: 0 4px 12px rgba(40, 57, 127, 0.10);
  --shadow-ayi-lg: 0 12px 32px rgba(40, 57, 127, 0.14);

  /* Radius */
  --radius-xs: 6px;
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-full: 999px;

  /* Typography */
  --font-display: "Lobster", cursive;
  --font-heading: "Coolvetica Condensed", "Oswald", "Archivo Narrow", "Arial Narrow", sans-serif;
  --font-body: "Coolvetica", "Archivo", "Inter", ui-sans-serif, system-ui, sans-serif;
}
```

### 11.3 Tailwind v4 `@theme` (di `resources/css/app.css`)

Pindahkan nilai yang sama ke `@theme`. Jangan menulis nilai hex
mentah di dalam markup Blade bila sudah ada tokennya.

```css
@theme {
  --font-sans: "Coolvetica", "Archivo", "Inter", ui-sans-serif, system-ui, sans-serif;
  --font-display: "Lobster", cursive;
  --font-heading: "Coolvetica Condensed", "Oswald", "Archivo Narrow", "Arial Narrow", sans-serif;
  --font-body: "Coolvetica", "Archivo", "Inter", ui-sans-serif, system-ui, sans-serif;

  --color-cream: #FFF8E7;
  --color-chili: #B71F30;
  --color-chili-hover: #9E1B29;
  --color-chili-active: #7F1520;
  --color-navy: #28397F;
  --color-leaf: #14502E;
  --color-sunny: #FEC345;
  --color-tang: #DE652E;

  --color-ink: #211D18;
  --color-ink-secondary: #5A5245;
  --color-ink-muted: #8A8171;

  --color-success: #14502E;
  --color-success-bg: #E2EFE6;
  --color-warning: #7C4A03;
  --color-warning-bg: #FFF3D6;
  --color-error: #B71F30;
  --color-error-bg: #FDECEC;
  --color-info: #28397F;
  --color-info-bg: #E6EAF6;

  --radius-xs: 6px;
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;

  --shadow-ayi-xs: 0 1px 2px rgba(40, 57, 127, 0.06);
  --shadow-ayi-sm: 0 1px 3px rgba(40, 57, 127, 0.08), 0 1px 2px rgba(40, 57, 127, 0.06);
  --shadow-ayi-md: 0 4px 12px rgba(40, 57, 127, 0.10);
  --shadow-ayi-lg: 0 12px 32px rgba(40, 57, 127, 0.14);
}
```

Aturan pemakaian token: komponen merujuk ke token, bukan nilai hex
mentah di dalam markup. Bila perlu nilai baru, tambahkan token dulu.

### 11.4 Daftar Class Komponen (wajib dipakai saat implementasi)

Class komponen yang dipakai di proyek ini:

| Class (Ayiriz) | Definisi |
|---|---|
| `.ayi-card` | Putih solid + border `#EADFC2` + shadow SM, radius 16px |
| `.ayi-card-raised` | Putih solid + shadow MD, radius 16px |
| `.ayi-input` | Putih solid + border `#D9C89E`, focus border biru 2px |
| `.ayi-btn-primary` | Solid merah `#B71F30`, teks putih, pill, tanpa blur/gradien |
| `.ayi-btn-secondary` | Putih + border, teks biru |
| `.shadow-ayi-xs` / `sm` / `md` / `lg` | Lihat Bagian 3.1 |
| `.ayi-accent-bar` | Garis 3px solid merah `#B71F30`, atau kuning `#FEC345` untuk aksen hero |
| `.interactive-card` | Hover: naik 4px + shadow MD, durasi 200ms, tanpa blur |

Class `.page-content`, `.scroll-progress`, `.loading-spinner`,
`.skeleton`, dan aturan `prefers-reduced-motion` di `app.css` tetap
dipakai, definisinya disesuaikan ke Bagian 9 dan `Motion-Ayiriz.md`.

---

## 12. Responsive: Mobile-First untuk Pelanggan, Desktop untuk Kasir

Ayiriz dipakai pelanggan dari HP (pesan sambil jalan ke kantin) dan
dipakai outlet/pusat dari laptop (kasir dan laporan). Karena itu
pendekatannya **mobile-first untuk katalog dan pemesanan**, dan
sidebar desktop untuk aplikasi.

| Lebar | Perilaku |
|---|---|
| Mobile (di bawah 768px) | 1 kolom; katalog kartu menu 2 kolom kecil atau 1 kolom; bottom tab bar pelanggan; tabel menjadi kartu; filter masuk ke panel bawah |
| Tablet (768–1279px) | Grid katalog 2–3 kolom; sidebar menjadi rail ikon atau drawer |
| Desktop (1280px ke atas) | Sidebar kiri penuh + grid konten fluid; grid katalog 3–4 kolom |

Aturan yang tetap berlaku di semua lebar:

- Tabel di mobile diubah menjadi kartu (judul, status, 1–2 field
  kunci, menu aksi), bukan tabel yang mengecil sampai tidak terbaca.
- Target sentuh minimal 44×44px di tablet dan mobile — terutama
  tombol "Tambah" dan stepper jumlah.
- Konten tidak boleh menempel ke tepi container; padding halaman
  mobile minimal 16px.
- Hero publik di mobile: judul Lobster 40px, satu kolom, foto makanan
  di bawah judul — bukan tiga foto sejajar yang mengecil.
- Periksa hasil di tiga lebar (≈390, 768, 1280) sebelum sebuah layar
  dinyatakan selesai.

---

## 13. Recommended Design Direction

Kombinasi final yang direkomendasikan:

| Category | Selection |
|---|---|
| Main Style | Minimalism (kanvas krem, permukaan datar) |
| Karakter | Hangat, lapar-mata, tegas, playful secukupnya |
| Tema | Light saja |
| Primer (background) | `#FFF8E7` (krem) |
| Tindakan utama | `#B71F30` (merah cabai) |
| Sekunder | `#28397F` (biru gelap) |
| Sukses | `#14502E` (hijau gelap) |
| Highlight | `#FEC345` (kuning, teks gelap di atasnya) |
| Aksen hangat | `#DE652E` (oranye, teks gelap di atasnya untuk teks kecil) |
| Gradien | Tidak dipakai (kecuali tipis sekali di hero publik bila perlu — lebih baik tidak) |
| Background | Krem solid `#FFF8E7` |
| Display Font | Lobster (brand + hero saja) |
| Heading Font | Coolvetica Condensed (fallback Oswald / Archivo Narrow) |
| Body Font | Coolvetica (fallback Archivo / Inter) |
| Icon Style | Lucide Outline |
| Shadow Style | Satu bayangan lembut ke bawah |
| Border Radius | 12px input/kartu kecil, 16px kartu/modal, pill tombol |
| Density | Seimbang |
| Navigasi | Top bar sticky (publik) + sidebar kiri (aplikasi) |
| Motion | Subtle & Smooth (lihat `Motion-Ayiriz.md`) |
| Brand | Wordmark "Ayiriz" Lobster merah di atas krem |
| Referensi | Palet Indomie; pola Hard Lines (kanvas lega + marquee); pola Plately (bidang warna + kartu blok warna) |

Final Design Principle:

> Antarmuka Ayiriz yang lega dan lapar-mata: kanvas krem yang luas,
> satu warna tindakan merah yang jelas, judul besar yang berani, dan
> foto makanan sebagai bintangnya — tanpa kaca, tanpa bayangan ganda,
> dan tanpa satu halaman pun yang dipadatkan sampai sesak.

Desain harus tetap mengutamakan keterbacaan, aksesibilitas,
konsistensi, dan kemudahan penggunaan. Warna Indomie dan huruf besar
merupakan pendukung pengalaman pengguna, bukan pengganti hierarki
visual dan usability.

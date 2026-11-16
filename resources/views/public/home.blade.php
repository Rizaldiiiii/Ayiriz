<x-layouts.public title="Katalog Kantin & Menu">
    @php
        $cartItemMap = [];
        foreach (($cart['items'] ?? []) as $row) {
            $cartItemMap[$row['menu']->id] = $row['jumlah'];
        }
    @endphp

    {{-- Bidang Warna Penuh Hero Publik (Pola Plately: latar oranye #DE652E + kartu krem #FFF8E7 di tengah) --}}
    <section class="bg-tang px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="mx-auto max-w-6xl rounded-[20px] border border-border-ayi bg-cream p-6 shadow-ayi-md sm:p-10 lg:p-12">
            <div class="grid gap-8 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-7">
                    <div class="inline-flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-sunny px-3.5 py-1 font-heading text-xs font-bold uppercase tracking-wider text-ink">
                            Kantin Mitra Depan Gerbang UNG
                        </span>
                        <span class="ayi-chip-sample">Data Contoh</span>
                    </div>

                    <h1 class="mt-4 font-display-brand text-[40px] leading-[1.05] text-chili sm:text-[56px]">
                        Ayiriz.
                    </h1>

                    <p class="mt-2 font-heading text-[24px] font-bold leading-tight text-ink sm:text-[30px]">
                        Pesan Online dari HP, Ambil & Bayar Langsung di Kasir Kantin.
                    </p>

                    <p class="mt-3 max-w-xl text-base leading-relaxed text-ink-secondary">
                        Lihat menu dan sisa porsi hari ini dari kantin-kantin mitra Ayiriz di depan gerbang kampus Universitas Negeri Gorontalo tanpa antre panjang. Kantin memasak pesanan saat kamu tiba dan membayar di kasir.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <a href="#katalog-menu" class="ayi-btn-primary">
                            <span>Jelajahi Menu Hari Ini</span>
                        </a>
                        <a href="#daftar-kantin" class="ayi-btn-secondary">
                            <span>Lihat {{ $outlets->count() }} Kantin Aktif</span>
                        </a>
                    </div>
                </div>

                {{-- Kartu Highlight Visual di Kanan Hero (1 kolom di mobile, kartu berlapis solid di desktop) --}}
                <div class="lg:col-span-5">
                    <div class="rounded-[16px] border border-border-ayi bg-surface p-5 shadow-ayi-sm">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="flex flex-col items-center justify-center rounded-[12px] bg-leaf p-3 text-white">
                                <span class="font-heading text-2xl font-bold tabular-nums">{{ $outlets->count() }}</span>
                                <span class="mt-0.5 text-center text-[11px] font-medium">Kantin Aktif</span>
                            </div>
                            <div class="flex flex-col items-center justify-center rounded-[12px] bg-sunny p-3 text-ink">
                                <span class="font-heading text-2xl font-bold tabular-nums">{{ $menus->total() }}</span>
                                <span class="mt-0.5 text-center text-[11px] font-semibold">Menu Tersedia</span>
                            </div>
                            <div class="flex flex-col items-center justify-center rounded-[12px] bg-tint-chili p-3 text-chili">
                                <span class="font-heading text-lg font-bold">Kasir</span>
                                <span class="mt-0.5 text-center text-[11px] font-semibold text-ink">Bayar Saat Ambil</span>
                            </div>
                        </div>

                        <div class="mt-4 rounded-[12px] border border-divider bg-surface-cream p-4">
                            <p class="font-heading text-sm font-bold text-navy">Cara Pesan di Ayiriz (3 Langkah):</p>
                            <ol class="mt-2 space-y-1.5 text-xs text-ink-secondary">
                                <li class="flex items-start gap-2">
                                    <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-chili font-heading text-[11px] font-bold text-white">1</span>
                                    <span>Pilih <strong>satu kantin mitra</strong> dan masukkan menu ke keranjang.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-chili font-heading text-[11px] font-bold text-white">2</span>
                                    <span>Checkout untuk mengunci harga & mengamankan stok porsi (status <strong>Diproses</strong>).</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-chili font-heading text-[11px] font-bold text-white">3</span>
                                    <span>Datang ke kantin, bayar di kasir, pesanan dimasak hangat & status <strong>Selesai</strong>.</span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Strip Marquee Publik (Pola Hard Lines — Motion-Ayiriz.md §13.4) --}}
    <p class="sr-only">
        Ayiriz — Kantin mitra di depan gerbang kampus UNG. Pesan online, bayar di kasir saat ambil pesanan.
    </p>
    <div class="marquee" aria-hidden="true">
        <div class="marquee-track font-heading text-sm font-bold tracking-wider">
            <span>AYIRIZ • DEPAN GERBANG UNG • PESAN ONLINE • BAYAR DI KASIR • SATU PESANAN SATU KANTIN • HARGA ANAK KULIAHAN •&nbsp;</span>
            <span>AYIRIZ • DEPAN GERBANG UNG • PESAN ONLINE • BAYAR DI KASIR • SATU PESANAN SATU KANTIN • HARGA ANAK KULIAHAN •&nbsp;</span>
            <span>AYIRIZ • DEPAN GERBANG UNG • PESAN ONLINE • BAYAR DI KASIR • SATU PESANAN SATU KANTIN • HARGA ANAK KULIAHAN •&nbsp;</span>
            <span>AYIRIZ • DEPAN GERBANG UNG • PESAN ONLINE • BAYAR DI KASIR • SATU PESANAN SATU KANTIN • HARGA ANAK KULIAHAN •&nbsp;</span>
            <span>AYIRIZ • DEPAN GERBANG UNG • PESAN ONLINE • BAYAR DI KASIR • SATU PESANAN SATU KANTIN • HARGA ANAK KULIAHAN •&nbsp;</span>
        </div>
    </div>

    {{-- Daftar Outlet / Kantin Mitra Aktif --}}
    <section id="daftar-kantin" class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 lg:px-8">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end" data-reveal="up">
            <div>
                <div class="ayi-accent-bar mb-2 w-12"></div>
                <h2 class="font-heading text-[26px] font-bold text-ink">
                    KANTIN MITRA AKTIF DI DEPAN GERBANG UNG
                </h2>
                <p class="text-sm text-ink-secondary">
                    Pilih kantin untuk melihat profil dan seluruh menu yang tersedia hari ini.
                </p>
            </div>
        </div>

        <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $badgeColors = ['bg-chili text-white', 'bg-navy text-white', 'bg-leaf text-white'];
            @endphp
            @forelse ($outlets as $idx => $outlet)
                <article
                    class="ayi-card interactive-card flex flex-col justify-between p-5"
                    data-reveal="up"
                    style="--reveal-delay: {{ ($idx % 3) * 90 }}ms"
                >
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3.5">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[12px] font-heading text-lg font-bold {{ $badgeColors[$idx % 3] }}">
                                    {{ strtoupper(substr($outlet->nama, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <h3 class="font-heading text-[21px] font-bold text-ink">
                                            {{ $outlet->nama }}
                                        </h3>
                                        @if ($outlet->is_sample)
                                            <span class="ayi-chip-sample">Data Contoh</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-ink-secondary">Pemilik: {{ $outlet->pemilik }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 space-y-1.5 rounded-[12px] bg-surface-cream p-3 text-xs text-ink-secondary">
                            <p class="flex items-center gap-2">
                                <span class="font-bold text-ink">Lokasi:</span>
                                <span>{{ $outlet->alamat }}</span>
                            </p>
                            <p class="flex items-center gap-2">
                                <span class="font-bold text-ink">Jam Buka:</span>
                                <span>{{ $outlet->jam_buka }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between border-t border-divider pt-4">
                        <span class="ayi-badge-selesai">
                            <span class="h-2 w-2 rounded-full bg-leaf" aria-hidden="true"></span>
                            <span>Aktif · {{ $outlet->menus_count }} Menu</span>
                        </span>

                        <a
                            href="{{ route('catalog.outlet', $outlet->slug) }}"
                            class="ayi-btn-secondary min-h-[40px] px-4 py-1.5 text-sm"
                        >
                            Lihat Menu →
                        </a>
                    </div>
                </article>
            @empty
                <div class="ayi-card col-span-full p-8 text-center">
                    <p class="font-heading text-lg font-bold text-ink">Belum ada kantin aktif yang terverifikasi.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Pencarian & Filter Katalog Menu (KF-03) --}}
    <section id="katalog-menu" class="mx-auto max-w-7xl px-4 pt-12 sm:px-6 lg:px-8">
        <div class="ayi-card bg-surface-cream p-5 sm:p-6" data-reveal="up">
            <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
                <div>
                    <h2 class="font-heading text-[26px] font-bold text-ink">
                        KATALOG MENU HARI INI
                    </h2>
                    <p class="text-sm text-ink-secondary">
                        Cari berdasarkan nama makanan/minuman, saring per kategori, kantin, atau rentang harga mahasiswa.
                    </p>
                </div>
                @if ($filters['q'] || $filters['category_id'] || $filters['outlet_id'] || $filters['min_harga'] || $filters['max_harga'])
                    <a href="{{ route('catalog.index') }}#katalog-menu" class="text-sm font-bold text-chili underline">
                        Reset Filter
                    </a>
                @endif
            </div>

            <form action="{{ route('catalog.index') }}#katalog-menu" method="GET" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <label for="filter-q" class="block font-heading text-xs font-bold uppercase tracking-wider text-ink">
                        Nama Menu / Kata Kunci
                    </label>
                    <input
                        id="filter-q"
                        type="search"
                        name="q"
                        value="{{ $filters['q'] }}"
                        placeholder="Misal: geprek, kopi, pisang ijo..."
                        class="ayi-input mt-1"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label for="filter-category" class="block font-heading text-xs font-bold uppercase tracking-wider text-ink">
                        Kategori
                    </label>
                    <select id="filter-category" name="category_id" class="ayi-input mt-1">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected((string) $filters['category_id'] === (string) $cat->id)>
                                {{ $cat->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label for="filter-outlet" class="block font-heading text-xs font-bold uppercase tracking-wider text-ink">
                        Kantin Mitra
                    </label>
                    <select id="filter-outlet" name="outlet_id" class="ayi-input mt-1">
                        <option value="">Semua Kantin</option>
                        @foreach ($outlets as $out)
                            <option value="{{ $out->id }}" @selected((string) $filters['outlet_id'] === (string) $out->id)>
                                {{ $out->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label for="filter-min-harga" class="block font-heading text-xs font-bold uppercase tracking-wider text-ink">
                        Harga Min (Rp)
                    </label>
                    <input
                        id="filter-min-harga"
                        type="number"
                        name="min_harga"
                        min="0"
                        step="1000"
                        value="{{ $filters['min_harga'] }}"
                        placeholder="5000"
                        class="ayi-input mt-1 tabular-nums"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label for="filter-max-harga" class="block font-heading text-xs font-bold uppercase tracking-wider text-ink">
                        Harga Maks (Rp)
                    </label>
                    <div class="mt-1 flex gap-2">
                        <input
                            id="filter-max-harga"
                            type="number"
                            name="max_harga"
                            min="0"
                            step="1000"
                            value="{{ $filters['max_harga'] }}"
                            placeholder="25000"
                            class="ayi-input tabular-nums"
                        >
                        <button type="submit" class="ayi-btn-primary shrink-0 px-4">
                            Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Grid Kartu Menu (3-4 kolom di desktop, 2 di tablet, 1-2 di mobile) --}}
        @if ($menus->count() > 0)
            <div class="mt-6 grid gap-4 sm:grid-cols-2 md:gap-6 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($menus as $idx => $menu)
                    <x-menu-card
                        :menu="$menu"
                        :index="$idx"
                        :cartQty="$cartItemMap[$menu->id] ?? 0"
                    />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $menus->links() }}
            </div>
        @else
            <div class="ayi-card mt-6 p-10 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-tint-sunny text-warning">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="8" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3" />
                    </svg>
                </div>
                <h3 class="mt-4 font-heading text-xl font-bold text-ink">
                    Tidak ada menu yang cocok dengan pencarian Anda
                </h3>
                <p class="mt-1 text-sm text-ink-secondary">
                    Coba kata kunci lain atau tampilkan kembali seluruh menu dari semua kantin.
                </p>
                <a href="{{ route('catalog.index') }}#katalog-menu" class="ayi-btn-primary mt-5">
                    Tampilkan Semua Menu
                </a>
            </div>
        @endif
    </section>
</x-layouts.public>

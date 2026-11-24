<x-layouts.public :title="$outlet->nama">
    @php
        $cartItemMap = [];
        foreach (($cart['items'] ?? []) as $row) {
            $cartItemMap[$row['menu']->id] = $row['jumlah'];
        }
    @endphp

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav aria-label="Breadcrumb" class="mb-4 text-xs font-semibold text-ink-secondary">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('catalog.index') }}" class="text-navy underline hover:text-chili">Beranda Katalog</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-ink">{{ $outlet->nama }}</li>
            </ol>
        </nav>

        {{-- Header Profil Outlet --}}
        <div class="ayi-card p-6 sm:p-8" data-reveal="up">
            <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
                <div class="flex items-start gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-[16px] bg-chili font-heading text-2xl font-bold text-white shadow-ayi-xs">
                        {{ strtoupper(substr($outlet->nama, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="font-heading text-[32px] font-bold leading-tight text-ink">
                                {{ $outlet->nama }}
                            </h1>
                            <x-status-badge :status="$outlet->status" type="outlet" />
                            @if ($outlet->is_sample)
                                <span class="ayi-chip-sample">Data Contoh</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-ink-secondary">
                            Dikelola oleh <strong>{{ $outlet->pemilik }}</strong> · Lokasi: {{ $outlet->alamat }}
                        </p>
                        <p class="mt-1 text-xs font-semibold text-navy">
                            Jam Buka: {{ $outlet->jam_buka }} · Kontak Kasir: {{ $outlet->no_hp }}
                        </p>
                    </div>
                </div>

                @if (($cart['total_item'] ?? 0) > 0)
                    <a href="{{ route('cart.index') }}" class="ayi-btn-primary">
                        <span>Lihat Keranjang ({{ $cart['total_item'] }} porsi · Rp {{ number_format($cart['total'], 0, ',', '.') }})</span>
                    </a>
                @endif
            </div>

            {{-- Filter Menu di Outlet Ini --}}
            <form action="{{ route('catalog.outlet', $outlet->slug) }}" method="GET" class="mt-6 grid gap-3 border-t border-divider pt-5 sm:grid-cols-12">
                <div class="sm:col-span-6">
                    <label for="outlet-q" class="block font-heading text-xs font-bold uppercase tracking-wider text-ink">
                        Cari Menu di {{ $outlet->nama }}
                    </label>
                    <input
                        id="outlet-q"
                        type="search"
                        name="q"
                        value="{{ $filters['q'] }}"
                        placeholder="Ketik nama makanan atau minuman..."
                        class="ayi-input mt-1"
                    >
                </div>

                <div class="sm:col-span-4">
                    <label for="outlet-category" class="block font-heading text-xs font-bold uppercase tracking-wider text-ink">
                        Kategori Menu
                    </label>
                    <select id="outlet-category" name="category_id" class="ayi-input mt-1">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected((string) $filters['category_id'] === (string) $cat->id)>
                                {{ $cat->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2 sm:col-span-2">
                    <button type="submit" class="ayi-btn-primary w-full">
                        Saring
                    </button>
                </div>
            </form>
        </div>

        {{-- Daftar Menu Outlet --}}
        <div class="mt-8">
            <h2 class="font-heading text-[24px] font-bold text-ink">
                DAFTAR MENU ({{ $menus->count() }})
            </h2>

            @if ($menus->count() > 0)
                <div class="mt-4 grid gap-4 sm:grid-cols-2 md:gap-6 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($menus as $idx => $menu)
                        <x-menu-card
                            :menu="$menu"
                            :index="$idx"
                            :cartQty="$cartItemMap[$menu->id] ?? 0"
                            :showOutletName="false"
                        />
                    @endforeach
                </div>
            @else
                <div class="ayi-card mt-4 p-8 text-center">
                    <p class="font-heading text-lg font-bold text-ink">Menu tidak ditemukan di kantin ini.</p>
                    <a href="{{ route('catalog.outlet', $outlet->slug) }}" class="ayi-btn-secondary mt-4">
                        Tampilkan Semua Menu Kantin
                    </a>
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>

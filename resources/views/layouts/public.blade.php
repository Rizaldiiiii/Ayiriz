<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — Ayiriz' : 'Ayiriz — Kantin Mitra Depan Gerbang UNG' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream text-ink antialiased flex flex-col pb-20 md:pb-0">
    {{-- Scroll Progress Indicator (Publik) --}}
    <div class="scroll-progress" aria-hidden="true"></div>

    @php
        $cartDetails = app(\App\Services\CartService::class)->getCartDetails();
        $cartCount = $cartDetails['total_item'] ?? 0;
    @endphp

    {{-- Top Bar Sticky Krem Solid (Minimalism §5.5: tanpa transparansi/blur) --}}
    <header
        x-data="{ scrolled: false }"
        @scroll.window="scrolled = window.scrollY > 40"
        :class="scrolled ? 'shadow-ayi-sm' : 'shadow-none'"
        class="sticky top-0 z-40 h-16 border-b border-border-ayi bg-cream transition-shadow duration-200"
    >
        <div class="mx-auto flex h-full max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            {{-- Wordmark Ayiriz Lobster 28px Merah --}}
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('catalog.index') }}"
                    class="font-display-brand text-[28px] leading-none text-chili focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-chili"
                >
                    Ayiriz
                </a>
                <span class="ayi-chip-sample hidden sm:inline-flex" title="Prototipe Tugas Kuliah — Data Seeder Fiktif">
                    Data Contoh
                </span>
            </div>

            {{-- Navigasi Tengah / Pencarian Cepat --}}
            <form
                action="{{ route('catalog.index') }}"
                method="GET"
                class="hidden max-w-md flex-1 md:flex"
                role="search"
            >
                <label for="topbar-search" class="sr-only">Cari menu di semua kantin Ayiriz</label>
                <div class="relative w-full">
                    <input
                        id="topbar-search"
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari ayam geprek, es kopi, pisang ijo..."
                        class="ayi-input min-h-[40px] py-1.5 pl-9 pr-4 text-sm"
                    >
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="8" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3" />
                    </svg>
                </div>
            </form>

            {{-- Navigasi Kanan --}}
            <nav class="flex items-center gap-2 sm:gap-3" aria-label="Navigasi utama">
                <a
                    href="{{ route('catalog.index') }}"
                    class="hidden rounded-full px-3.5 py-2 font-heading text-sm font-bold transition sm:inline-flex {{ request()->routeIs('catalog.index') || request()->routeIs('catalog.outlet') || request()->routeIs('catalog.menu') ? 'bg-tint-chili text-chili' : 'text-ink-secondary hover:text-chili' }}"
                >
                    Katalog
                </a>

                <a
                    href="{{ route('catalog.reaksi.lookup') }}"
                    class="hidden rounded-full px-3.5 py-2 font-heading text-sm font-bold transition sm:inline-flex {{ request()->routeIs('catalog.reaksi*') ? 'bg-tint-chili text-chili' : 'text-ink-secondary hover:text-chili' }}"
                >
                    Beri Reaksi
                </a>

                {{-- Keranjang Tamu (Tanpa Login — KF-04 Model B) --}}
                <a
                    href="{{ route('cart.index') }}"
                    class="relative inline-flex min-h-[44px] items-center gap-2 rounded-full border border-border-ayi bg-surface px-3.5 py-2 font-heading text-sm font-bold text-ink shadow-ayi-xs transition hover:border-chili"
                    aria-label="Keranjang pesanan ({{ $cartCount }} item)"
                >
                    <svg class="h-5 w-5 text-chili" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="8" cy="21" r="1" />
                        <circle cx="19" cy="21" r="1" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                    </svg>
                    <span class="hidden sm:inline">Keranjang</span>
                    <span class="inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-chili px-1.5 text-xs font-bold text-white tabular-nums">
                        {{ $cartCount }}
                    </span>
                </a>

                @auth
                    @if (auth()->user()->isOutlet())
                        <a href="{{ route('outlet.dashboard') }}" class="ayi-btn-primary min-h-[40px] px-4 py-2 text-sm">
                            Panel Kasir Outlet
                        </a>
                    @elseif (auth()->user()->isPusat())
                        <a href="{{ route('pusat.dashboard') }}" class="ayi-btn-primary min-h-[40px] px-4 py-2 text-sm">
                            Dashboard Pusat
                        </a>
                    @endif

                    {{-- Dropdown Profil (Alpine.js sesuai Motion-Ayiriz.md §6.1) --}}
                    <div
                        x-data="{ open: false }"
                        @click.outside="open = false"
                        @keydown.escape.window="open = false"
                        class="relative"
                    >
                        <button
                            type="button"
                            @click="open = !open"
                            :aria-expanded="open"
                            aria-controls="user-dropdown-menu"
                            class="inline-flex min-h-[44px] items-center gap-2 rounded-[12px] border border-border-ayi bg-surface px-3 py-2 text-sm font-semibold text-ink shadow-ayi-xs hover:bg-surface-cream focus-visible:outline focus-visible:outline-2 focus-visible:outline-chili"
                        >
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-tint-chili font-heading text-xs font-bold text-chili">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden max-w-[110px] truncate lg:inline">{{ auth()->user()->name }}</span>
                            <svg
                                class="h-4 w-4 text-ink-secondary transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div
                            id="user-dropdown-menu"
                            x-cloak
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                            class="absolute right-0 top-full z-50 mt-2 w-60 rounded-[16px] border border-border-ayi bg-surface p-2 shadow-ayi-md"
                        >
                            <div class="border-b border-divider px-3 py-2.5">
                                <p class="truncate font-heading text-sm font-bold text-ink">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-ink-secondary">{{ auth()->user()->email }}</p>
                                <span class="mt-1.5 inline-block rounded-full bg-tint-navy px-2.5 py-0.5 font-heading text-[11px] font-bold text-navy">
                                    {{ auth()->user()->role->label() }}
                                </span>
                            </div>

                            <form action="{{ route('logout') }}" method="POST" class="mt-1">
                                @csrf
                                <button
                                    type="submit"
                                    class="flex w-full items-center gap-2 rounded-[10px] px-3 py-2.5 text-left text-sm font-semibold text-chili hover:bg-tint-chili"
                                >
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5m5 5H9" />
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="ayi-btn-secondary min-h-[42px] px-4 py-2 text-sm">
                        Login Mitra / Pusat
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Toast Notifikasi (Minimalism §5.8) --}}
    @if (session('toast_success') || session('toast_info') || $errors->any())
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 6000)"
            class="fixed right-4 top-20 z-50 w-full max-w-md px-4 sm:px-0"
            role="status"
            aria-live="polite"
        >
            @if (session('toast_success'))
                <div class="flex items-start justify-between gap-3 rounded-[12px] border border-border-ayi border-l-4 border-l-leaf bg-surface p-4 shadow-ayi-lg">
                    <div class="flex items-start gap-2.5">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-leaf" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
                        </svg>
                        <div>
                            <p class="font-heading text-xs font-bold uppercase tracking-wider text-leaf">Berhasil</p>
                            <p class="mt-0.5 text-sm text-ink">{{ session('toast_success') }}</p>
                        </div>
                    </div>
                    <button type="button" @click="show = false" class="rounded p-1 text-ink-muted hover:text-ink" aria-label="Tutup notifikasi">✕</button>
                </div>
            @elseif (session('toast_info'))
                <div class="flex items-start justify-between gap-3 rounded-[12px] border border-border-ayi border-l-4 border-l-navy bg-surface p-4 shadow-ayi-lg">
                    <div class="flex items-start gap-2.5">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-navy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01" />
                        </svg>
                        <div>
                            <p class="font-heading text-xs font-bold uppercase tracking-wider text-navy">Informasi</p>
                            <p class="mt-0.5 text-sm text-ink">{{ session('toast_info') }}</p>
                        </div>
                    </div>
                    <button type="button" @click="show = false" class="rounded p-1 text-ink-muted hover:text-ink" aria-label="Tutup notifikasi">✕</button>
                </div>
            @elseif ($errors->any())
                <div class="flex items-start justify-between gap-3 rounded-[12px] border border-border-ayi border-l-4 border-l-chili bg-surface p-4 shadow-ayi-lg">
                    <div class="flex items-start gap-2.5">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-chili" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
                        </svg>
                        <div>
                            <p class="font-heading text-xs font-bold uppercase tracking-wider text-chili">Perhatian</p>
                            <p class="mt-0.5 text-sm text-ink">{{ $errors->first() }}</p>
                        </div>
                    </div>
                    <button type="button" @click="show = false" class="rounded p-1 text-ink-muted hover:text-ink" aria-label="Tutup notifikasi">✕</button>
                </div>
            @endif
        </div>
    @endif

    {{-- Modal Konfirmasi Pindah Outlet Keranjang (KF-04: 1 pesanan = 1 outlet) --}}
    @if (session('cart_conflict'))
        @php $conflict = session('cart_conflict'); @endphp
        <div
            x-data="{ open: true }"
            x-show="open"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-conflict-title"
        >
            <div class="absolute inset-0 bg-[#211D18]/45" x-show="open" x-transition.opacity.duration.200ms></div>

            <div
                class="relative w-full max-w-md rounded-[16px] border border-border-ayi bg-surface p-6 shadow-ayi-lg"
                x-show="open"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            >
                <div class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-tint-tang text-tang">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                    </svg>
                </div>

                <h2 id="modal-conflict-title" class="mt-4 font-heading text-[22px] font-bold text-ink">
                    Ganti Kantin di Keranjang?
                </h2>

                <p class="mt-2 text-sm leading-relaxed text-ink-secondary">
                    Satu pesanan di Ayiriz hanya berlaku untuk <strong>satu kantin</strong> karena pembayaran tunai dan pengambilan dilakukan di kasir kantin tersebut.
                </p>
                <p class="mt-2 rounded-[12px] bg-surface-cream p-3 text-sm text-ink">
                    Keranjang Anda saat ini berisi menu dari <strong>{{ $conflict['current_outlet_nama'] }}</strong>. Kosongkan keranjang lama dan ganti dengan <strong>{{ $conflict['menu_nama'] }}</strong> dari <strong>{{ $conflict['new_outlet_nama'] }}</strong>?
                </p>

                <div class="mt-6 flex flex-wrap items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="open = false"
                        class="ayi-btn-secondary min-h-[44px] px-4 py-2 text-sm"
                    >
                        Batal
                    </button>

                    <form action="{{ route('cart.items.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="menu_id" value="{{ $conflict['menu_id'] }}">
                        <input type="hidden" name="jumlah" value="{{ $conflict['jumlah'] }}">
                        <input type="hidden" name="replace_outlet" value="1">
                        <button type="submit" class="ayi-btn-primary min-h-[44px] px-5 py-2 text-sm">
                            Ya, Kosongkan & Ganti Kantin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Konten Utama --}}
    <main class="page-content flex-1">
        {{ $slot }}
    </main>

    {{-- Footer Publik --}}
    <footer class="mt-16 border-t border-border-ayi bg-surface py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-center">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="font-display-brand text-3xl text-chili">Ayiriz</span>
                        <span class="ayi-chip-sample">Data Contoh</span>
                    </div>
                    <p class="mt-1 font-heading text-sm font-bold text-ink">
                        Sistem Informasi Pengelolaan UMKM Lokal Ayiriz
                    </p>
                    <p class="mt-1 max-w-xl text-xs text-ink-secondary">
                        Kantin-kantin mitra di depan gerbang kampus Universitas Negeri Gorontalo (UNG). Pesan online tanpa daftar/login, ambil dan bayar tunai langsung di kasir kantin. Prototipe tugas kuliah — seluruh kantin, menu, dan transaksi adalah data contoh fiktif.
                    </p>
                </div>

                <div class="flex flex-wrap gap-4 text-sm font-semibold text-navy">
                    <a href="{{ route('catalog.index') }}" class="underline decoration-navy/40 underline-offset-4 hover:text-chili">Katalog Kantin</a>
                    <a href="{{ route('cart.index') }}" class="underline decoration-navy/40 underline-offset-4 hover:text-chili">Keranjang Pesanan</a>
                    @guest
                        <a href="{{ route('login') }}" class="underline decoration-navy/40 underline-offset-4 hover:text-chili">Login Mitra / Pusat</a>
                    @endguest
                </div>
            </div>
        </div>
    </footer>

    {{-- Mobile Bottom Tab Bar (Tamu / Publik, maksimal 4 item sesuai Minimalism §5.5) --}}
    <nav
        class="fixed bottom-0 left-0 right-0 z-40 flex h-16 items-center justify-around border-t border-border-ayi bg-surface px-2 md:hidden"
        aria-label="Navigasi bawah mobile"
    >
        <a
            href="{{ route('catalog.index') }}"
            class="flex min-h-[44px] min-w-[64px] flex-col items-center justify-center rounded-lg px-2 py-1 text-xs font-bold {{ request()->routeIs('catalog.index') ? 'text-chili' : 'text-ink-secondary' }}"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
            </svg>
            <span class="mt-0.5">Beranda</span>
        </a>

        <a
            href="{{ route('catalog.index') }}#katalog-menu"
            class="flex min-h-[44px] min-w-[64px] flex-col items-center justify-center rounded-lg px-2 py-1 text-xs font-bold text-ink-secondary hover:text-chili"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <rect x="3" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="14" width="7" height="7" rx="1" />
                <rect x="3" y="14" width="7" height="7" rx="1" />
            </svg>
            <span class="mt-0.5">Katalog</span>
        </a>

        <a
            href="{{ route('cart.index') }}"
            class="relative flex min-h-[44px] min-w-[64px] flex-col items-center justify-center rounded-lg px-2 py-1 text-xs font-bold {{ request()->routeIs('cart.*') ? 'text-chili' : 'text-ink-secondary' }}"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="8" cy="21" r="1" />
                <circle cx="19" cy="21" r="1" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
            </svg>
            <span class="mt-0.5">Keranjang ({{ $cartCount }})</span>
        </a>

        @auth
            <a
                href="{{ auth()->user()->isPusat() ? route('pusat.dashboard') : route('outlet.dashboard') }}"
                class="flex min-h-[44px] min-w-[64px] flex-col items-center justify-center rounded-lg px-2 py-1 text-xs font-bold text-chili"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M3 6h18M3 18h18" />
                </svg>
                <span class="mt-0.5">Panel</span>
            </a>
        @else
            <a
                href="{{ route('login') }}"
                class="flex min-h-[44px] min-w-[64px] flex-col items-center justify-center rounded-lg px-2 py-1 text-xs font-bold text-ink-secondary"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3" />
                </svg>
                <span class="mt-0.5">Mitra</span>
            </a>
        @endauth
    </nav>
</body>
</html>

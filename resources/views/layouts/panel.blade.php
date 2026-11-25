<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — Panel Ayiriz' : 'Panel Pengelolaan — Ayiriz' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream text-ink antialiased" x-data="{ drawerOpen: false }">
    @php
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $isPusat = $user->isPusat();
        $isOutlet = $user->isOutlet();
    @endphp

    <div class="flex min-h-screen">
        {{-- Overlay Drawer Mobile (tanpa blur, solid gelap hangat transparan) --}}
        <div
            x-cloak
            x-show="drawerOpen"
            @click="drawerOpen = false"
            x-transition.opacity.duration.200ms
            class="fixed inset-0 z-40 bg-[#211D18]/45 lg:hidden"
        ></div>

        {{-- Sidebar Kiri Putih Solid (Minimalism §5.5) --}}
        <aside
            :class="drawerOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-border-ayi bg-surface transition-transform duration-200 lg:static lg:z-auto"
            aria-label="Sidebar navigasi aplikasi"
        >
            {{-- Brand Header --}}
            <div class="flex h-16 items-center justify-between border-b border-divider px-6">
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('catalog.index') }}" class="font-display-brand text-[28px] leading-none text-chili">
                        Ayiriz
                    </a>
                    <span class="ayi-chip-sample">Data Contoh</span>
                </div>
                <button
                    type="button"
                    @click="drawerOpen = false"
                    class="rounded-lg p-2 text-ink-secondary hover:bg-tint-chili hover:text-chili lg:hidden"
                    aria-label="Tutup menu navigasi"
                >
                    ✕
                </button>
            </div>

            {{-- Profil & Badge Role --}}
            <div class="border-b border-divider bg-surface-cream px-6 py-4">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-tint-navy px-3 py-0.5 font-heading text-xs font-bold text-navy">
                    {{ $user->role->label() }}
                </span>
                <p class="mt-2 truncate font-heading text-base font-bold text-ink">
                    {{ $isOutlet && $user->outlet ? $user->outlet->nama : $user->name }}
                </p>
                <p class="truncate text-xs text-ink-secondary">{{ $user->email }}</p>
                @if ($isOutlet && $user->outlet)
                    <div class="mt-2">
                        <x-status-badge :status="$user->outlet->status" type="outlet" />
                    </div>
                @endif
            </div>

            {{-- Menu Navigasi Sesuai Role --}}
            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                @if ($isOutlet)
                    <p class="px-3 pb-1.5 font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">
                        Operasional & Kasir Outlet
                    </p>

                    @php
                        $outletLinks = [
                            [
                                'label' => 'Beranda Outlet',
                                'route' => 'outlet.dashboard',
                                'active' => request()->routeIs('outlet.dashboard'),
                                'icon' => 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z',
                            ],
                            [
                                'label' => 'Kasir & Pesanan Masuk',
                                'route' => 'outlet.orders.index',
                                'active' => request()->routeIs('outlet.orders.*') || request()->routeIs('outlet.antrean.*'),
                                'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2',
                            ],
                            [
                                'label' => 'Menu & Stok Porsi',
                                'route' => 'outlet.menus.index',
                                'active' => request()->routeIs('outlet.menus.*'),
                                'icon' => 'M4 6h16M4 12h16M4 18h10',
                            ],
                            [
                                'label' => 'Stok Bahan & Resep (KF-06)',
                                'route' => 'outlet.stok',
                                'active' => request()->routeIs('outlet.stok*'),
                                'icon' => 'M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z',
                            ],
                            [
                                'label' => 'Laporan Outlet (KF-09)',
                                'route' => 'outlet.laporan',
                                'active' => request()->routeIs('outlet.laporan*'),
                                'icon' => 'M18 20V10M12 20V4M6 20v-6',
                            ],
                        ];
                    @endphp

                    @foreach ($outletLinks as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            class="flex min-h-[44px] items-center gap-3 rounded-r-[12px] px-3.5 py-2.5 text-sm font-semibold transition {{ $item['active'] ? 'border-l-[3px] border-l-chili bg-tint-chili text-chili' : 'text-ink-secondary hover:bg-surface-cream hover:text-ink' }}"
                        >
                            <svg class="h-5 w-5 shrink-0 {{ $item['active'] ? 'text-chili' : 'text-ink-secondary' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                            </svg>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                @elseif ($isPusat)
                    <p class="px-3 pb-1.5 font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">
                        Manajemen Brand & Audit Pusat
                    </p>

                    <a
                        href="{{ route('pusat.dashboard') }}"
                        class="flex min-h-[44px] items-center gap-3 rounded-r-[12px] px-3.5 py-2.5 text-sm font-semibold transition {{ request()->routeIs('pusat.dashboard') ? 'border-l-[3px] border-l-chili bg-tint-chili text-chili' : 'text-ink-secondary hover:bg-surface-cream hover:text-ink' }}"
                    >
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('pusat.dashboard') ? 'text-chili' : 'text-ink-secondary' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                        </svg>
                        <span>Beranda Pusat</span>
                    </a>

                    <a
                        href="{{ route('pusat.outlets.index') }}"
                        class="flex min-h-[44px] items-center gap-3 rounded-r-[12px] px-3.5 py-2.5 text-sm font-semibold transition {{ request()->routeIs('pusat.outlets.*') ? 'border-l-[3px] border-l-chili bg-tint-chili text-chili' : 'text-ink-secondary hover:bg-surface-cream hover:text-ink' }}"
                    >
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('pusat.outlets.*') ? 'text-chili' : 'text-ink-secondary' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        </svg>
                        <span>Manajemen Outlet (KF-01)</span>
                    </a>

                    <a
                        href="{{ route('pusat.laporan') }}"
                        class="flex min-h-[44px] items-center gap-3 rounded-r-[12px] px-3.5 py-2.5 text-sm font-semibold transition {{ request()->routeIs('pusat.laporan*') ? 'border-l-[3px] border-l-chili bg-tint-chili text-chili' : 'text-ink-secondary hover:bg-surface-cream hover:text-ink' }}"
                    >
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('pusat.laporan*') ? 'text-chili' : 'text-ink-secondary' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 20V10M12 20V4M6 20v-6" />
                        </svg>
                        <span>Laporan Gabungan (KF-08)</span>
                    </a>

                    <a
                        href="{{ route('pusat.audit') }}"
                        class="flex min-h-[44px] items-center gap-3 rounded-r-[12px] px-3.5 py-2.5 text-sm font-semibold transition {{ request()->routeIs('pusat.audit*') ? 'border-l-[3px] border-l-chili bg-tint-chili text-chili' : 'text-ink-secondary hover:bg-surface-cream hover:text-ink' }}"
                    >
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('pusat.audit*') ? 'text-chili' : 'text-ink-secondary' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                        </svg>
                        <span>Audit Royalti (KF-10)</span>
                    </a>
                @endif
            </nav>

            {{-- Footer Sidebar --}}
            <div class="border-t border-divider p-4 space-y-2">
                <a
                    href="{{ route('catalog.index') }}"
                    class="flex min-h-[40px] w-full items-center justify-center gap-2 rounded-full border border-border-ayi bg-surface-cream px-4 py-2 font-heading text-xs font-bold text-navy hover:bg-tint-navy"
                >
                    <span>Lihat Katalog Publik</span>
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="flex min-h-[40px] w-full items-center justify-center gap-2 rounded-full bg-tint-chili px-4 py-2 font-heading text-xs font-bold text-chili hover:bg-chili hover:text-white transition"
                    >
                        <span>Keluar Sesi</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Area Konten Kanan --}}
        <div class="flex flex-1 flex-col min-w-0">
            {{-- Top Bar Mobile/Tablet --}}
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-border-ayi bg-cream px-4 sm:px-6 lg:hidden">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="drawerOpen = true"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-[12px] border border-border-ayi bg-surface text-ink shadow-ayi-xs"
                        aria-label="Buka menu navigasi"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M3 6h18M3 18h18" />
                        </svg>
                    </button>
                    <span class="font-display-brand text-2xl text-chili">Ayiriz</span>
                </div>

                <span class="ayi-chip-sample">Data Contoh</span>
            </header>

            {{-- Toast Notifikasi --}}
            @if (session('toast_success') || session('toast_info') || $errors->any())
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-init="setTimeout(() => show = false, 6000)"
                    class="fixed right-4 top-4 z-50 w-full max-w-md px-4 sm:px-0"
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
                            <button type="button" @click="show = false" class="rounded p-1 text-ink-muted hover:text-ink" aria-label="Tutup">✕</button>
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
                            <button type="button" @click="show = false" class="rounded p-1 text-ink-muted hover:text-ink" aria-label="Tutup">✕</button>
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
                            <button type="button" @click="show = false" class="rounded p-1 text-ink-muted hover:text-ink" aria-label="Tutup">✕</button>
                        </div>
                    @endif
                </div>
            @endif

            <main class="page-content flex-1 p-4 sm:p-6 lg:p-8">
                <div class="mx-auto max-w-6xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>
</html>

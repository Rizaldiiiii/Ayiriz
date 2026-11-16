<x-layouts.panel title="Dashboard Pusat Ayiriz">
    {{-- Header --}}
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal="up">
        <div>
            <div class="ayi-accent-bar mb-2 w-12"></div>
            <h1 class="font-heading text-[32px] font-bold text-ink">
                DASHBOARD PUSAT AYIRIZ
            </h1>
            <p class="text-sm text-ink-secondary">
                Pantau kantin mitra di depan gerbang UNG, verifikasi outlet baru, dan ringkasan bagi hasil royalti dari pesanan Selesai.
            </p>
        </div>

        <a href="{{ route('pusat.outlets.create') }}" class="ayi-btn-primary">
            + Daftarkan Kantin Mitra
        </a>
    </div>

    {{-- KPI Tiles --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="ayi-card p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">Total Kantin Mitra</span>
            <p class="mt-2 font-heading text-[28px] font-bold text-ink tabular-nums">
                {{ $summary['total_outlet'] }} Outlet
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                {{ $summary['outlet_aktif'] }} Aktif · {{ $summary['outlet_menunggu'] }} Menunggu Verifikasi
            </p>
        </div>

        <div class="ayi-card p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">Penjualan Gabungan (Selesai)</span>
            <p class="mt-2 font-heading text-[28px] font-bold text-navy tabular-nums">
                Rp {{ number_format($summary['total_penjualan_selesai'], 0, ',', '.') }}
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Hanya dihitung dari pesanan Selesai
            </p>
        </div>

        <div class="ayi-card p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">Total Bagi Hasil Royalti</span>
            <p class="mt-2 font-heading text-[28px] font-bold text-chili tabular-nums">
                Rp {{ number_format($summary['total_royalti_selesai'], 0, ',', '.') }}
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Royalti awal 10% per pesanan Selesai
            </p>
        </div>

        <div class="ayi-card bg-surface-cream p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-navy">Model Franchise (b)</span>
            <p class="mt-2 font-heading text-lg font-bold text-ink">
                Outlet Bebas Menu Lokal
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Pusat tidak mengubah menu/harga outlet. Pusat memverifikasi outlet & menerima royalti.
            </p>
        </div>
    </div>

    {{-- Ringkasan Outlet --}}
    <div class="mt-8">
        <div class="flex items-center justify-between gap-2">
            <h2 class="font-heading text-[24px] font-bold text-ink">
                RINGKASAN KANTIN MITRA & PENJUALAN
            </h2>
            <a href="{{ route('pusat.outlets.index') }}" class="text-sm font-bold text-chili underline">
                Kelola Semua Outlet →
            </a>
        </div>

        {{-- Tabel Desktop --}}
        <div class="ayi-card mt-4 hidden overflow-hidden md:block">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase tracking-wider text-ink">
                    <tr>
                        <th class="px-5 py-3.5">Kantin Mitra</th>
                        <th class="px-5 py-3.5">Pemilik & Kontak</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Royalti</th>
                        <th class="px-5 py-3.5 text-right">Penjualan Selesai</th>
                        <th class="px-5 py-3.5 text-right">Royalti Terhitung</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-divider">
                    @foreach ($outlets as $outlet)
                        <tr class="hover:bg-surface-cream">
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="font-heading text-base font-bold text-ink">{{ $outlet->nama }}</span>
                                    @if ($outlet->is_sample)
                                        <span class="ayi-chip-sample">Data Contoh</span>
                                    @endif
                                </div>
                                <p class="text-xs text-ink-secondary">{{ $outlet->alamat }} · {{ $outlet->menus_count }} Menu</p>
                            </td>
                            <td class="px-5 py-4 text-xs text-ink-secondary">
                                <div class="font-semibold text-ink">{{ $outlet->pemilik }}</div>
                                <div>{{ $outlet->no_hp }} · {{ $outlet->user?->email }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <x-status-badge :status="$outlet->status" type="outlet" />
                            </td>
                            <td class="px-5 py-4 text-right font-heading font-bold text-navy tabular-nums">
                                {{ number_format((float) $outlet->royalty_percent, 0) }}%
                            </td>
                            <td class="px-5 py-4 text-right font-heading text-base font-bold text-ink tabular-nums">
                                Rp {{ number_format((int) $outlet->total_penjualan, 0, ',', '.') }}
                                <span class="block text-[11px] font-normal text-ink-muted">
                                    {{ $outlet->orders_selesai_count }} Selesai · {{ $outlet->orders_diproses_count }} Diproses
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right font-heading text-base font-bold text-chili tabular-nums">
                                Rp {{ number_format((int) $outlet->total_royalti, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('pusat.outlets.edit', $outlet) }}" class="ayi-btn-secondary min-h-[36px] px-3.5 py-1.5 text-xs">
                                    Kelola
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Kartu Mobile --}}
        <div class="mt-4 space-y-4 md:hidden">
            @foreach ($outlets as $outlet)
                <article class="ayi-card p-5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="font-heading text-lg font-bold text-ink">{{ $outlet->nama }}</h3>
                            <p class="text-xs text-ink-secondary">Pemilik: {{ $outlet->pemilik }} ({{ $outlet->no_hp }})</p>
                        </div>
                        <x-status-badge :status="$outlet->status" type="outlet" />
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2 rounded-[10px] bg-surface-cream p-3 text-xs">
                        <div>
                            <span class="text-ink-muted">Penjualan Selesai</span>
                            <p class="font-heading text-base font-bold text-ink tabular-nums">
                                Rp {{ number_format((int) $outlet->total_penjualan, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <span class="text-ink-muted">Royalti ({{ number_format((float) $outlet->royalty_percent, 0) }}%)</span>
                            <p class="font-heading text-base font-bold text-chili tabular-nums">
                                Rp {{ number_format((int) $outlet->total_royalti, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-3 flex justify-end">
                        <a href="{{ route('pusat.outlets.edit', $outlet) }}" class="ayi-btn-secondary min-h-[38px] px-4 py-1.5 text-xs">
                            Kelola Outlet
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</x-layouts.panel>

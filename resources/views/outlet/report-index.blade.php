<x-layouts.panel :title="'Laporan Outlet ' . $outlet->nama">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal="up">
        <div>
            <div class="ayi-accent-bar mb-2 w-12"></div>
            <h1 class="font-heading text-[32px] font-bold text-ink">
                LAPORAN OUTLET — {{ strtoupper($outlet->nama) }} (KF-09)
            </h1>
            <p class="text-sm text-ink-secondary">
                Rekap penjualan harian/mingguan/bulanan (hanya pesanan Selesai), menu terlaris, stok bahan menipis, dan hitungan reaksi per menu di <strong>{{ $outlet->nama }}</strong>.
            </p>
        </div>
    </div>

    {{-- Rekap Cepat 3 Periode (Harian, Mingguan, Bulanan) --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        @foreach ($rekapPeriode as $key => $rp)
            <div class="ayi-card p-5 {{ $periode === $key ? 'border-2 border-chili' : '' }}">
                <span class="font-heading text-xs font-bold uppercase tracking-wider text-navy">{{ $rp['label'] }}</span>
                <p class="mt-2 font-heading text-[26px] font-bold text-ink tabular-nums">
                    Rp {{ number_format($rp['penjualan'], 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-ink-secondary">
                    {{ $rp['pesanan'] }} Pesanan Selesai · Royalti 10%: <strong class="text-chili tabular-nums">Rp {{ number_format($rp['royalti'], 0, ',', '.') }}</strong>
                </p>
            </div>
        @endforeach
    </div>

    {{-- Tab Filter Periode --}}
    <div class="mt-6 flex flex-wrap gap-2 border-b border-divider pb-3">
        @php
            $periodeTabs = [
                'harian' => 'Harian (Hari Ini)',
                'mingguan' => 'Mingguan (7 Hari)',
                'bulanan' => 'Bulanan (Bulan Ini)',
                'semua' => 'Semua Waktu',
            ];
        @endphp
        @foreach ($periodeTabs as $pKey => $pLabel)
            <a
                href="{{ route('outlet.laporan', ['periode' => $pKey]) }}"
                class="rounded-full px-4 py-2 font-heading text-sm font-bold transition {{ $periode === $pKey ? 'bg-tint-chili text-chili border-b-2 border-chili' : 'bg-surface text-ink-secondary hover:text-ink' }}"
            >
                {{ $pLabel }}
            </a>
        @endforeach
    </div>

    {{-- KPI Ringkasan Periode Terpilih --}}
    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="ayi-card bg-surface-cream p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">Penjualan Kotor (Selesai)</span>
            <p class="mt-2 font-heading text-[26px] font-bold text-ink tabular-nums">
                Rp {{ number_format($selectedSummary['total_penjualan'], 0, ',', '.') }}
            </p>
        </div>
        <div class="ayi-card bg-surface-cream p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-chili">Bagi Hasil Royalti ({{ number_format((float) $outlet->royalty_percent, 0) }}%)</span>
            <p class="mt-2 font-heading text-[26px] font-bold text-chili tabular-nums">
                Rp {{ number_format($selectedSummary['total_royalti'], 0, ',', '.') }}
            </p>
        </div>
        <div class="ayi-card bg-surface-cream p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-leaf">Pendapatan Bersih Outlet</span>
            <p class="mt-2 font-heading text-[26px] font-bold text-leaf tabular-nums">
                Rp {{ number_format($selectedSummary['pendapatan_bersih'], 0, ',', '.') }}
            </p>
        </div>
        <div class="ayi-card bg-surface-cream p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-navy">Transaksi Selesai</span>
            <p class="mt-2 font-heading text-[26px] font-bold text-navy tabular-nums">
                {{ $selectedSummary['jumlah_selesai'] }} Pesanan
            </p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-12 lg:items-start">
        {{-- Menu Terlaris di Outlet Ini --}}
        <section class="ayi-card p-6 lg:col-span-7">
            <h2 class="font-heading text-[22px] font-bold text-ink">
                MENU TERLARIS DI {{ strtoupper($outlet->nama) }}
            </h2>
            <p class="mt-1 text-xs text-ink-secondary">
                Diurutkan berdasarkan jumlah porsi terjual dari pesanan berstatus Selesai pada periode terpilih.
            </p>

            @if ($menuTerlaris->count() > 0)
                <div class="mt-4 overflow-hidden rounded-[12px] border border-border-ayi">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase text-ink">
                            <tr>
                                <th class="px-4 py-3">Menu</th>
                                <th class="px-4 py-3 text-center">Porsi Terjual</th>
                                <th class="px-4 py-3 text-right">Total Penjualan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-divider bg-surface">
                            @foreach ($menuTerlaris as $row)
                                <tr>
                                    <td class="px-4 py-3.5 font-semibold text-ink">
                                        {{ $row->nama_menu_saat_submit }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-heading text-base font-bold text-navy tabular-nums">
                                        {{ (int) $row->total_porsi }} porsi
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-heading text-base font-bold text-chili tabular-nums">
                                        Rp {{ number_format((int) $row->total_omzet, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="mt-4 rounded-[12px] bg-surface-cream p-4 text-sm text-ink-secondary">
                    Belum ada penjualan menu yang berstatus Selesai pada periode ini.
                </p>
            @endif
        </section>

        {{-- Stok Bahan Menipis di Outlet Ini --}}
        <section class="ayi-card p-6 lg:col-span-5">
            <div class="flex items-center justify-between gap-2">
                <h2 class="font-heading text-[22px] font-bold text-ink">
                    STOK BAHAN MENIPIS
                </h2>
                <a href="{{ route('outlet.stok') }}" class="text-xs font-bold text-chili underline">
                    Kelola Stok →
                </a>
            </div>
            <p class="mt-1 text-xs text-ink-secondary">
                Bahan baku dengan sisa stok kurang dari atau sama dengan batas minimum.
            </p>

            @if ($stokMenipis->count() > 0)
                <div class="mt-4 space-y-2.5">
                    @foreach ($stokMenipis as $ing)
                        <div class="flex items-center justify-between rounded-[12px] border border-border-strong bg-warning-bg p-3.5 text-xs">
                            <div>
                                <p class="font-heading text-base font-bold text-ink">{{ $ing->nama }}</p>
                                <p class="text-ink-secondary">Batas minimum: {{ $ing->formattedBatasMinimum() }}</p>
                            </div>
                            <span class="rounded-full bg-surface px-3 py-1 font-heading text-sm font-bold text-chili tabular-nums shadow-ayi-xs">
                                Sisa {{ $ing->formattedStok() }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-4 rounded-[12px] bg-tint-leaf p-4 text-xs font-semibold text-leaf">
                    Seluruh stok bahan baku berada di atas batas minimum.
                </div>
            @endif
        </section>
    </div>

    {{-- Hitungan Reaksi Emote per Menu di Outlet Ini (Tanpa Skor / Rata-rata / Ranking) --}}
    <section class="mt-8">
        <div class="ayi-card p-6">
            <h2 class="font-heading text-[22px] font-bold text-ink">
                HITUNGAN REAKSI EMOTE PER MENU DI {{ strtoupper($outlet->nama) }}
            </h2>
            <p class="mt-1 text-xs text-ink-secondary">
                Kesan pelanggan terhadap masing-masing menu di outlet Anda (<strong>😋 Enak · 🥵 Pedas · 🫠 Manis / menyejukkan</strong>) — ditampilkan sebagai hitungan per emote tanpa skor total, tanpa rata-rata, dan tanpa peringkat.
            </p>

            <div class="mt-4 overflow-hidden rounded-[12px] border border-border-ayi">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase text-ink">
                        <tr>
                            <th class="px-4 py-3">Nama Menu</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3 text-center">😋 Enak</th>
                            <th class="px-4 py-3 text-center">🥵 Pedas</th>
                            <th class="px-4 py-3 text-center">🫠 Manis / menyejukkan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider bg-surface">
                        @foreach ($menusWithReactions as $menu)
                            @php
                                $rc = $menu->reactionCounts();
                            @endphp
                            <tr>
                                <td class="px-4 py-3.5 font-semibold text-ink">
                                    {{ $menu->nama }}
                                </td>
                                <td class="px-4 py-3.5 text-xs text-ink-secondary">
                                    {{ $menu->category?->nama }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-tint-chili px-3 py-0.5 font-heading text-sm font-bold text-ink tabular-nums">
                                        😋 {{ $rc['enak'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-tint-tang px-3 py-0.5 font-heading text-sm font-bold text-ink tabular-nums">
                                        🥵 {{ $rc['pedas'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-tint-sunny px-3 py-0.5 font-heading text-sm font-bold text-ink tabular-nums">
                                        🫠 {{ $rc['manis'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layouts.panel>

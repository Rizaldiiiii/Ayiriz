<x-layouts.panel title="Laporan & Dashboard Gabungan Pusat (KF-08)">
    {{-- Header --}}
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal="up">
        <div>
            <div class="ayi-accent-bar mb-2 w-12"></div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="font-heading text-[32px] font-bold text-ink">
                    LAPORAN & DASHBOARD GABUNGAN PUSAT (KF-08)
                </h1>
                <span class="ayi-chip-sample">Data Contoh</span>
            </div>
            <p class="mt-1 text-sm text-ink-secondary">
                Rekapitulasi penjualan gabungan seluruh kantin mitra Ayiriz (hanya dari pesanan <strong>Selesai</strong>), perbandingan penjualan & royalti per outlet per periode, serta menu terlaris <strong>per outlet</strong>.
            </p>
        </div>

        <a href="{{ route('pusat.audit', ['semester' => $selectedSemester]) }}" class="ayi-btn-primary">
            Buka Audit Royalti Semester (KF-10) →
        </a>
    </div>

    {{-- Filter Periode & Pemilih Semester --}}
    <div class="mt-6 flex flex-col justify-between gap-4 rounded-[12px] border border-border-ayi bg-surface p-4 shadow-ayi-xs lg:flex-row lg:items-center">
        <div class="flex flex-wrap items-center gap-2" role="tablist" aria-label="Filter periode laporan pusat">
            @php
                $tabs = [
                    'harian' => 'Harian (Hari Ini)',
                    'mingguan' => 'Mingguan (7 Hari)',
                    'bulanan' => 'Bulanan (Bulan Ini)',
                    'semester' => 'Per Semester (' . $selectedSemester . ')',
                    'semua' => 'Semua Periode',
                ];
            @endphp

            @foreach ($tabs as $key => $label)
                <a
                    href="{{ route('pusat.laporan', ['periode' => $key, 'semester' => $selectedSemester]) }}"
                    class="inline-flex min-h-[40px] items-center rounded-full px-4 py-1.5 font-heading text-xs font-bold transition {{ $periode === $key ? 'bg-chili text-white shadow-ayi-xs' : 'border border-border-ayi bg-surface-cream text-ink-secondary hover:border-navy hover:text-navy' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('pusat.laporan') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="periode" value="semester" />
            <label for="semester_select" class="font-heading text-xs font-bold uppercase tracking-wider text-navy">
                Pilih Semester:
            </label>
            <select
                id="semester_select"
                name="semester"
                class="ayi-input min-h-[38px] w-auto py-1.5 text-xs font-semibold"
                onchange="this.form.submit()"
            >
                @foreach ($availableSemesters as $semCode => $semLabel)
                    <option value="{{ $semCode }}" @selected($selectedSemester === $semCode)>
                        {{ $semLabel }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="ayi-btn-secondary min-h-[38px] px-3.5 py-1.5 text-xs">
                Terapkan
            </button>
        </form>
    </div>

    {{-- 4 Kartu Ringkasan Periode (Harian, Mingguan, Bulanan, Semester) --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($rekapPeriode as $key => $item)
            <a
                href="{{ route('pusat.laporan', ['periode' => $key, 'semester' => $selectedSemester]) }}"
                class="ayi-card block p-5 transition hover:border-navy {{ $periode === $key ? 'border-2 border-chili bg-surface-cream' : '' }}"
            >
                <div class="flex items-center justify-between gap-2">
                    <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">
                        {{ $item['label'] }}
                    </span>
                    @if ($periode === $key)
                        <span class="rounded-full bg-tint-chili px-2.5 py-0.5 font-heading text-[11px] font-bold text-chili">
                            Aktif
                        </span>
                    @endif
                </div>
                <p class="mt-2 font-heading text-[24px] font-bold text-ink tabular-nums">
                    Rp {{ number_format($item['penjualan'], 0, ',', '.') }}
                </p>
                <div class="mt-2 flex flex-wrap items-center justify-between gap-2 text-xs text-ink-secondary">
                    <span>{{ $item['pesanan'] }} Pesanan Selesai</span>
                    <span class="font-semibold text-chili">
                        Royalti: Rp {{ number_format($item['royalti'], 0, ',', '.') }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Banner Total Gabungan Periode Terpilih --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="ayi-card border-l-4 border-l-navy p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">
                Total Penjualan Pusat (Periode Terpilih)
            </span>
            <p class="mt-1.5 font-heading text-[28px] font-bold text-navy tabular-nums" data-testid="pusat-total-penjualan">
                Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Jumlah total pesanan <strong>Selesai</strong> dari seluruh outlet pada periode ini
            </p>
        </div>

        <div class="ayi-card border-l-4 border-l-chili p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">
                Total Bagi Hasil Royalti Pusat
            </span>
            <p class="mt-1.5 font-heading text-[28px] font-bold text-chili tabular-nums" data-testid="pusat-total-royalti">
                Rp {{ number_format($summary['total_royalti'], 0, ',', '.') }}
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Akumulasi royalti pesanan Selesai seluruh kantin mitra
            </p>
        </div>

        <div class="ayi-card border-l-4 border-l-leaf p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">
                Volume Transaksi Selesai
            </span>
            <p class="mt-1.5 font-heading text-[28px] font-bold text-leaf tabular-nums">
                {{ $summary['jumlah_selesai'] }} Pesanan
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Tersebar di {{ $summary['total_outlet'] }} kantin mitra terdaftar
            </p>
        </div>
    </div>

    {{-- Tabel Perbandingan Penjualan & Royalti Antar Outlet (Audit Bisnis Pusat — KF-08) --}}
    <section class="mt-8">
        <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-end">
            <div>
                <h2 class="font-heading text-[24px] font-bold text-ink">
                    PENJUALAN & ROYALTI PER OUTLET
                </h2>
                <p class="text-xs text-ink-secondary">
                    Perbandingan penjualan antar outlet untuk evaluasi bisnis oleh Pusat. Dihitung <strong>hanya dari pesanan Selesai</strong> (tanpa pengurutan atau perbandingan berdasarkan reaksi emote pelanggan).
                </p>
            </div>
        </div>

        {{-- Tabel Desktop --}}
        <div class="ayi-card mt-4 hidden overflow-hidden md:block">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase tracking-wider text-ink">
                    <tr>
                        <th class="px-5 py-3.5">Kantin Mitra</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Persen Royalti</th>
                        <th class="px-5 py-3.5 text-right">Pesanan Selesai</th>
                        <th class="px-5 py-3.5 text-right">Total Penjualan (Selesai)</th>
                        <th class="px-5 py-3.5 text-right">Total Royalti Outlet</th>
                        <th class="px-5 py-3.5 text-right">Aksi Audit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-divider">
                    @foreach ($outlets as $outlet)
                        @php
                            $penjualanOutlet = (int) $outlet->period_penjualan;
                            $royaltiOutlet = (int) $outlet->period_royalti;
                        @endphp
                        <tr class="hover:bg-surface-cream">
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="font-heading text-base font-bold text-ink">{{ $outlet->nama }}</span>
                                    @if ($outlet->is_sample)
                                        <span class="ayi-chip-sample">Data Contoh</span>
                                    @endif
                                </div>
                                <p class="text-xs text-ink-secondary">
                                    Pemilik: {{ $outlet->pemilik }} · {{ $outlet->menus_count }} Menu
                                </p>
                            </td>
                            <td class="px-5 py-4">
                                <x-status-badge :status="$outlet->status" type="outlet" />
                            </td>
                            <td class="px-5 py-4 text-right font-heading font-bold text-navy tabular-nums">
                                {{ number_format((float) $outlet->royalty_percent, 2, ',', '.') }}%
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums">
                                <span class="font-heading font-bold text-ink">{{ $outlet->orders_selesai_count }}</span> Selesai
                                @if ($outlet->orders_diproses_count > 0)
                                    <span class="block text-[11px] text-ink-muted">
                                        ({{ $outlet->orders_diproses_count }} Diproses — belum dihitung)
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right font-heading text-base font-bold text-ink tabular-nums">
                                Rp {{ number_format($penjualanOutlet, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right font-heading text-base font-bold text-chili tabular-nums">
                                Rp {{ number_format($royaltiOutlet, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a
                                    href="{{ route('pusat.audit', ['semester' => $selectedSemester, 'outlet_id' => $outlet->id]) }}"
                                    class="ayi-btn-secondary min-h-[36px] px-3.5 py-1.5 text-xs"
                                >
                                    Audit Semester
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-navy bg-surface-cream font-heading text-sm font-bold text-ink">
                    <tr>
                        <td colspan="3" class="px-5 py-3.5">TOTAL GABUNGAN PUSAT (PESANAN SELESAI)</td>
                        <td class="px-5 py-3.5 text-right tabular-nums">{{ $summary['jumlah_selesai'] }} Selesai</td>
                        <td class="px-5 py-3.5 text-right text-base text-navy tabular-nums">
                            Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3.5 text-right text-base text-chili tabular-nums">
                            Rp {{ number_format($summary['total_royalti'], 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Kartu Mobile --}}
        <div class="mt-4 space-y-4 md:hidden">
            @foreach ($outlets as $outlet)
                <article class="ayi-card p-5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="font-heading text-lg font-bold text-ink">{{ $outlet->nama }}</h3>
                            <p class="text-xs text-ink-secondary">Pemilik: {{ $outlet->pemilik }}</p>
                        </div>
                        <x-status-badge :status="$outlet->status" type="outlet" />
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2 rounded-[10px] bg-surface-cream p-3 text-xs">
                        <div>
                            <span class="text-ink-muted">Penjualan Selesai ({{ $outlet->orders_selesai_count }})</span>
                            <p class="font-heading text-base font-bold text-ink tabular-nums">
                                Rp {{ number_format((int) $outlet->period_penjualan, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <span class="text-ink-muted">Royalti ({{ number_format((float) $outlet->royalty_percent, 0) }}%)</span>
                            <p class="font-heading text-base font-bold text-chili tabular-nums">
                                Rp {{ number_format((int) $outlet->period_royalti, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-3 flex justify-end">
                        <a
                            href="{{ route('pusat.audit', ['semester' => $selectedSemester, 'outlet_id' => $outlet->id]) }}"
                            class="ayi-btn-secondary min-h-[38px] px-4 py-1.5 text-xs"
                        >
                            Audit Royalti Semester
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Menu Terlaris PER OUTLET (Bukan Digabung Antar Outlet — PRD KF-08) --}}
    <section class="mt-10">
        <div>
            <h2 class="font-heading text-[24px] font-bold text-ink">
                MENU TERLARIS PER OUTLET (PESANAN SELESAI)
            </h2>
            <p class="text-xs text-ink-secondary">
                Sesuai PRD §4 KF-08, daftar menu terlaris dikelompokkan <strong>per outlet masing-masing</strong> (bukan diadu lintas kantin) berdasarkan jumlah porsi pesanan Selesai pada periode terpilih.
            </p>
        </div>

        <div class="mt-4 grid gap-6 lg:grid-cols-3">
            @foreach ($outlets as $outlet)
                @php
                    $topMenus = $topMenusByOutlet[$outlet->id] ?? collect();
                @endphp
                <div class="ayi-card flex flex-col justify-between p-5" data-testid="top-menu-outlet-{{ $outlet->slug }}">
                    <div>
                        <div class="flex items-start justify-between gap-2 border-b border-divider pb-3">
                            <div>
                                <h3 class="font-heading text-lg font-bold text-ink">{{ $outlet->nama }}</h3>
                                <p class="text-xs text-ink-secondary">
                                    Penjualan Periode Ini: <strong>Rp {{ number_format((int) $outlet->period_penjualan, 0, ',', '.') }}</strong>
                                </p>
                            </div>
                            <span class="rounded-full bg-tint-navy px-2.5 py-0.5 font-heading text-xs font-bold text-navy">
                                {{ number_format((float) $outlet->royalty_percent, 0) }}% Royalti
                            </span>
                        </div>

                        @if ($topMenus->isEmpty())
                            <div class="mt-4 rounded-[10px] bg-surface-cream p-4 text-center text-xs text-ink-muted">
                                Belum ada menu terjual (pesanan Selesai) pada periode ini.
                            </div>
                        @else
                            <ul class="mt-3 divide-y divide-divider text-sm">
                                @foreach ($topMenus as $idx => $menuRow)
                                    <li class="flex items-center justify-between gap-3 py-2.5">
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-ink">
                                                {{ $idx + 1 }}. {{ $menuRow->nama_menu_saat_submit }}
                                            </p>
                                            <p class="text-xs text-ink-secondary tabular-nums">
                                                Omzet: Rp {{ number_format((int) $menuRow->total_omzet, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <span class="shrink-0 rounded-full bg-tint-sunny px-3 py-1 font-heading text-xs font-bold text-ink tabular-nums">
                                            {{ (int) $menuRow->total_porsi }} Porsi
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="mt-4 border-t border-divider pt-3 flex items-center justify-between text-xs">
                        <span class="text-ink-muted">Royalti Terhitung:</span>
                        <span class="font-heading text-sm font-bold text-chili tabular-nums">
                            Rp {{ number_format((int) $outlet->period_royalti, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-layouts.panel>

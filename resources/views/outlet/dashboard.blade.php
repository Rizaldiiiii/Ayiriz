<x-layouts.panel :title="'Dashboard ' . $outlet->nama">
    {{-- Header --}}
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal="up">
        <div>
            <div class="ayi-accent-bar mb-2 w-12"></div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="font-heading text-[32px] font-bold text-ink">
                    {{ strtoupper($outlet->nama) }}
                </h1>
                <x-status-badge :status="$outlet->status" type="outlet" />
                @if ($outlet->is_sample)
                    <span class="ayi-chip-sample">Data Contoh</span>
                @endif
            </div>
            <p class="mt-1 text-sm text-ink-secondary">
                {{ $outlet->alamat }} · Jam Buka: {{ $outlet->jam_buka }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('outlet.orders.index') }}" class="ayi-btn-primary">
                Buka Kasir ({{ $summary['jumlah_antrean'] }} Antrean · {{ $summary['jumlah_diproses'] }} Diproses)
            </a>
            <a href="{{ route('outlet.menus.create') }}" class="ayi-btn-secondary">
                + Tambah Menu
            </a>
        </div>
    </div>

    {{-- KPI Ringkasan Outlet --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="ayi-card p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-warning">Daftar Tunggu (Antrean)</span>
            <p class="mt-2 font-heading text-[28px] font-bold text-ink tabular-nums">
                {{ $summary['jumlah_antrean'] }} Antrean
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Belum bayar · stok belum dikurangi
            </p>
        </div>

        <div class="ayi-card p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-[#8A3410]">Pesanan Diproses</span>
            <p class="mt-2 font-heading text-[28px] font-bold text-ink tabular-nums">
                {{ $summary['jumlah_diproses'] }} Pesanan
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Sudah bayar tunai · sedang dimasak
            </p>
        </div>

        <div class="ayi-card p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-leaf">Pesanan Selesai</span>
            <p class="mt-2 font-heading text-[28px] font-bold text-leaf tabular-nums">
                {{ $summary['jumlah_selesai'] }} Pesanan
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Sudah diambil pelanggan
            </p>
        </div>

        <div class="ayi-card p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-navy">Penjualan (Selesai)</span>
            <p class="mt-2 font-heading text-[24px] font-bold text-ink tabular-nums">
                Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Hanya dari pesanan Selesai
            </p>
        </div>

        <div class="ayi-card p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-chili">Royalti ({{ number_format((float) $outlet->royalty_percent, 0) }}%)</span>
            <p class="mt-2 font-heading text-[24px] font-bold text-chili tabular-nums">
                Rp {{ number_format($summary['total_royalti'], 0, ',', '.') }}
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Dari pesanan berstatus Selesai
            </p>
        </div>
    </div>

    @if ($menuHabisCount > 0)
        <div class="mt-5 flex items-center justify-between gap-4 rounded-[12px] border border-border-ayi bg-warning-bg p-4 text-sm text-warning">
            <span>Terdapat <strong>{{ $menuHabisCount }} menu</strong> dengan stok porsi 0 atau berstatus tidak tersedia.</span>
            <a href="{{ route('outlet.menus.index') }}" class="font-heading text-xs font-bold underline">Kelola Stok Porsi →</a>
        </div>
    @endif

    {{-- Bagian 1: Daftar Tunggu (Antrean Belum Dibayar) --}}
    <div class="mt-8">
        <div class="flex items-center justify-between gap-2">
            <div>
                <h2 class="font-heading text-[24px] font-bold text-ink">
                    DAFTAR TUNGGU (ANTREAN BELUM DIBAYAR)
                </h2>
                <p class="text-xs text-ink-secondary">
                    Saat pelanggan datang dan membayar tunai di kasir, konfirmasi pembayaran agar antrean menjadi pesanan Diproses dan stok porsi berkurang.
                </p>
            </div>
            <a href="{{ route('outlet.orders.index') }}" class="text-sm font-bold text-chili underline">
                Buka Layar Kasir →
            </a>
        </div>

        @if ($antreans->count() > 0)
            <div class="ayi-card mt-4 hidden overflow-hidden md:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase text-ink">
                        <tr>
                            <th class="px-5 py-3.5">Kode Antrean</th>
                            <th class="px-5 py-3.5">Nama Pemesan</th>
                            <th class="px-5 py-3.5">Item Menu</th>
                            <th class="px-5 py-3.5">Waktu Submit</th>
                            <th class="px-5 py-3.5 text-right">Total Tunai</th>
                            <th class="px-5 py-3.5 text-right">Aksi Kasir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach ($antreans as $antrean)
                            <tr class="hover:bg-surface-cream">
                                <td class="px-5 py-4 font-heading text-base font-bold text-chili">
                                    {{ $antrean->kode }}
                                </td>
                                <td class="px-5 py-4 font-semibold text-ink">
                                    {{ $antrean->nama_pemesan }}
                                </td>
                                <td class="px-5 py-4 text-xs text-ink-secondary">
                                    @foreach ($antrean->items as $item)
                                        <div>{{ $item->jumlah }}x {{ $item->nama_menu_saat_submit }}</div>
                                    @endforeach
                                </td>
                                <td class="px-5 py-4 text-xs text-ink-secondary">
                                    {{ $antrean->created_at?->translatedFormat('d M Y, H:i') }} WITA
                                </td>
                                <td class="px-5 py-4 text-right font-heading text-lg font-bold text-ink tabular-nums">
                                    {{ $antrean->formattedTotal() }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('outlet.antrean.show', $antrean) }}" class="ayi-btn-primary min-h-[38px] px-4 py-1.5 text-xs">
                                        Terima Bayar Tunai
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 space-y-3 md:hidden">
                @foreach ($antreans as $antrean)
                    <article class="ayi-card p-4">
                        <div class="flex items-center justify-between">
                            <span class="font-heading text-lg font-bold text-chili">{{ $antrean->kode }}</span>
                            <span class="rounded-full bg-warning-bg px-3 py-0.5 font-heading text-xs font-bold text-warning">Daftar Tunggu</span>
                        </div>
                        <p class="mt-1 text-sm font-bold text-ink">Pemesan: {{ $antrean->nama_pemesan }}</p>
                        <div class="mt-2 text-xs text-ink-secondary">
                            @foreach ($antrean->items as $item)
                                <div>{{ $item->jumlah }}x {{ $item->nama_menu_saat_submit }}</div>
                            @endforeach
                        </div>
                        <div class="mt-3 flex items-center justify-between border-t border-divider pt-3">
                            <span class="font-heading text-lg font-bold text-ink tabular-nums">{{ $antrean->formattedTotal() }}</span>
                            <a href="{{ route('outlet.antrean.show', $antrean) }}" class="ayi-btn-primary min-h-[38px] px-4 py-1.5 text-xs">
                                Terima Bayar Tunai
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="ayi-card mt-4 p-6 text-center">
                <p class="font-heading text-base font-bold text-ink">Tidak ada antrean di daftar tunggu saat ini.</p>
            </div>
        @endif
    </div>

    {{-- Bagian 2: Pesanan Diproses (Sudah Dibayar Tunai, Sedang Dimasak) --}}
    <div class="mt-8">
        <div class="flex items-center justify-between gap-2">
            <div>
                <h2 class="font-heading text-[24px] font-bold text-ink">
                    PESANAN DIPROSES (SUDAH DIBAYAR · SEDANG DIMASAK)
                </h2>
                <p class="text-xs text-ink-secondary">
                    Pesanan di bawah ini sudah dibayar tunai di kasir. Tandai Selesai saat pelanggan mengambil makanannya.
                </p>
            </div>
        </div>

        @if ($pesananDiproses->count() > 0)
            <div class="ayi-card mt-4 hidden overflow-hidden md:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase text-ink">
                        <tr>
                            <th class="px-5 py-3.5">Kode Pesanan</th>
                            <th class="px-5 py-3.5">Nama Pemesan</th>
                            <th class="px-5 py-3.5">Item Dipesan</th>
                            <th class="px-5 py-3.5">Waktu Bayar</th>
                            <th class="px-5 py-3.5 text-right">Total Bayar</th>
                            <th class="px-5 py-3.5 text-right">Aksi Kasir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach ($pesananDiproses as $order)
                            <tr class="hover:bg-surface-cream">
                                <td class="px-5 py-4 font-heading text-base font-bold text-chili">
                                    {{ $order->kode }}
                                </td>
                                <td class="px-5 py-4 font-semibold text-ink">
                                    {{ $order->nama_pemesan }}
                                </td>
                                <td class="px-5 py-4 text-xs text-ink-secondary">
                                    @foreach ($order->items as $item)
                                        <div>{{ $item->jumlah }}x {{ $item->nama_menu_saat_submit }}</div>
                                    @endforeach
                                </td>
                                <td class="px-5 py-4 text-xs text-ink-secondary">
                                    {{ $order->created_at?->translatedFormat('d M Y, H:i') }} WITA
                                </td>
                                <td class="px-5 py-4 text-right font-heading text-lg font-bold text-ink tabular-nums">
                                    {{ $order->formattedTotal() }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('outlet.orders.show', $order) }}" class="ayi-btn-primary min-h-[38px] px-4 py-1.5 text-xs">
                                        Buka Struk / Selesai
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 space-y-3 md:hidden">
                @foreach ($pesananDiproses as $order)
                    <article class="ayi-card p-4">
                        <div class="flex items-center justify-between">
                            <span class="font-heading text-lg font-bold text-chili">{{ $order->kode }}</span>
                            <x-status-badge :status="$order->status" type="order" />
                        </div>
                        <p class="mt-1 text-sm font-bold text-ink">Pemesan: {{ $order->nama_pemesan }}</p>
                        <div class="mt-2 text-xs text-ink-secondary">
                            @foreach ($order->items as $item)
                                <div>{{ $item->jumlah }}x {{ $item->nama_menu_saat_submit }}</div>
                            @endforeach
                        </div>
                        <div class="mt-3 flex items-center justify-between border-t border-divider pt-3">
                            <span class="font-heading text-lg font-bold text-ink tabular-nums">{{ $order->formattedTotal() }}</span>
                            <a href="{{ route('outlet.orders.show', $order) }}" class="ayi-btn-primary min-h-[38px] px-4 py-1.5 text-xs">
                                Buka Struk
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="ayi-card mt-4 p-6 text-center">
                <p class="font-heading text-base font-bold text-ink">Tidak ada pesanan berstatus Diproses saat ini.</p>
            </div>
        @endif
    </div>
</x-layouts.panel>

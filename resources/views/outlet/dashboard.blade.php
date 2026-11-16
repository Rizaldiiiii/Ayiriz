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
                Buka Kasir Pesanan ({{ $summary['jumlah_diproses'] }} Diproses)
            </a>
            <a href="{{ route('outlet.menus.create') }}" class="ayi-btn-secondary">
                + Tambah Menu
            </a>
        </div>
    </div>

    {{-- KPI Ringkasan Outlet --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="ayi-card p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-[#8A3410]">Pesanan Masuk (Diproses)</span>
            <p class="mt-2 font-heading text-[28px] font-bold text-ink tabular-nums">
                {{ $summary['jumlah_diproses'] }} Pesanan
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Menunggu pelanggan tiba & bayar di kasir
            </p>
        </div>

        <div class="ayi-card p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-leaf">Pesanan Selesai</span>
            <p class="mt-2 font-heading text-[28px] font-bold text-leaf tabular-nums">
                {{ $summary['jumlah_selesai'] }} Pesanan
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Sudah dibayar & diambil di kasir
            </p>
        </div>

        <div class="ayi-card p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-navy">Total Penjualan (Selesai)</span>
            <p class="mt-2 font-heading text-[26px] font-bold text-ink tabular-nums">
                Rp {{ number_format($summary['total_penjualan'], 0, ',', '.') }}
            </p>
            <p class="mt-1 text-xs text-ink-secondary">
                Pesanan Diproses belum dihitung
            </p>
        </div>

        <div class="ayi-card p-5">
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-chili">Royalti Franchise ({{ number_format((float) $outlet->royalty_percent, 0) }}%)</span>
            <p class="mt-2 font-heading text-[26px] font-bold text-chili tabular-nums">
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

    {{-- Daftar Pesanan Masuk (Diproses) --}}
    <div class="mt-8">
        <div class="flex items-center justify-between gap-2">
            <div>
                <h2 class="font-heading text-[24px] font-bold text-ink">
                    ANTREAN KASIR — PESANAN DIPROSES
                </h2>
                <p class="text-xs text-ink-secondary">
                    Masak pesanan saat pelanggan datang dan membayar di kasir, lalu tandai Selesai.
                </p>
            </div>
            <a href="{{ route('outlet.orders.index') }}" class="text-sm font-bold text-chili underline">
                Lihat Semua Pesanan →
            </a>
        </div>

        @if ($pesananDiproses->count() > 0)
            <div class="ayi-card mt-4 hidden overflow-hidden md:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase text-ink">
                        <tr>
                            <th class="px-5 py-3.5">Kode Pesanan</th>
                            <th class="px-5 py-3.5">Pelanggan</th>
                            <th class="px-5 py-3.5">Item Dipesan</th>
                            <th class="px-5 py-3.5">Waktu Masuk</th>
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
                                    {{ $order->user?->name }}
                                </td>
                                <td class="px-5 py-4 text-xs text-ink-secondary">
                                    @foreach ($order->items as $item)
                                        <div>{{ $item->jumlah }}x {{ $item->nama_menu_saat_checkout }}</div>
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
                                        Buka di Kasir
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
                        <p class="mt-1 text-sm font-bold text-ink">Pelanggan: {{ $order->user?->name }}</p>
                        <div class="mt-2 text-xs text-ink-secondary">
                            @foreach ($order->items as $item)
                                <div>{{ $item->jumlah }}x {{ $item->nama_menu_saat_checkout }}</div>
                            @endforeach
                        </div>
                        <div class="mt-3 flex items-center justify-between border-t border-divider pt-3">
                            <span class="font-heading text-lg font-bold text-ink tabular-nums">{{ $order->formattedTotal() }}</span>
                            <a href="{{ route('outlet.orders.show', $order) }}" class="ayi-btn-primary min-h-[38px] px-4 py-1.5 text-xs">
                                Proses Kasir
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="ayi-card mt-4 p-8 text-center">
                <p class="font-heading text-lg font-bold text-ink">Tidak ada antrean pesanan Diproses saat ini.</p>
                <p class="mt-1 text-xs text-ink-secondary">Pesanan online baru dari pelanggan akan langsung muncul di sini.</p>
            </div>
        @endif
    </div>
</x-layouts.panel>

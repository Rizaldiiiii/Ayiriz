<x-layouts.panel :title="'Kasir Pesanan ' . $order->kode">
    <nav aria-label="Breadcrumb" class="mb-4 text-xs font-semibold text-ink-secondary">
        <ol class="flex items-center gap-2">
            <li><a href="{{ route('outlet.orders.index') }}" class="text-navy underline hover:text-chili">Kasir & Pesanan Masuk</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-ink">{{ $order->kode }}</li>
        </ol>
    </nav>

    @php
        $estimasiRoyalti = (int) round($order->total * ((float) $outlet->royalty_percent / 100));
    @endphp

    <div class="grid gap-6 lg:grid-cols-12 lg:items-start" x-data="{ confirmSelesai: false, confirmHapus: false }">
        {{-- Kartu Struk & Rincian Pesanan --}}
        <div class="ayi-card p-6 sm:p-8 lg:col-span-8" data-reveal="up">
            <div class="flex flex-col justify-between gap-4 border-b border-divider pb-5 sm:flex-row sm:items-center">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-heading text-xs font-bold uppercase tracking-wider text-navy">
                            STRUK KASIR — {{ strtoupper($outlet->nama) }}
                        </span>
                        @if ($order->is_sample)
                            <span class="ayi-chip-sample">Data Contoh</span>
                        @endif
                    </div>
                    <h1 class="mt-1 font-heading text-[32px] font-bold text-chili">
                        {{ $order->kode }}
                    </h1>
                    <p class="text-xs text-ink-secondary">
                        Waktu Pesan: {{ $order->created_at?->translatedFormat('d F Y, H:i') }} WITA
                        @if ($order->isSelesai() && $order->selesai_at)
                            · Selesai: {{ $order->selesai_at->translatedFormat('d F Y, H:i') }} WITA
                        @endif
                    </p>
                </div>

                <div class="flex flex-col items-start gap-2 sm:items-end">
                    <x-status-badge :status="$order->status" type="order" />
                    <span class="rounded-full bg-tint-navy px-3 py-1 font-heading text-xs font-bold text-navy">
                        Metode: Bayar di Kasir
                    </span>
                </div>
            </div>

            {{-- Info Pelanggan & Kantin --}}
            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-[12px] bg-surface-cream p-4">
                    <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">Pelanggan Pemesan</span>
                    <p class="mt-1 font-heading text-lg font-bold text-ink">{{ $order->user?->name }}</p>
                    <p class="text-xs text-ink-secondary">{{ $order->user?->email }}</p>
                </div>

                <div class="rounded-[12px] bg-surface-cream p-4">
                    <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">Kantin Mitra</span>
                    <p class="mt-1 font-heading text-lg font-bold text-ink">{{ $outlet->nama }}</p>
                    <p class="text-xs text-ink-secondary">{{ $outlet->alamat }}</p>
                </div>
            </div>

            {{-- Tabel Item Struk --}}
            <div class="mt-6">
                <h2 class="font-heading text-lg font-bold text-ink">
                    RINCIAN ITEM PESANAN
                </h2>

                <div class="mt-3 overflow-hidden rounded-[12px] border border-border-ayi">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase text-ink">
                            <tr>
                                <th class="px-4 py-3">Item Menu</th>
                                <th class="px-4 py-3 text-right">Harga Checkout</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-divider bg-surface">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="px-4 py-3.5 font-semibold text-ink">
                                        {{ $item->nama_menu_saat_checkout }}
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-heading font-bold text-ink-secondary tabular-nums">
                                        {{ $item->formattedHarga() }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-heading font-bold text-ink tabular-nums">
                                        {{ $item->jumlah }}
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-heading font-bold text-ink tabular-nums">
                                        {{ $item->formattedSubtotal() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t border-border-ayi bg-surface-cream">
                            <tr>
                                <td colspan="3" class="px-4 py-3 font-heading text-sm font-bold text-ink">
                                    Total Diterima di Kasir
                                </td>
                                <td class="px-4 py-3 text-right font-heading text-2xl font-bold text-ink tabular-nums">
                                    {{ $order->formattedTotal() }}
                                </td>
                            </tr>
                            <tr class="border-t border-divider">
                                <td colspan="3" class="px-4 py-3 text-xs font-semibold text-ink-secondary">
                                    @if ($order->isSelesai())
                                        Royalti Franchise Ayiriz ({{ number_format((float) $order->royalty_percent_saat_selesai, 0) }}% — Tercatat)
                                    @else
                                        Estimasi Royalti Franchise ({{ number_format((float) $outlet->royalty_percent, 0) }}% — Baru dihitung saat Selesai)
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-heading text-lg font-bold text-chili tabular-nums">
                                    @if ($order->isSelesai())
                                        {{ $order->formattedRoyalty() }}
                                    @else
                                        Rp {{ number_format($estimasiRoyalti, 0, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                            @if ($order->isSelesai())
                                <tr class="border-t border-divider bg-tint-leaf">
                                    <td colspan="3" class="px-4 py-3 font-heading text-sm font-bold text-leaf">
                                        Pendapatan Bersih Outlet (90%)
                                    </td>
                                    <td class="px-4 py-3 text-right font-heading text-lg font-bold text-leaf tabular-nums">
                                        Rp {{ number_format($order->total - $order->royalty_nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Panel Tindakan Kasir (KF-05) --}}
        <aside class="ayi-card p-6 lg:col-span-4">
            <h2 class="font-heading text-xl font-bold text-ink">
                TINDAKAN KASIR
            </h2>

            @if ($order->isDiproses())
                <div class="mt-3 rounded-[12px] bg-tint-tang p-4 text-xs leading-relaxed text-[#8A3410]">
                    <p class="font-heading text-sm font-bold">Status Saat Ini: Diproses</p>
                    <p class="mt-1">
                        Outlet memasak pesanan saat pelanggan datang dan membayar di kasir. Setelah uang diterima dan makanan diserahkan, klik <strong>Tandai Selesai</strong>.
                    </p>
                </div>

                <div class="mt-5 space-y-3">
                    <button
                        type="button"
                        @click="confirmSelesai = true"
                        class="ayi-btn-primary w-full"
                    >
                        Terima Bayar & Tandai Selesai
                    </button>

                    <button
                        type="button"
                        @click="confirmHapus = true"
                        class="w-full rounded-full border border-chili bg-tint-chili px-5 py-3 font-heading text-sm font-bold text-chili hover:bg-chili hover:text-white transition"
                    >
                        Hapus Pesanan (Pelanggan Tidak Datang)
                    </button>
                </div>

                <p class="mt-4 text-xs leading-relaxed text-ink-muted">
                    Apabila pelanggan batal/tidak datang ke kasir, hapus pesanan Diproses ini agar <strong>stok porsi menu kembali</strong> tanpa royalti dan tanpa pengurangan stok bahan.
                </p>
            @else
                <div class="mt-3 rounded-[12px] bg-tint-leaf p-4 text-xs leading-relaxed text-leaf">
                    <p class="font-heading text-sm font-bold">Transaksi Kasir Selesai</p>
                    <p class="mt-1">
                        Pembayaran sebesar <strong>{{ $order->formattedTotal() }}</strong> telah diterima di kasir dan royalti <strong>{{ $order->formattedRoyalty() }}</strong> ({{ number_format((float) $order->royalty_percent_saat_selesai, 0) }}%) telah tercatat.
                    </p>
                </div>

                <p class="mt-3 text-xs text-ink-secondary">
                    Sesuai aturan sistem (KF-05), pesanan yang sudah berstatus <strong>Selesai</strong> bersifat final: tidak dapat dikembalikan ke Diproses dan tidak dapat dihapus.
                </p>

                <button
                    type="button"
                    onclick="window.print()"
                    class="ayi-btn-secondary mt-5 w-full"
                >
                    Cetak / Simpan Struk
                </button>
            @endif

            <a href="{{ route('outlet.orders.index') }}" class="ayi-btn-secondary mt-3 w-full">
                ← Kembali ke Daftar Pesanan
            </a>
        </aside>

        {{-- Modal Konfirmasi Tandai Selesai (Motion-Ayiriz.md §7.1: tidak tertutup saat klik overlay tanpa sengaja) --}}
        @if ($order->isDiproses())
            <div
                x-cloak
                x-show="confirmSelesai"
                @keydown.escape.window="confirmSelesai = false"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-selesai-title"
            >
                <div
                    class="absolute inset-0 bg-[#211D18]/45"
                    x-show="confirmSelesai"
                    x-transition.opacity.duration.200ms
                ></div>

                <div
                    class="relative w-full max-w-md rounded-[16px] border border-border-ayi bg-surface p-6 shadow-ayi-lg"
                    x-show="confirmSelesai"
                    x-transition:enter="transition ease-out duration-250"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                >
                    <h2 id="modal-selesai-title" class="font-heading text-[22px] font-bold text-ink">
                        Tandai Pesanan {{ $order->kode }} Selesai?
                    </h2>

                    <p class="mt-2 text-sm leading-relaxed text-ink-secondary">
                        Pastikan pembayaran sebesar <strong>{{ $order->formattedTotal() }}</strong> sudah diterima di kasir dan pesanan telah diambil pelanggan. Status tidak bisa dikembalikan ke Diproses, dan royalti <strong>Rp {{ number_format($estimasiRoyalti, 0, ',', '.') }}</strong> (10%) akan langsung dicatat.
                    </p>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            @click="confirmSelesai = false"
                            class="ayi-btn-secondary min-h-[42px] px-4 py-2 text-sm"
                        >
                            Batal
                        </button>

                        <form action="{{ route('outlet.orders.complete', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="ayi-btn-primary min-h-[42px] px-5 py-2 text-sm">
                                Ya, Terima Bayar & Selesai
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Konfirmasi Hapus Pesanan Diproses (Pelanggan Tidak Datang) --}}
            <div
                x-cloak
                x-show="confirmHapus"
                @keydown.escape.window="confirmHapus = false"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-hapus-title"
            >
                <div
                    class="absolute inset-0 bg-[#211D18]/45"
                    x-show="confirmHapus"
                    x-transition.opacity.duration.200ms
                ></div>

                <div
                    class="relative w-full max-w-md rounded-[16px] border border-border-ayi bg-surface p-6 shadow-ayi-lg"
                    x-show="confirmHapus"
                    x-transition:enter="transition ease-out duration-250"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-3"
                >
                    <h2 id="modal-hapus-title" class="font-heading text-[22px] font-bold text-chili">
                        Hapus Pesanan {{ $order->kode }}?
                    </h2>

                    <p class="mt-2 text-sm leading-relaxed text-ink-secondary">
                        Gunakan tindakan ini apabila pelanggan tidak datang ke kasir. Karena makanan belum dimasak:
                    </p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-ink-secondary">
                        <li>Seluruh stok porsi menu pada pesanan ini akan <strong>dikembalikan</strong>.</li>
                        <li><strong>Tidak ada royalti</strong> yang dihitung dan tidak ada pengurangan stok bahan.</li>
                    </ul>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            @click="confirmHapus = false"
                            class="ayi-btn-secondary min-h-[42px] px-4 py-2 text-sm"
                        >
                            Batal
                        </button>

                        <form action="{{ route('outlet.orders.destroy', $order) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ayi-btn-primary min-h-[42px] px-5 py-2 text-sm">
                                Ya, Hapus & Kembalikan Stok Porsi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.panel>

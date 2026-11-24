<x-layouts.panel :title="'Antrean ' . $antrean->kode">
    <nav aria-label="Breadcrumb" class="mb-4 text-xs font-semibold text-ink-secondary">
        <ol class="flex items-center gap-2">
            <li><a href="{{ route('outlet.orders.index') }}" class="text-navy underline hover:text-chili">Kasir: Daftar Tunggu & Pesanan</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-ink">Antrean {{ $antrean->kode }}</li>
        </ol>
    </nav>

    @php
        $stokKurang = false;
        foreach ($antrean->items as $item) {
            if (! $item->menu || ! $item->menu->tersedia || $item->menu->stok_porsi < $item->jumlah) {
                $stokKurang = true;
                break;
            }
        }
    @endphp

    <div class="grid gap-6 lg:grid-cols-12 lg:items-start" x-data="{ confirmBayar: false, confirmHapus: false }">
        {{-- Detail Antrean --}}
        <div class="ayi-card p-6 sm:p-8 lg:col-span-8" data-reveal="up">
            <div class="flex flex-col justify-between gap-4 border-b border-divider pb-5 sm:flex-row sm:items-center">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-heading text-xs font-bold uppercase tracking-wider text-navy">
                            DAFTAR TUNGGU KASIR — {{ strtoupper($outlet->nama) }}
                        </span>
                        @if ($antrean->is_sample)
                            <span class="ayi-chip-sample">Data Contoh</span>
                        @endif
                    </div>
                    <h1 class="mt-1 font-heading text-[32px] font-bold text-chili">
                        {{ $antrean->kode }}
                    </h1>
                    <p class="text-xs text-ink-secondary">
                        Waktu Submit Antrean: {{ $antrean->created_at?->translatedFormat('d F Y, H:i') }} WITA
                    </p>
                </div>

                <div class="flex flex-col items-start gap-2 sm:items-end">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-warning-bg px-3.5 py-1 font-heading text-xs font-bold text-warning">
                        <span class="h-2 w-2 rounded-full bg-sunny" aria-hidden="true"></span>
                        <span>Daftar Tunggu (Belum Dibayar)</span>
                    </span>
                    <span class="rounded-full bg-tint-navy px-3 py-1 font-heading text-xs font-bold text-navy">
                        Metode: Bayar Tunai di Kasir
                    </span>
                </div>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-[12px] bg-surface-cream p-4">
                    <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">Nama Pemesan (Tamu)</span>
                    <p class="mt-1 font-heading text-xl font-bold text-ink">{{ $antrean->nama_pemesan }}</p>
                    <p class="text-xs text-ink-secondary">Pelanggan tamu tanpa akun</p>
                </div>

                <div class="rounded-[12px] bg-surface-cream p-4">
                    <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">Kantin Mitra</span>
                    <p class="mt-1 font-heading text-lg font-bold text-ink">{{ $outlet->nama }}</p>
                    <p class="text-xs text-ink-secondary">{{ $outlet->alamat }}</p>
                </div>
            </div>

            @if ($stokKurang)
                <div class="mt-5 rounded-[12px] border border-chili bg-error-bg p-4 text-xs leading-relaxed text-error">
                    <p class="font-heading text-sm font-bold">Stok Porsi Tidak Mencukupi untuk Konfirmasi Pembayaran</p>
                    <p class="mt-1">
                        Salah satu menu pada antrean ini sudah habis atau sisa stok porsinya kurang dari jumlah yang diminta di antara waktu submit dan waktu bayar. Sesuai aturan KF-05, antrean ini tidak dapat dikonfirmasi menjadi pesanan kecuali stok porsi mencukupi.
                    </p>
                </div>
            @endif

            {{-- Tabel Item Antrean --}}
            <div class="mt-6">
                <h2 class="font-heading text-lg font-bold text-ink">
                    RINCIAN ITEM ANTREAN (HARGA TERKUNCI SAAT SUBMIT)
                </h2>

                <div class="mt-3 overflow-hidden rounded-[12px] border border-border-ayi">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase text-ink">
                            <tr>
                                <th class="px-4 py-3">Item Menu</th>
                                <th class="px-4 py-3 text-center">Stok Saat Ini</th>
                                <th class="px-4 py-3 text-right">Harga Submit</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-divider bg-surface">
                            @foreach ($antrean->items as $item)
                                @php
                                    $sisaStok = $item->menu?->stok_porsi ?? 0;
                                    $cukup = $item->menu && $item->menu->tersedia && $sisaStok >= $item->jumlah;
                                @endphp
                                <tr>
                                    <td class="px-4 py-3.5 font-semibold text-ink">
                                        {{ $item->nama_menu_saat_submit }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-heading text-xs font-bold tabular-nums {{ $cukup ? 'text-leaf' : 'text-error' }}">
                                        {{ $sisaStok }} porsi
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
                                <td colspan="4" class="px-4 py-3.5 font-heading text-base font-bold text-ink">
                                    Total Tagihan Tunai di Kasir
                                </td>
                                <td class="px-4 py-3.5 text-right font-heading text-2xl font-bold text-chili tabular-nums">
                                    {{ $antrean->formattedTotal() }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Panel Tindakan Kasir pada Antrean --}}
        <aside class="ayi-card p-6 lg:col-span-4">
            <h2 class="font-heading text-xl font-bold text-ink">
                KONFIRMASI PEMBAYARAN TUNAI
            </h2>

            <div class="mt-3 rounded-[12px] bg-surface-cream p-4 text-xs leading-relaxed text-ink-secondary">
                <p class="font-heading text-sm font-bold text-ink">Status: Daftar Tunggu (Belum Dibayar)</p>
                <p class="mt-1">
                    Periksa nama pemesan (<strong>{{ $antrean->nama_pemesan }}</strong>) dan terima uang tunai sebesar <strong>{{ $antrean->formattedTotal() }}</strong>. Saat Anda menekan tombol konfirmasi di bawah:
                </p>
                <ul class="mt-1.5 list-disc space-y-1 pl-4">
                    <li>Antrean berubah menjadi pesanan berstatus <strong>Diproses</strong> dengan kode <strong>{{ $antrean->kode }}</strong>.</li>
                    <li><strong>Stok porsi menu berkurang</strong> sesuai jumlah item.</li>
                    <li>Layar kasir menampilkan <strong>QR Reaksi</strong> pesanan.</li>
                </ul>
            </div>

            <div class="mt-5 space-y-3">
                <button
                    type="button"
                    @click="confirmBayar = true"
                    @if ($stokKurang) disabled @endif
                    class="ayi-btn-primary w-full"
                >
                    Konfirmasi Bayar Tunai ({{ $antrean->formattedTotal() }})
                </button>

                <button
                    type="button"
                    @click="confirmHapus = true"
                    class="w-full rounded-full border border-chili bg-tint-chili px-5 py-3 font-heading text-sm font-bold text-chili hover:bg-chili hover:text-white transition"
                >
                    Hapus Antrean (Pelanggan Tidak Datang)
                </button>
            </div>

            <a href="{{ route('outlet.orders.index') }}" class="ayi-btn-secondary mt-4 w-full">
                ← Kembali ke Daftar Kasir
            </a>
        </aside>

        {{-- Modal Konfirmasi Bayar Tunai --}}
        <div
            x-cloak
            x-show="confirmBayar"
            @keydown.escape.window="confirmBayar = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-bayar-title"
        >
            <div class="absolute inset-0 bg-[#211D18]/45" x-show="confirmBayar" x-transition.opacity.duration.200ms></div>

            <div
                class="relative w-full max-w-md rounded-[16px] border border-border-ayi bg-surface p-6 shadow-ayi-lg"
                x-show="confirmBayar"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            >
                <h2 id="modal-bayar-title" class="font-heading text-[22px] font-bold text-ink">
                    Konfirmasi Pembayaran Tunai {{ $antrean->kode }}?
                </h2>

                <p class="mt-2 text-sm leading-relaxed text-ink-secondary">
                    Pastikan uang tunai sebesar <strong>{{ $antrean->formattedTotal() }}</strong> dari <strong>{{ $antrean->nama_pemesan }}</strong> sudah diterima di kasir. Antrean akan menjadi pesanan <strong>Diproses</strong>, stok porsi menu akan dikurangi, dan pesanan yang sudah dibayar tidak dapat dihapus.
                </p>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="confirmBayar = false" class="ayi-btn-secondary min-h-[42px] px-4 py-2 text-sm">
                        Batal
                    </button>

                    <form action="{{ route('outlet.antrean.confirm', $antrean) }}" method="POST">
                        @csrf
                        <button type="submit" class="ayi-btn-primary min-h-[42px] px-5 py-2 text-sm">
                            Ya, Terima Tunai & Proses Pesanan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Konfirmasi Hapus Antrean --}}
        <div
            x-cloak
            x-show="confirmHapus"
            @keydown.escape.window="confirmHapus = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-hapus-antrean-title"
        >
            <div class="absolute inset-0 bg-[#211D18]/45" x-show="confirmHapus" x-transition.opacity.duration.200ms></div>

            <div
                class="relative w-full max-w-md rounded-[16px] border border-border-ayi bg-surface p-6 shadow-ayi-lg"
                x-show="confirmHapus"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95 translate-y-3"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            >
                <h2 id="modal-hapus-antrean-title" class="font-heading text-[22px] font-bold text-chili">
                    Hapus Antrean {{ $antrean->kode }}?
                </h2>

                <p class="mt-2 text-sm leading-relaxed text-ink-secondary">
                    Hapus antrean atas nama <strong>{{ $antrean->nama_pemesan }}</strong> dari daftar tunggu bila pelanggan tidak datang. Karena antrean belum mengurangi stok porsi, penghapusan ini <strong>tidak mengubah stok maupun royalti</strong>.
                </p>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="confirmHapus = false" class="ayi-btn-secondary min-h-[42px] px-4 py-2 text-sm">
                        Batal
                    </button>

                    <form action="{{ route('outlet.antrean.destroy', $antrean) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ayi-btn-primary min-h-[42px] px-5 py-2 text-sm">
                            Ya, Hapus dari Daftar Tunggu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.panel>

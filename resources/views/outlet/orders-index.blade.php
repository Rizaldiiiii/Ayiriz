<x-layouts.panel :title="'Kasir & Pesanan ' . $outlet->nama">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal="up">
        <div>
            <div class="ayi-accent-bar mb-2 w-12"></div>
            <h1 class="font-heading text-[32px] font-bold text-ink">
                KASIR: DAFTAR TUNGGU & PESANAN (KF-05)
            </h1>
            <p class="text-sm text-ink-secondary">
                Konfirmasi pembayaran tunai dari daftar tunggu (antrean → pesanan <strong>Diproses</strong> + kurangi stok porsi), lalu tandai <strong>Selesai</strong> saat pesanan diambil.
            </p>
        </div>
    </div>

    {{-- BAGIAN 1: DAFTAR TUNGGU (ANTREAN BELUM DIBAYAR) --}}
    <section class="mt-8" aria-labelledby="heading-daftar-tunggu">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 id="heading-daftar-tunggu" class="font-heading text-[24px] font-bold text-ink">
                    1. DAFTAR TUNGGU (ANTREAN BELUM DIBAYAR · {{ $counts['antrean'] }})
                </h2>
                <p class="text-xs text-ink-secondary">
                    Antrean belum mengurangi stok porsi dan belum memiliki status pesanan. Hapus antrean bila pelanggan tidak datang (tanpa efek stok/royalti).
                </p>
            </div>
        </div>

        @if ($antreans->count() > 0)
            {{-- Tabel Desktop Antrean --}}
            <div class="ayi-card mt-4 hidden overflow-hidden md:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase tracking-wider text-ink">
                        <tr>
                            <th class="px-5 py-3.5">Kode Antrean</th>
                            <th class="px-5 py-3.5">Nama Pemesan</th>
                            <th class="px-5 py-3.5">Rincian Item</th>
                            <th class="px-5 py-3.5">Waktu Submit</th>
                            <th class="px-5 py-3.5 text-right">Total Bayar Tunai</th>
                            <th class="px-5 py-3.5 text-right">Aksi Kasir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach ($antreans as $antrean)
                            <tr class="hover:bg-surface-cream">
                                <td class="px-5 py-4">
                                    <a href="{{ route('outlet.antrean.show', $antrean) }}" class="font-heading text-base font-bold text-chili underline decoration-chili/40">
                                        {{ $antrean->kode }}
                                    </a>
                                    @if ($antrean->is_sample)
                                        <div class="mt-1">
                                            <span class="ayi-chip-sample">Data Contoh</span>
                                        </div>
                                    @endif
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
                                <td class="px-5 py-4 text-right font-heading text-base font-bold text-ink tabular-nums">
                                    {{ $antrean->formattedTotal() }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <a href="{{ route('outlet.antrean.show', $antrean) }}" class="ayi-btn-primary min-h-[36px] px-3.5 py-1.5 text-xs">
                                            Terima Bayar Tunai
                                        </a>

                                        <form
                                            action="{{ route('outlet.antrean.destroy', $antrean) }}"
                                            method="POST"
                                            onsubmit="return confirm('Hapus antrean {{ $antrean->kode }} ({{ addslashes($antrean->nama_pemesan) }}) karena pelanggan tidak datang?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="rounded-full bg-tint-chili px-3 py-1.5 font-heading text-xs font-bold text-chili hover:bg-chili hover:text-white transition"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Kartu Mobile Antrean --}}
            <div class="mt-4 space-y-4 md:hidden">
                @foreach ($antreans as $antrean)
                    <article class="ayi-card p-5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-heading text-lg font-bold text-chili">{{ $antrean->kode }}</span>
                                <p class="text-sm font-bold text-ink">{{ $antrean->nama_pemesan }}</p>
                                <p class="text-xs text-ink-muted">{{ $antrean->created_at?->translatedFormat('d M Y, H:i') }} WITA</p>
                            </div>
                            <span class="rounded-full bg-warning-bg px-3 py-1 font-heading text-xs font-bold text-warning">
                                Daftar Tunggu
                            </span>
                        </div>

                        <div class="mt-3 rounded-[10px] bg-surface-cream p-3 text-xs text-ink-secondary">
                            @foreach ($antrean->items as $item)
                                <div class="flex justify-between py-0.5">
                                    <span>{{ $item->jumlah }}x {{ $item->nama_menu_saat_submit }}</span>
                                    <span class="font-semibold text-ink tabular-nums">{{ $item->formattedSubtotal() }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-divider pt-3">
                            <div>
                                <span class="block text-[11px] uppercase text-ink-muted">Total Bayar Tunai</span>
                                <span class="font-heading text-xl font-bold text-ink tabular-nums">{{ $antrean->formattedTotal() }}</span>
                            </div>
                            <a href="{{ route('outlet.antrean.show', $antrean) }}" class="ayi-btn-primary min-h-[38px] px-4 py-1.5 text-xs">
                                Terima Bayar Tunai
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="ayi-card mt-4 p-6 text-center">
                <p class="font-heading text-base font-bold text-ink">Daftar tunggu (antrean) kosong.</p>
                <p class="mt-0.5 text-xs text-ink-secondary">Antrean baru dari pelanggan tamu akan tampil di sini sebelum dibayar.</p>
            </div>
        @endif
    </section>

    {{-- BAGIAN 2: PESANAN YANG SUDAH DIBAYAR (DIPROSES & SELESAI) --}}
    <section class="mt-10" aria-labelledby="heading-pesanan-dibayar">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 id="heading-pesanan-dibayar" class="font-heading text-[24px] font-bold text-ink">
                    2. PESANAN SUDAH DIBAYAR (DIPROSES & SELESAI)
                </h2>
                <p class="text-xs text-ink-secondary">
                    Pesanan yang sudah dikonfirmasi pembayarannya tidak dapat dihapus. Tandai Selesai saat diambil pelanggan.
                </p>
            </div>
        </div>

        {{-- Tab Filter Status (Minimalism §5.9) --}}
        <div class="mt-4 flex flex-wrap gap-2 border-b border-divider pb-3">
            <a
                href="{{ route('outlet.orders.index') }}"
                class="rounded-full px-4 py-2 font-heading text-sm font-bold transition {{ ! $statusFilter ? 'bg-tint-chili text-chili border-b-2 border-chili' : 'bg-surface text-ink-secondary hover:text-ink' }}"
            >
                Semua Pesanan ({{ $counts['semua'] }})
            </a>
            <a
                href="{{ route('outlet.orders.index', ['status' => 'diproses']) }}"
                class="rounded-full px-4 py-2 font-heading text-sm font-bold transition {{ $statusFilter === 'diproses' ? 'bg-tint-tang text-[#8A3410] border-b-2 border-tang' : 'bg-surface text-ink-secondary hover:text-ink' }}"
            >
                Diproses / Sedang Dimasak ({{ $counts['diproses'] }})
            </a>
            <a
                href="{{ route('outlet.orders.index', ['status' => 'selesai']) }}"
                class="rounded-full px-4 py-2 font-heading text-sm font-bold transition {{ $statusFilter === 'selesai' ? 'bg-tint-leaf text-leaf border-b-2 border-leaf' : 'bg-surface text-ink-secondary hover:text-ink' }}"
            >
                Selesai ({{ $counts['selesai'] }})
            </a>
        </div>

        @if ($orders->count() > 0)
            {{-- Tabel Desktop --}}
            <div class="ayi-card mt-5 hidden overflow-hidden md:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase tracking-wider text-ink">
                        <tr>
                            <th class="px-5 py-3.5">Kode Pesanan</th>
                            <th class="px-5 py-3.5">Nama Pemesan</th>
                            <th class="px-5 py-3.5">Rincian Item</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Total Bayar</th>
                            <th class="px-5 py-3.5 text-right">Royalti (10%)</th>
                            <th class="px-5 py-3.5 text-right">Aksi Kasir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-surface-cream">
                                <td class="px-5 py-4">
                                    <a href="{{ route('outlet.orders.show', $order) }}" class="font-heading text-base font-bold text-chili underline decoration-chili/40">
                                        {{ $order->kode }}
                                    </a>
                                    <p class="text-xs text-ink-muted">{{ $order->created_at?->translatedFormat('d M Y, H:i') }} WITA</p>
                                </td>
                                <td class="px-5 py-4 font-semibold text-ink">
                                    {{ $order->nama_pemesan }}
                                </td>
                                <td class="px-5 py-4 text-xs text-ink-secondary">
                                    @foreach ($order->items as $item)
                                        <div>{{ $item->jumlah }}x {{ $item->nama_menu_saat_submit }}</div>
                                    @endforeach
                                </td>
                                <td class="px-5 py-4">
                                    <x-status-badge :status="$order->status" type="order" />
                                </td>
                                <td class="px-5 py-4 text-right font-heading text-base font-bold text-ink tabular-nums">
                                    {{ $order->formattedTotal() }}
                                </td>
                                <td class="px-5 py-4 text-right font-heading text-sm font-bold tabular-nums {{ $order->isSelesai() ? 'text-chili' : 'text-ink-muted' }}">
                                    @if ($order->isSelesai())
                                        {{ $order->formattedRoyalty() }}
                                    @else
                                        Rp 0 <span class="block text-[11px] font-normal">(Belum Selesai)</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('outlet.orders.show', $order) }}" class="ayi-btn-primary min-h-[36px] px-3.5 py-1.5 text-xs">
                                        {{ $order->isDiproses() ? 'Tandai Selesai / Struk' : 'Lihat Struk' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Kartu Mobile --}}
            <div class="mt-5 space-y-4 md:hidden">
                @foreach ($orders as $order)
                    <article class="ayi-card p-5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-heading text-lg font-bold text-chili">{{ $order->kode }}</span>
                                <p class="text-sm font-bold text-ink">{{ $order->nama_pemesan }}</p>
                                <p class="text-xs text-ink-muted">{{ $order->created_at?->translatedFormat('d M Y, H:i') }} WITA</p>
                            </div>
                            <x-status-badge :status="$order->status" type="order" />
                        </div>

                        <div class="mt-3 rounded-[10px] bg-surface-cream p-3 text-xs text-ink-secondary">
                            @foreach ($order->items as $item)
                                <div class="flex justify-between py-0.5">
                                    <span>{{ $item->jumlah }}x {{ $item->nama_menu_saat_submit }}</span>
                                    <span class="font-semibold text-ink tabular-nums">{{ $item->formattedSubtotal() }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-divider pt-3">
                            <div>
                                <span class="block text-[11px] uppercase text-ink-muted">Total Kasir</span>
                                <span class="font-heading text-xl font-bold text-ink tabular-nums">{{ $order->formattedTotal() }}</span>
                            </div>
                            <a href="{{ route('outlet.orders.show', $order) }}" class="ayi-btn-primary min-h-[38px] px-4 py-1.5 text-xs">
                                {{ $order->isDiproses() ? 'Tandai Selesai / Struk' : 'Lihat Struk' }}
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @else
            <div class="ayi-card mt-5 p-10 text-center">
                <h3 class="font-heading text-xl font-bold text-ink">Belum Ada Pesanan pada Kategori Ini</h3>
                <p class="mt-1 text-sm text-ink-secondary">Pesanan yang sudah dibayar tunai di {{ $outlet->nama }} akan tampil di sini.</p>
            </div>
        @endif
    </section>
</x-layouts.panel>

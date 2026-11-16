<x-layouts.panel :title="'Kasir & Pesanan ' . $outlet->nama">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal="up">
        <div>
            <div class="ayi-accent-bar mb-2 w-12"></div>
            <h1 class="font-heading text-[32px] font-bold text-ink">
                KASIR & PESANAN MASUK (KF-05)
            </h1>
            <p class="text-sm text-ink-secondary">
                Terima pembayaran tunai/QRIS manual di kasir saat pelanggan datang, masak pesanan, lalu ubah status <strong>Diproses → Selesai</strong>.
            </p>
        </div>
    </div>

    {{-- Tab Filter Status (Minimalism §5.9) --}}
    <div class="mt-6 flex flex-wrap gap-2 border-b border-divider pb-3">
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
            Diproses / Antrean Kasir ({{ $counts['diproses'] }})
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
                        <th class="px-5 py-3.5">Pelanggan</th>
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
                                {{ $order->user?->name }}
                            </td>
                            <td class="px-5 py-4 text-xs text-ink-secondary">
                                @foreach ($order->items as $item)
                                    <div>{{ $item->jumlah }}x {{ $item->nama_menu_saat_checkout }}</div>
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
                                    {{ $order->isDiproses() ? 'Terima Bayar / Detail' : 'Lihat Struk' }}
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
                            <p class="text-sm font-bold text-ink">{{ $order->user?->name }}</p>
                            <p class="text-xs text-ink-muted">{{ $order->created_at?->translatedFormat('d M Y, H:i') }} WITA</p>
                        </div>
                        <x-status-badge :status="$order->status" type="order" />
                    </div>

                    <div class="mt-3 rounded-[10px] bg-surface-cream p-3 text-xs text-ink-secondary">
                        @foreach ($order->items as $item)
                            <div class="flex justify-between py-0.5">
                                <span>{{ $item->jumlah }}x {{ $item->nama_menu_saat_checkout }}</span>
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
                            {{ $order->isDiproses() ? 'Proses di Kasir' : 'Lihat Struk' }}
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
            <h2 class="font-heading text-xl font-bold text-ink">Belum Ada Pesanan pada Kategori Ini</h2>
            <p class="mt-1 text-sm text-ink-secondary">Pesanan pelanggan yang masuk ke {{ $outlet->nama }} akan tampil di sini.</p>
        </div>
    @endif
</x-layouts.panel>

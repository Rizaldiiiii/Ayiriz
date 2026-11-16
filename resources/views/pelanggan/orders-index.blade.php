<x-layouts.public title="Riwayat Pesanan Saya">
    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal="up">
            <div>
                <div class="ayi-accent-bar mb-2 w-12"></div>
                <h1 class="font-heading text-[32px] font-bold text-ink">
                    RIWAYAT PESANAN SAYA
                </h1>
                <p class="text-sm text-ink-secondary">
                    Pantau status pesanan Anda. Tunjukkan kode pesanan saat tiba dan membayar di kasir kantin.
                </p>
            </div>

            <a href="{{ route('catalog.index') }}" class="ayi-btn-primary">
                + Buat Pesanan Baru
            </a>
        </div>

        @if ($orders->count() > 0)
            {{-- Tabel untuk Desktop (Minimalism §5.6) --}}
            <div class="ayi-card mt-6 hidden overflow-hidden md:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase tracking-wider text-ink">
                        <tr>
                            <th class="px-5 py-3.5">Kode Pesanan</th>
                            <th class="px-5 py-3.5">Kantin Mitra</th>
                            <th class="px-5 py-3.5">Rincian Menu</th>
                            <th class="px-5 py-3.5">Waktu Pesan</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Total Bayar</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach ($orders as $order)
                            <tr class="min-h-[54px] hover:bg-surface-cream">
                                <td class="px-5 py-4 font-heading text-base font-bold text-ink">
                                    <a href="{{ route('pelanggan.orders.show', $order->kode) }}" class="text-chili underline decoration-chili/40 hover:text-chili-hover">
                                        {{ $order->kode }}
                                    </a>
                                    @if ($order->is_sample)
                                        <div class="mt-1">
                                            <span class="ayi-chip-sample">Data Contoh</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 font-semibold text-ink">
                                    {{ $order->outlet?->nama ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-xs text-ink-secondary">
                                    @foreach ($order->items as $item)
                                        <div>{{ $item->jumlah }}x {{ $item->nama_menu_saat_checkout }}</div>
                                    @endforeach
                                </td>
                                <td class="px-5 py-4 text-xs text-ink-secondary">
                                    {{ $order->created_at?->translatedFormat('d M Y, H:i') }} WITA
                                </td>
                                <td class="px-5 py-4">
                                    <x-status-badge :status="$order->status" type="order" />
                                </td>
                                <td class="px-5 py-4 text-right font-heading text-lg font-bold text-chili tabular-nums">
                                    {{ $order->formattedTotal() }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a
                                        href="{{ route('pelanggan.orders.show', $order->kode) }}"
                                        class="ayi-btn-secondary min-h-[38px] px-4 py-1.5 text-xs"
                                    >
                                        Detail & Struk
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Kartu untuk Mobile (Minimalism §5.6 & §12: 1 baris = 1 kartu) --}}
            <div class="mt-6 space-y-4 md:hidden">
                @foreach ($orders as $order)
                    <article class="ayi-card p-5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="font-heading text-lg font-bold text-chili">{{ $order->kode }}</span>
                                <p class="text-sm font-bold text-ink">{{ $order->outlet?->nama }}</p>
                                <p class="text-xs text-ink-secondary">{{ $order->created_at?->translatedFormat('d M Y, H:i') }} WITA</p>
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
                                <span class="block text-[11px] uppercase text-ink-muted">Total Bayar di Kasir</span>
                                <span class="font-heading text-xl font-bold text-chili tabular-nums">{{ $order->formattedTotal() }}</span>
                            </div>
                            <a href="{{ route('pelanggan.orders.show', $order->kode) }}" class="ayi-btn-secondary min-h-[40px] px-4 py-2 text-xs">
                                Buka Detail →
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @else
            <div class="ayi-card mt-6 p-10 text-center">
                <h2 class="font-heading text-xl font-bold text-ink">Belum Ada Riwayat Pesanan</h2>
                <p class="mt-1 text-sm text-ink-secondary">
                    Anda belum pernah membuat pesanan. Pilih kantin mitra di katalog dan mulai pesan sekarang!
                </p>
                <a href="{{ route('catalog.index') }}" class="ayi-btn-primary mt-5">
                    Jelajahi Katalog Menu
                </a>
            </div>
        @endif
    </section>
</x-layouts.public>

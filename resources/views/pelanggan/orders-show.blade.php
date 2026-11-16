<x-layouts.public :title="'Pesanan ' . $order->kode">
    <section class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <nav aria-label="Breadcrumb" class="mb-4 text-xs font-semibold text-ink-secondary">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('pelanggan.orders.index') }}" class="text-navy underline hover:text-chili">Pesanan Saya</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-ink">{{ $order->kode }}</li>
            </ol>
        </nav>

        <div class="ayi-card p-6 sm:p-8" data-reveal="up">
            {{-- Header Kode & Status --}}
            <div class="flex flex-col justify-between gap-4 border-b border-divider pb-5 sm:flex-row sm:items-center">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-ink-muted">Kode Pesanan</span>
                        @if ($order->is_sample)
                            <span class="ayi-chip-sample">Data Contoh</span>
                        @endif
                    </div>
                    <h1 class="mt-1 font-heading text-[32px] font-bold text-chili">
                        {{ $order->kode }}
                    </h1>
                    <p class="text-xs text-ink-secondary">
                        Dibuat pada {{ $order->created_at?->translatedFormat('d F Y, H:i') }} WITA
                        @if ($order->isSelesai() && $order->selesai_at)
                            · Selesai pada {{ $order->selesai_at->translatedFormat('d F Y, H:i') }} WITA
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

            {{-- Instruksi Status --}}
            @if ($order->isDiproses())
                <div class="mt-5 rounded-[12px] border border-border-ayi bg-tint-tang p-4 text-sm text-[#8A3410]">
                    <p class="font-heading text-base font-bold">
                        Silakan Datang & Bayar di Kasir {{ $order->outlet?->nama }}
                    </p>
                    <p class="mt-1 text-xs leading-relaxed">
                        Tunjukkan kode pesanan <strong>{{ $order->kode }}</strong> kepada kasir di <strong>{{ $order->outlet?->alamat }}</strong>. Kantin memasak pesanan Anda saat pembayaran diterima di kasir, lalu menandai pesanan menjadi <strong>Selesai</strong>.
                    </p>
                </div>
            @else
                <div class="mt-5 rounded-[12px] border border-border-ayi bg-tint-leaf p-4 text-sm text-leaf">
                    <p class="font-heading text-base font-bold">
                        Pesanan Telah Selesai & Dibayar di Kasir
                    </p>
                    <p class="mt-1 text-xs leading-relaxed">
                        Terima kasih telah memesan di <strong>{{ $order->outlet?->nama }}</strong>! Struk pembayaran Anda tercatat di bawah ini.
                    </p>
                </div>
            @endif

            {{-- Info Kantin & Pelanggan --}}
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-[12px] bg-surface-cream p-4">
                    <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">Kantin Mitra</span>
                    <p class="mt-1 font-heading text-lg font-bold text-ink">{{ $order->outlet?->nama }}</p>
                    <p class="text-xs text-ink-secondary">{{ $order->outlet?->alamat }}</p>
                    <p class="mt-1 text-xs text-ink-secondary">Jam Buka: {{ $order->outlet?->jam_buka }} · HP: {{ $order->outlet?->no_hp }}</p>
                </div>

                <div class="rounded-[12px] bg-surface-cream p-4">
                    <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">Pemesan</span>
                    <p class="mt-1 font-heading text-lg font-bold text-ink">{{ $order->user?->name }}</p>
                    <p class="text-xs text-ink-secondary">{{ $order->user?->email }}</p>
                    <p class="mt-1 text-xs font-semibold text-navy">Pembayaran: Tunai / QRIS Manual di Kasir</p>
                </div>
            </div>

            {{-- Rincian Item (Harga Dikunci Saat Checkout) --}}
            <div class="mt-6">
                <h2 class="font-heading text-[21px] font-bold text-ink">
                    RINCIAN ITEM PESANAN (HARGA TERKUNCI SAAT CHECKOUT)
                </h2>

                <div class="mt-3 overflow-hidden rounded-[12px] border border-border-ayi">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase text-ink">
                            <tr>
                                <th class="px-4 py-3">Nama Menu Saat Checkout</th>
                                <th class="px-4 py-3 text-right">Harga Satuan</th>
                                <th class="px-4 py-3 text-center">Jumlah</th>
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
                                <td colspan="3" class="px-4 py-4 font-heading text-base font-bold text-ink">
                                    Total Pembayaran di Kasir
                                </td>
                                <td class="px-4 py-4 text-right font-heading text-2xl font-bold text-chili tabular-nums">
                                    {{ $order->formattedTotal() }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Panel Reaksi Emote (Stub KF-07 Tahap 2) --}}
            <div class="mt-6 rounded-[12px] border border-border-ayi bg-surface-cream p-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="font-heading text-lg font-bold text-navy">
                        Reaksi Emote per Menu (KF-07)
                    </h3>
                    <span class="rounded-full bg-divider px-3 py-0.5 font-heading text-xs font-bold text-ink-secondary">
                        Belum Tersedia di Tahap Ini (Tahap 2)
                    </span>
                </div>
                <p class="mt-1 text-xs leading-relaxed text-ink-secondary">
                    Pilihan reaksi emote (<strong>😋 Enak</strong>, <strong>🥵 Pedas</strong>, <strong>🫠 Manis / menyejukkan</strong>) untuk pesanan yang sudah Selesai dijadwalkan pada <strong>Tahap 2 — Operasional Outlet</strong>.
                </p>
            </div>

            <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('pelanggan.orders.index') }}" class="ayi-btn-secondary">
                    ← Kembali ke Riwayat Pesanan
                </a>
                @if ($order->outlet)
                    <a href="{{ route('catalog.outlet', $order->outlet->slug) }}" class="ayi-btn-primary">
                        Pesan Lagi di {{ $order->outlet->nama }}
                    </a>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>

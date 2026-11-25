<x-layouts.public :title="'Reaksi Pesanan ' . $kode">
    <section class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <nav aria-label="Breadcrumb" class="mb-4 text-xs font-semibold text-ink-secondary">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('catalog.index') }}" class="text-navy underline hover:text-chili">Katalog</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('catalog.reaksi.lookup') }}" class="text-navy underline hover:text-chili">Beri Reaksi</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-ink">{{ $kode }}</li>
            </ol>
        </nav>

        @if ($order)
            {{-- Pesanan Sudah Dibayar (Diproses atau Selesai) -> Boleh Beri Reaksi --}}
            <div class="ayi-card p-6 sm:p-8" data-reveal="up">
                <div class="flex flex-col justify-between gap-4 border-b border-divider pb-5 sm:flex-row sm:items-center">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-tint-navy px-3 py-1 font-heading text-xs font-bold text-navy">
                                KF-07 · Reaksi Emote Pelanggan
                            </span>
                            @if ($order->is_sample)
                                <span class="ayi-chip-sample">Data Contoh</span>
                            @endif
                        </div>
                        <h1 class="mt-2 font-heading text-[32px] font-bold text-chili">
                            Pesanan {{ $order->kode }}
                        </h1>
                        <p class="text-sm text-ink-secondary">
                            Pemesan: <strong>{{ $order->nama_pemesan }}</strong> · Kantin: <strong>{{ $order->outlet?->nama }}</strong>
                        </p>
                    </div>

                    <div class="flex flex-col items-start gap-2 sm:items-end">
                        <x-status-badge :status="$order->status" type="order" />
                        <span class="text-xs font-semibold text-leaf">
                            Sudah Dibayar Tunai di Kasir
                        </span>
                    </div>
                </div>

                {{-- Panduan Arti Emote (PRD §2: Arti ini tampil di samping pilihan emote) --}}
                <div class="mt-5 rounded-[12px] bg-surface-cream p-4 text-xs leading-relaxed text-ink-secondary">
                    <p class="font-heading text-sm font-bold text-ink">
                        Pilih 1 Reaksi Emote untuk Tiap Menu dalam Pesanan Ini:
                    </p>
                    <p class="mt-0.5">
                        <strong>😋 = Enak</strong> · <strong>🥵 = Pedas</strong> · <strong>🫠 = Manis / menyejukkan</strong>.
                        Satu pesanan memiliki maksimal satu reaksi per menu. Jika Anda memilih emote baru pada menu yang sama, pilihan baru akan <strong>menggantikan</strong> reaksi sebelumnya.
                    </p>
                </div>

                {{-- Daftar Menu dalam Pesanan Ini --}}
                <div class="mt-6 space-y-4">
                    @foreach ($order->items as $item)
                        @php
                            $currentEmote = $reactionsByMenu[(int) $item->menu_id] ?? null;
                            $menuCounts = $item->menu ? $item->menu->reactionCounts() : ['enak' => 0, 'pedas' => 0, 'manis' => 0];
                        @endphp
                        <article class="rounded-[16px] border border-border-ayi bg-surface p-5 shadow-ayi-xs">
                            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                                <div>
                                    <h2 class="font-heading text-[22px] font-bold text-ink">
                                        {{ $item->nama_menu_saat_submit }}
                                    </h2>
                                    <p class="text-xs text-ink-secondary">
                                        Dipesan: {{ $item->jumlah }} porsi · {{ $item->formattedHarga() }} / porsi
                                    </p>

                                    {{-- Total Hitungan Emote Menu Ini (Tanpa Skor / Rata-rata / Ranking) --}}
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-ink-secondary">
                                        <span class="font-semibold text-ink-muted">Total kesan menu ini:</span>
                                        <span class="rounded-full bg-tint-chili px-2.5 py-0.5 font-medium text-ink">
                                            😋 {{ $menuCounts['enak'] }}
                                        </span>
                                        <span class="rounded-full bg-tint-tang px-2.5 py-0.5 font-medium text-ink">
                                            🥵 {{ $menuCounts['pedas'] }}
                                        </span>
                                        <span class="rounded-full bg-tint-sunny px-2.5 py-0.5 font-medium text-ink">
                                            🫠 {{ $menuCounts['manis'] }}
                                        </span>
                                    </div>
                                </div>

                                @if ($currentEmote)
                                    <div class="rounded-full bg-tint-leaf px-3.5 py-1.5 font-heading text-xs font-bold text-leaf">
                                        Reaksi Anda: {{ \App\Enums\ReactionEmote::from($currentEmote)->fullLabel() }}
                                    </div>
                                @endif
                            </div>

                            {{-- Tombol Pilihan 3 Emote dengan Arti Tertulis di Sampingnya --}}
                            <div class="mt-4 grid gap-3 border-t border-divider pt-4 sm:grid-cols-3">
                                @foreach (\App\Enums\ReactionEmote::cases() as $emoteCase)
                                    @php
                                        $isSelected = $currentEmote === $emoteCase->value;
                                        $bgClass = match ($emoteCase) {
                                            \App\Enums\ReactionEmote::Enak => $isSelected ? 'border-2 border-chili bg-tint-chili text-chili' : 'border border-border-ayi bg-surface hover:bg-tint-chili text-ink',
                                            \App\Enums\ReactionEmote::Pedas => $isSelected ? 'border-2 border-tang bg-tint-tang text-[#8A3410]' : 'border border-border-ayi bg-surface hover:bg-tint-tang text-ink',
                                            \App\Enums\ReactionEmote::Manis => $isSelected ? 'border-2 border-warning bg-tint-sunny text-warning' : 'border border-border-ayi bg-surface hover:bg-tint-sunny text-ink',
                                        };
                                    @endphp
                                    <form action="{{ route('catalog.reaksi.store', $order->kode) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="menu_id" value="{{ $item->menu_id }}">
                                        <input type="hidden" name="emote" value="{{ $emoteCase->value }}">
                                        <button
                                            type="submit"
                                            data-emote-trigger
                                            aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                            class="flex min-h-[48px] w-full items-center justify-center gap-2.5 rounded-full px-4 py-2.5 font-heading text-sm font-bold shadow-ayi-xs transition active:scale-[0.97] {{ $bgClass }}"
                                        >
                                            <span class="text-xl" aria-hidden="true">{{ $emoteCase->emoji() }}</span>
                                            <span>{{ $emoteCase->label() }}</span>
                                            @if ($isSelected)
                                                <span class="text-xs">(Dipilih)</span>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-divider pt-5">
                    <a href="{{ route('catalog.index') }}" class="ayi-btn-secondary">
                        ← Kembali ke Katalog
                    </a>
                    @if ($order->outlet)
                        <a href="{{ route('catalog.outlet', $order->outlet->slug) }}" class="ayi-btn-primary">
                            Lihat Menu {{ $order->outlet->nama }}
                        </a>
                    @endif
                </div>
            </div>
        @elseif ($antrean)
            {{-- Masih Antrean (Belum Dibayar) -> Tolak Reaksi --}}
            <div class="ayi-card p-8 text-center sm:p-10" data-reveal="up">
                <div class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full bg-warning-bg text-warning">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
                    </svg>
                </div>

                <span class="mt-4 inline-block rounded-full bg-warning-bg px-3.5 py-1 font-heading text-xs font-bold text-warning">
                    Daftar Tunggu (Belum Dibayar di Kasir)
                </span>

                <h1 class="mt-3 font-heading text-[28px] font-bold text-ink">
                    Antrean {{ $antrean->kode }} Belum Dapat Diberi Reaksi
                </h1>

                <p class="mx-auto mt-2 max-w-xl text-sm leading-relaxed text-ink-secondary">
                    Antrean atas nama <strong>{{ $antrean->nama_pemesan }}</strong> di <strong>{{ $antrean->outlet?->nama }}</strong> saat ini masih berada di daftar tunggu (belum dibayar tunai di kasir). Sesuai aturan sistem (KF-07), reaksi emote hanya dapat diberikan setelah pembayaran dikonfirmasi oleh kasir.
                </p>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('catalog.reaksi.lookup') }}" class="ayi-btn-secondary">
                        Cari Kode Pesanan Lain
                    </a>
                    <a href="{{ route('catalog.index') }}" class="ayi-btn-primary">
                        Kembali ke Katalog
                    </a>
                </div>
            </div>
        @else
            {{-- Kode Tidak Ditemukan --}}
            <div class="ayi-card p-8 text-center sm:p-10" data-reveal="up">
                <h1 class="font-heading text-[28px] font-bold text-chili">
                    Kode Pesanan "{{ $kode }}" Tidak Ditemukan
                </h1>
                <p class="mx-auto mt-2 max-w-lg text-sm text-ink-secondary">
                    Pastikan kode pesanan yang Anda masukkan sesuai dengan kode yang tertera pada struk kasir (misal <code>AYZ-20261116-0001</code>).
                </p>
                <div class="mt-6">
                    <a href="{{ route('catalog.reaksi.lookup') }}" class="ayi-btn-primary">
                        Coba Masukkan Kode Lagi
                    </a>
                </div>
            </div>
        @endif
    </section>
</x-layouts.public>

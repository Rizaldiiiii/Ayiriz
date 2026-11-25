<x-layouts.public :title="$menu->nama">
    <section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Breadcrumb (Minimalism §5.11) --}}
        <nav aria-label="Breadcrumb" class="mb-4 text-xs font-semibold text-ink-secondary">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('catalog.index') }}" class="text-navy underline hover:text-chili">Beranda</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('catalog.outlet', $menu->outlet->slug) }}" class="text-navy underline hover:text-chili">{{ $menu->outlet->nama }}</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-ink">{{ $menu->nama }}</li>
            </ol>
        </nav>

        <div class="ayi-card overflow-hidden p-6 sm:p-8" data-reveal="up">
            <div class="grid gap-8 md:grid-cols-12 md:items-start">
                {{-- Visual Menu dengan Blok Warna Solid --}}
                <div class="md:col-span-5">
                    <div class="aspect-[4/3] w-full overflow-hidden rounded-[16px] bg-sunny p-5">
                        @if ($menu->foto)
                            <img
                                src="{{ asset('storage/' . $menu->foto) }}"
                                alt="{{ $menu->nama }}"
                                class="h-full w-full rounded-[12px] object-cover"
                            >
                        @else
                            <div class="flex h-full w-full flex-col items-center justify-center rounded-[12px] bg-cream p-6 text-center text-ink">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-tint-chili text-chili">
                                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2M7 2v20M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7" />
                                    </svg>
                                </div>
                                <p class="mt-3 font-heading text-sm font-bold text-ink">{{ $menu->nama }}</p>
                                <p class="text-xs text-ink-secondary">{{ $menu->outlet->nama }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Detail & Form Pesan Tamu --}}
                <div class="md:col-span-7">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-tint-navy px-3 py-1 font-heading text-xs font-bold text-navy">
                            {{ $menu->category?->nama ?? 'Menu' }}
                        </span>
                        @if ($menu->bisaDipesan())
                            <span class="ayi-badge-selesai">
                                <span class="h-2 w-2 rounded-full bg-leaf" aria-hidden="true"></span>
                                <span>Tersedia · {{ $menu->stok_porsi }} porsi hari ini</span>
                            </span>
                        @else
                            <span class="ayi-badge-nonaktif">
                                <span>Stok Habis / Tidak Tersedia</span>
                            </span>
                        @endif
                        @if ($menu->is_sample)
                            <span class="ayi-chip-sample">Data Contoh</span>
                        @endif
                    </div>

                    <h1 class="mt-3 font-heading text-[32px] font-bold leading-tight text-ink">
                        {{ $menu->nama }}
                    </h1>

                    <p class="mt-1 text-sm text-ink-secondary">
                        Kantin:
                        <a href="{{ route('catalog.outlet', $menu->outlet->slug) }}" class="font-bold text-navy underline hover:text-chili">
                            {{ $menu->outlet->nama }}
                        </a>
                        ({{ $menu->outlet->alamat }})
                    </p>

                    <div class="mt-4">
                        <span class="block text-xs font-semibold uppercase tracking-wider text-ink-muted">Harga Menu</span>
                        <span class="font-heading text-[32px] font-bold text-chili tabular-nums">
                            {{ $menu->formattedHarga() }}
                        </span>
                    </div>

                    <p class="mt-4 text-base leading-relaxed text-ink-secondary">
                        {{ $menu->deskripsi ?: 'Menu khas anak kuliahan dari kantin mitra Ayiriz di depan gerbang kampus UNG. Dimasak hangat saat Anda datang dan membayar tunai di kasir.' }}
                    </p>

                    {{-- Reaksi Emote (KF-07 — arti emote tertulis di samping pilihan, bukan hanya tooltip) --}}
                    @php
                        $rc = $menu->reactionCounts();
                    @endphp
                    <div class="mt-6 rounded-[12px] border border-border-ayi bg-surface-cream p-4">
                        <p class="font-heading text-xs font-bold uppercase tracking-wider text-ink-secondary">
                            Kesan Pelanggan terhadap Menu Ini (Tanpa Skor / Ranking):
                        </p>
                        <div class="mt-2.5 flex flex-wrap gap-2 text-xs font-semibold text-ink">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-tint-chili px-3 py-1">
                                <span aria-hidden="true">😋</span>
                                <span>Enak: <strong class="tabular-nums">{{ $rc['enak'] }}</strong></span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-tint-tang px-3 py-1">
                                <span aria-hidden="true">🥵</span>
                                <span>Pedas: <strong class="tabular-nums">{{ $rc['pedas'] }}</strong></span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-tint-sunny px-3 py-1">
                                <span aria-hidden="true">🫠</span>
                                <span>Manis / menyejukkan: <strong class="tabular-nums">{{ $rc['manis'] }}</strong></span>
                            </span>
                        </div>
                        <p class="mt-2 text-[11px] text-ink-secondary">
                            Sudah memesan dan membayar di kasir?
                            <a href="{{ route('catalog.reaksi.lookup') }}" class="font-bold text-chili underline hover:text-chili-hover">
                                Beri Reaksi dengan Kode Pesanan →
                            </a>
                        </p>
                    </div>

                    {{-- Aksi Tambah ke Keranjang (Tamu Tanpa Login — Model B) --}}
                    <div class="mt-6 border-t border-divider pt-5">
                        @if ($menu->bisaDipesan())
                            <form
                                action="{{ route('cart.items.store') }}"
                                method="POST"
                                x-data="{ qty: 1, max: {{ $menu->stok_porsi }} }"
                                class="flex flex-wrap items-center gap-4"
                            >
                                @csrf
                                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                <input type="hidden" name="jumlah" :value="qty">

                                <div class="inline-flex min-h-[46px] items-center rounded-full border border-border-strong bg-surface px-2">
                                    <button
                                        type="button"
                                        @click="if (qty > 1) qty--"
                                        class="flex h-10 w-10 items-center justify-center rounded-full font-heading text-lg font-bold text-ink hover:bg-surface-cream active:scale-[0.97]"
                                        aria-label="Kurangi porsi"
                                    >
                                        −
                                    </button>
                                    <span class="min-w-[36px] text-center font-heading text-base font-bold text-ink tabular-nums" x-text="qty">1</span>
                                    <button
                                        type="button"
                                        @click="if (qty < max) qty++"
                                        class="flex h-10 w-10 items-center justify-center rounded-full font-heading text-lg font-bold text-ink hover:bg-surface-cream active:scale-[0.97]"
                                        aria-label="Tambah porsi"
                                    >
                                        +
                                    </button>
                                </div>

                                <button type="submit" class="ayi-btn-primary px-6">
                                    <span>Tambah ke Keranjang</span>
                                </button>
                            </form>
                            <p class="mt-2.5 text-xs text-ink-secondary">
                                Tidak perlu daftar/login. Cukup tulis nama pemesan saat submit keranjang ke antrean kasir.
                            </p>
                        @else
                            <button type="button" disabled class="ayi-btn-primary opacity-60 cursor-not-allowed">
                                Stok Porsi Habis
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>

@props([
    'menu',
    'index' => 0,
    'cartQty' => 0,
    'showOutletName' => true,
])

@php
    // Blok warna solid di belakang foto bergiliran (Plately pattern: hijau #14502E, oranye #DE652E, kuning #FEC345)
    $blockColors = ['bg-leaf text-white', 'bg-tang text-ink', 'bg-sunny text-ink'];
    $blockClass = $blockColors[$index % 3];
    $bisaDipesan = $menu->bisaDipesan();
@endphp

<article
    class="ayi-card interactive-card flex flex-col overflow-hidden p-4 sm:p-5"
    data-reveal="up"
    style="--reveal-delay: {{ ($index % 4) * 80 }}ms"
>
    {{-- Blok warna solid di belakang foto makanan (Plately style) --}}
    <a
        href="{{ route('catalog.menu', $menu) }}"
        class="relative block aspect-[4/3] w-full overflow-hidden rounded-[12px] {{ $blockClass }} p-4 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-chili"
    >
        @if ($menu->foto)
            <img
                src="{{ asset('storage/' . $menu->foto) }}"
                alt="{{ $menu->nama }}"
                class="h-full w-full rounded-[8px] object-cover"
                loading="lazy"
            />
        @else
            <div class="flex h-full w-full flex-col items-center justify-center rounded-[10px] bg-cream p-3 text-ink shadow-ayi-xs">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-tint-sunny text-chili">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2M7 2v20M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7" />
                    </svg>
                </div>
                <span class="mt-2 line-clamp-1 text-center font-heading text-xs font-bold uppercase tracking-wider text-ink-secondary">
                    {{ $menu->category?->nama ?? 'Menu Kantin' }}
                </span>
            </div>
        @endif

        <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
            <span class="rounded-full bg-cream px-2.5 py-1 font-heading text-xs font-bold text-ink shadow-ayi-xs">
                {{ $menu->category?->nama ?? 'Menu' }}
            </span>
        </div>

        <div class="absolute bottom-3 right-3">
            @if ($bisaDipesan)
                <span class="rounded-full bg-surface px-2.5 py-1 font-heading text-xs font-bold text-leaf shadow-ayi-xs">
                    Stok {{ $menu->stok_porsi }} porsi
                </span>
            @else
                <span class="rounded-full bg-divider px-2.5 py-1 font-heading text-xs font-bold text-ink-secondary shadow-ayi-xs">
                    Habis
                </span>
            @endif
        </div>
    </a>

    {{-- Isi Kartu di atas Putih Solid --}}
    <div class="mt-4 flex flex-1 flex-col justify-between">
        <div>
            @if ($showOutletName && $menu->outlet)
                <div class="flex items-center justify-between gap-2">
                    <a
                        href="{{ route('catalog.outlet', $menu->outlet->slug) }}"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-navy underline decoration-navy/40 underline-offset-2 hover:text-chili"
                    >
                        <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                        </svg>
                        <span class="truncate">{{ $menu->outlet->nama }}</span>
                    </a>
                </div>
            @endif

            <h3 class="mt-1 font-heading text-[21px] font-bold leading-snug text-ink">
                <a href="{{ route('catalog.menu', $menu) }}" class="hover:text-chili focus-visible:outline focus-visible:outline-2 focus-visible:outline-chili">
                    {{ $menu->nama }}
                </a>
            </h3>

            @if ($menu->deskripsi)
                <p class="mt-1 line-clamp-2 text-[13px] leading-relaxed text-ink-secondary">
                    {{ $menu->deskripsi }}
                </p>
            @endif

            {{-- Hitungan reaksi per menu (tanpa skor total/ranking, stub Tahap 2) --}}
            <div class="mt-3 flex flex-wrap items-center gap-1.5 text-xs text-ink-secondary" aria-label="Kesan reaksi pelanggan">
                <span class="inline-flex items-center gap-1 rounded-full bg-tint-chili px-2.5 py-0.5 font-medium text-ink" title="Enak">
                    <span aria-hidden="true">😋</span> 0
                </span>
                <span class="inline-flex items-center gap-1 rounded-full bg-tint-tang px-2.5 py-0.5 font-medium text-ink" title="Pedas">
                    <span aria-hidden="true">🥵</span> 0
                </span>
                <span class="inline-flex items-center gap-1 rounded-full bg-tint-sunny px-2.5 py-0.5 font-medium text-ink" title="Manis / menyejukkan">
                    <span aria-hidden="true">🫠</span> 0
                </span>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between gap-3 border-t border-divider pt-3.5">
            <div>
                <span class="block text-[11px] font-medium uppercase tracking-wider text-ink-muted">Harga</span>
                <span class="font-heading text-[24px] font-bold leading-none text-chili tabular-nums">
                    {{ $menu->formattedHarga() }}
                </span>
            </div>

            @auth
                @if (auth()->user()->isPelanggan())
                    @if (! $bisaDipesan)
                        <button
                            type="button"
                            disabled
                            class="inline-flex min-h-[44px] items-center justify-center rounded-full bg-divider px-4 py-2 font-heading text-sm font-bold text-ink-secondary cursor-not-allowed"
                        >
                            Stok Habis
                        </button>
                    @elseif ($cartQty > 0)
                        {{-- Stepper jumlah (- n +) khas Ayiriz --}}
                        <div class="inline-flex min-h-[44px] items-center rounded-full bg-chili px-1.5 text-white shadow-ayi-xs">
                            <form action="{{ route('cart.items.update', $menu) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="jumlah" value="{{ $cartQty - 1 }}">
                                <button
                                    type="submit"
                                    aria-label="Kurangi jumlah {{ $menu->nama }}"
                                    class="flex h-9 w-9 items-center justify-center rounded-full font-heading text-lg font-bold text-white transition active:scale-[0.97] hover:bg-chili-hover"
                                >
                                    −
                                </button>
                            </form>
                            <span class="min-w-[28px] text-center font-heading text-sm font-bold tabular-nums">
                                {{ $cartQty }}
                            </span>
                            <form action="{{ route('cart.items.store') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                <input type="hidden" name="jumlah" value="1">
                                <button
                                    type="submit"
                                    aria-label="Tambah jumlah {{ $menu->nama }}"
                                    @if ($cartQty >= $menu->stok_porsi) disabled @endif
                                    class="flex h-9 w-9 items-center justify-center rounded-full font-heading text-lg font-bold text-white transition active:scale-[0.97] hover:bg-chili-hover disabled:opacity-40"
                                >
                                    +
                                </button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('cart.items.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                            <input type="hidden" name="jumlah" value="1">
                            <button
                                type="submit"
                                class="ayi-btn-primary min-h-[44px] px-4 py-2 text-sm"
                            >
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                                </svg>
                                <span>Tambah</span>
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('catalog.menu', $menu) }}" class="ayi-btn-secondary min-h-[44px] px-4 py-2 text-sm">
                        Detail
                    </a>
                @endif
            @else
                @if ($bisaDipesan)
                    <a
                        href="{{ route('login') }}"
                        class="ayi-btn-primary min-h-[44px] px-4 py-2 text-sm"
                        title="Login pelanggan untuk memesan"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                        </svg>
                        <span>Tambah</span>
                    </a>
                @else
                    <span class="inline-flex min-h-[44px] items-center justify-center rounded-full bg-divider px-4 py-2 font-heading text-sm font-bold text-ink-secondary">
                        Stok Habis
                    </span>
                @endif
            @endauth
        </div>
    </div>
</article>

<x-layouts.public title="Keranjang & Antrean Pesanan">
    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Header & Stepper Alur Model B (Minimalism §5.11) --}}
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal="up">
            <div>
                <div class="ayi-accent-bar mb-2 w-12"></div>
                <h1 class="font-heading text-[32px] font-bold text-ink">
                    KERANJANG & DAFTAR TUNGGU (ANTREAN)
                </h1>
                <p class="text-sm text-ink-secondary">
                    Pesan sebagai tamu tanpa login. Tulis nama pemesan, kirim ke daftar tunggu kasir, lalu bayar tunai saat tiba di kantin.
                </p>
            </div>

            <div class="inline-flex flex-wrap items-center gap-2 rounded-full border border-border-ayi bg-surface px-4 py-2 text-xs font-bold text-ink shadow-ayi-xs">
                <span class="rounded-full bg-chili px-2.5 py-0.5 text-white">1. Keranjang Tamu</span>
                <span>→</span>
                <span class="text-ink-secondary">2. Masuk Antrean Kasir</span>
                <span>→</span>
                <span class="text-ink-secondary">3. Bayar Tunai (Diproses → Selesai)</span>
            </div>
        </div>

        {{-- Bukti Antrean Terakhir yang Baru Disubmit Tamu (KF-04 Model B) --}}
        @if (! empty($lastAntrean))
            <div class="ayi-card mt-6 border-2 border-leaf bg-surface p-6 sm:p-8" data-reveal="up">
                <div class="flex flex-col justify-between gap-4 border-b border-divider pb-5 sm:flex-row sm:items-center">
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-tint-leaf px-3 py-1 font-heading text-xs font-bold text-leaf">
                            <span class="h-2 w-2 rounded-full bg-leaf" aria-hidden="true"></span>
                            <span>Terdaftar di Daftar Tunggu (Antrean Kasir)</span>
                        </span>
                        <h2 class="mt-2 font-heading text-[28px] font-bold text-ink">
                            Tunjukkan Kode Antrean Ini di Kasir {{ $lastAntrean['outlet_nama'] }}
                        </h2>
                        <p class="text-xs text-ink-secondary">
                            Waktu Submit: {{ $lastAntrean['created_at'] }} · Lokasi: {{ $lastAntrean['outlet_alamat'] }}
                        </p>
                    </div>

                    <div class="rounded-[16px] border border-border-ayi bg-surface-cream px-5 py-3 text-left sm:text-right">
                        <span class="block font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">Kode Antrean Anda</span>
                        <span class="font-heading text-[32px] font-bold text-chili tabular-nums">{{ $lastAntrean['kode'] }}</span>
                    </div>
                </div>

                <div class="mt-5 grid gap-6 lg:grid-cols-12 lg:items-start">
                    <div class="space-y-3 lg:col-span-5">
                        <div class="rounded-[12px] bg-surface-cream p-4">
                            <span class="font-heading text-xs font-bold uppercase tracking-wider text-ink-muted">Nama Pemesan</span>
                            <p class="mt-1 font-heading text-2xl font-bold text-navy">{{ $lastAntrean['nama_pemesan'] }}</p>
                        </div>

                        <div class="rounded-[12px] bg-tint-sunny p-4 text-xs leading-relaxed text-ink">
                            <p class="font-heading text-sm font-bold text-warning">Langkah Selanjutnya di Kantin:</p>
                            <ol class="mt-1.5 list-decimal space-y-1 pl-4 text-ink-secondary">
                                <li>Datang ke kasir <strong>{{ $lastAntrean['outlet_nama'] }}</strong> dan sebutkan nama <strong>{{ $lastAntrean['nama_pemesan'] }}</strong> atau kode <strong>{{ $lastAntrean['kode'] }}</strong>.</li>
                                <li>Bayar <strong>tunai</strong> sebesar <strong>Rp {{ number_format($lastAntrean['total'], 0, ',', '.') }}</strong>.</li>
                                <li>Kasir mengonfirmasi pembayaran Anda, antrean berubah menjadi pesanan <strong>Diproses</strong> untuk dimasak, lalu <strong>Selesai</strong> saat diserahkan.</li>
                            </ol>
                        </div>
                    </div>

                    <div class="lg:col-span-7">
                        <div class="overflow-hidden rounded-[12px] border border-border-ayi">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase text-ink">
                                    <tr>
                                        <th class="px-4 py-3">Menu (Harga Terkunci Saat Submit)</th>
                                        <th class="px-4 py-3 text-right">Harga</th>
                                        <th class="px-4 py-3 text-center">Qty</th>
                                        <th class="px-4 py-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-divider bg-surface">
                                    @foreach ($lastAntrean['items'] as $item)
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-ink">{{ $item['nama_menu_saat_submit'] }}</td>
                                            <td class="px-4 py-3 text-right font-heading font-bold text-ink-secondary tabular-nums">
                                                Rp {{ number_format($item['harga_saat_submit'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-center font-heading font-bold text-ink tabular-nums">{{ $item['jumlah'] }}</td>
                                            <td class="px-4 py-3 text-right font-heading font-bold text-ink tabular-nums">
                                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="border-t border-border-ayi bg-surface-cream">
                                    <tr>
                                        <td colspan="3" class="px-4 py-3.5 font-heading text-base font-bold text-ink">
                                            Total Bayar Tunai di Kasir
                                        </td>
                                        <td class="px-4 py-3.5 text-right font-heading text-2xl font-bold text-chili tabular-nums">
                                            Rp {{ number_format($lastAntrean['total'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($cart['outlet'] && count($cart['items']) > 0)
            <div class="mt-6 grid gap-6 lg:grid-cols-12 lg:items-start">
                {{-- Daftar Item Keranjang --}}
                <div class="space-y-4 lg:col-span-7">
                    <div class="ayi-card bg-surface-cream p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-wider text-ink-muted">Kantin Tujuan Pengambilan & Kasir</span>
                                <h2 class="font-heading text-[22px] font-bold text-navy">
                                    <a href="{{ route('catalog.outlet', $cart['outlet']->slug) }}" class="underline decoration-navy/40 hover:text-chili">
                                        {{ $cart['outlet']->nama }}
                                    </a>
                                </h2>
                                <p class="text-xs text-ink-secondary">
                                    {{ $cart['outlet']->alamat }} · Jam Buka: {{ $cart['outlet']->jam_buka }}
                                </p>
                            </div>

                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="rounded-full bg-tint-chili px-3.5 py-2 font-heading text-xs font-bold text-chili hover:bg-chili hover:text-white transition"
                                >
                                    Kosongkan Keranjang
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach ($cart['items'] as $row)
                            @php
                                /** @var \App\Models\Menu $menu */
                                $menu = $row['menu'];
                                $qty = $row['jumlah'];
                                $subtotal = $row['subtotal'];
                            @endphp
                            <div class="ayi-card flex flex-col justify-between gap-4 p-5 sm:flex-row sm:items-center">
                                <div class="flex-1">
                                    <span class="rounded-full bg-tint-navy px-2.5 py-0.5 font-heading text-[11px] font-bold text-navy">
                                        {{ $menu->category?->nama ?? 'Menu' }}
                                    </span>
                                    <h3 class="mt-1 font-heading text-[20px] font-bold text-ink">
                                        <a href="{{ route('catalog.menu', $menu) }}" class="hover:text-chili">
                                            {{ $menu->nama }}
                                        </a>
                                    </h3>
                                    <p class="mt-0.5 text-sm font-semibold text-chili tabular-nums">
                                        {{ $menu->formattedHarga() }} / porsi
                                        <span class="ml-2 text-xs font-normal text-ink-muted">(Sisa stok: {{ $menu->stok_porsi }} porsi)</span>
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-4 sm:justify-end">
                                    {{-- Stepper Jumlah (- n +) --}}
                                    <div class="inline-flex min-h-[44px] items-center rounded-full border border-border-strong bg-surface px-1.5">
                                        <form action="{{ route('cart.items.update', $menu) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="jumlah" value="{{ $qty - 1 }}">
                                            <button
                                                type="submit"
                                                aria-label="Kurangi jumlah {{ $menu->nama }}"
                                                class="flex h-9 w-9 items-center justify-center rounded-full font-heading text-lg font-bold text-ink hover:bg-surface-cream active:scale-[0.97]"
                                            >
                                                −
                                            </button>
                                        </form>

                                        <span class="min-w-[32px] text-center font-heading text-sm font-bold text-ink tabular-nums">
                                            {{ $qty }}
                                        </span>

                                        <form action="{{ route('cart.items.update', $menu) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="jumlah" value="{{ $qty + 1 }}">
                                            <button
                                                type="submit"
                                                aria-label="Tambah jumlah {{ $menu->nama }}"
                                                @if ($qty >= $menu->stok_porsi) disabled @endif
                                                class="flex h-9 w-9 items-center justify-center rounded-full font-heading text-lg font-bold text-ink hover:bg-surface-cream active:scale-[0.97] disabled:opacity-40"
                                            >
                                                +
                                            </button>
                                        </form>
                                    </div>

                                    <div class="min-w-[110px] text-right">
                                        <span class="block text-[11px] uppercase text-ink-muted">Subtotal</span>
                                        <span class="font-heading text-lg font-bold text-ink tabular-nums">
                                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <form action="{{ route('cart.items.destroy', $menu) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            aria-label="Hapus {{ $menu->nama }} dari keranjang"
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-tint-chili text-chili hover:bg-chili hover:text-white transition"
                                        >
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Form Submit Antrean Tamu (KF-04 Model B) --}}
                <aside class="ayi-card p-6 lg:col-span-5">
                    <h2 class="font-heading text-[22px] font-bold text-ink">
                        KIRIM KE DAFTAR TUNGGU (ANTREAN)
                    </h2>

                    <form action="{{ route('cart.submit') }}" method="POST" class="mt-4 space-y-4" x-data="{ loading: false }" @submit="loading = true">
                        @csrf

                        <div>
                            <label for="nama_pemesan" class="block font-heading text-sm font-bold text-ink">
                                Nama Pemesan <span class="text-chili">*</span>
                            </label>
                            <input
                                id="nama_pemesan"
                                type="text"
                                name="nama_pemesan"
                                value="{{ old('nama_pemesan') }}"
                                required
                                maxlength="120"
                                placeholder="Contoh: Aldi Pratama"
                                class="ayi-input mt-1.5"
                                @error('nama_pemesan') aria-invalid="true" aria-describedby="nama-pemesan-error" @enderror
                            >
                            <p class="mt-1 text-xs text-ink-secondary">
                                Nama ini akan tampil di layar kasir <strong>{{ $cart['outlet']->nama }}</strong> saat Anda membayar tunai.
                            </p>
                            @error('nama_pemesan')
                                <p id="nama-pemesan-error" class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">
                                    ● {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <dl class="space-y-2.5 border-t border-divider pt-4 text-sm">
                            <div class="flex items-center justify-between">
                                <dt class="text-ink-secondary">Kantin Mitra</dt>
                                <dd class="font-bold text-ink">{{ $cart['outlet']->nama }}</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-ink-secondary">Total Porsi</dt>
                                <dd class="font-bold text-ink tabular-nums">{{ $cart['total_item'] }} porsi</dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-ink-secondary">Metode Pembayaran</dt>
                                <dd>
                                    <span class="rounded-full bg-tint-navy px-2.5 py-0.5 font-heading text-xs font-bold text-navy">
                                        Bayar Tunai di Kasir
                                    </span>
                                </dd>
                            </div>
                            <div class="flex items-center justify-between border-t border-divider pt-3">
                                <dt class="font-heading text-base font-bold text-ink">Total Bayar</dt>
                                <dd class="font-heading text-[26px] font-bold text-chili tabular-nums">
                                    Rp {{ number_format($cart['total'], 0, ',', '.') }}
                                </dd>
                            </div>
                        </dl>

                        <div class="rounded-[12px] bg-surface-cream p-3.5 text-xs leading-relaxed text-ink-secondary">
                            <p class="font-bold text-ink">Aturan Antrean & Kasir Ayiriz (Model B):</p>
                            <ul class="mt-1 list-disc space-y-1 pl-4">
                                <li>Harga menu dikunci saat submit antrean (<code>harga_saat_submit</code>).</li>
                                <li>Submit keranjang masuk <strong>daftar tunggu (antrean)</strong> dan belum mengurangi stok porsi.</li>
                                <li>Saat Anda datang dan membayar tunai di kasir, kasir mengonfirmasi pembayaran — antrean berubah menjadi pesanan <strong>Diproses</strong> (stok porsi berkurang) lalu dimasak.</li>
                            </ul>
                        </div>

                        <button type="submit" :disabled="loading" class="ayi-btn-primary w-full">
                            <span x-show="!loading">Kirim ke Antrean Kasir</span>
                            <span x-cloak x-show="loading" class="flex items-center gap-2">
                                <span class="loading-spinner"></span>
                                <span>Mengirim Antrean...</span>
                            </span>
                        </button>
                    </form>

                    <a href="{{ route('catalog.outlet', $cart['outlet']->slug) }}" class="ayi-btn-secondary mt-3 w-full">
                        + Tambah Menu Lain di Kantin Ini
                    </a>
                </aside>
            </div>
        @elseif (empty($lastAntrean))
            <div class="ayi-card mt-6 p-10 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-tint-chili text-chili">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="8" cy="21" r="1" />
                        <circle cx="19" cy="21" r="1" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                    </svg>
                </div>
                <h2 class="mt-4 font-heading text-[24px] font-bold text-ink">
                    Keranjang Anda Masih Kosong
                </h2>
                <p class="mx-auto mt-1 max-w-md text-sm text-ink-secondary">
                    Pilih satu kantin mitra Ayiriz di depan gerbang kampus UNG, tambahkan makanan atau minuman ke keranjang, lalu tulis nama Anda tanpa perlu daftar akun.
                </p>
                <a href="{{ route('catalog.index') }}" class="ayi-btn-primary mt-6">
                    Lihat Katalog Kantin & Menu
                </a>
            </div>
        @endif
    </section>
</x-layouts.public>

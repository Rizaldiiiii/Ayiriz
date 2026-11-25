<x-layouts.panel :title="'Stok Bahan & Resep ' . $outlet->nama">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal="up">
        <div>
            <div class="ayi-accent-bar mb-2 w-12"></div>
            <h1 class="font-heading text-[32px] font-bold text-ink">
                MANAJEMEN STOK BAHAN BAKU & RESEP (KF-06)
            </h1>
            <p class="text-sm text-ink-secondary">
                Kelola stok bahan baku untuk <strong>{{ $outlet->nama }}</strong>, catat stok masuk/keluar manual, tetapkan batas minimum, dan atur resep takaran per menu.
            </p>
        </div>

        <a href="{{ route('outlet.menus.index') }}" class="ayi-btn-secondary">
            Kelola Stok Porsi Menu →
        </a>
    </div>

    {{-- Banner Peringatan Stok Menipis --}}
    @if ($stokMenipis->count() > 0)
        <div class="mt-6 rounded-[16px] border border-border-strong bg-warning-bg p-5 text-warning" role="alert">
            <div class="flex items-start gap-3">
                <span class="mt-1.5 h-3 w-3 shrink-0 rounded-full bg-sunny" aria-hidden="true"></span>
                <div>
                    <h2 class="font-heading text-lg font-bold text-warning">
                        Peringatan Stok Bahan Menipis ({{ $stokMenipis->count() }} Bahan)
                    </h2>
                    <p class="mt-1 text-xs text-ink-secondary">
                        Bahan baku berikut memiliki stok saat ini kurang dari atau sama dengan batas minimum. Catat stok masuk agar peringatan hilang:
                    </p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($stokMenipis as $item)
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-border-strong bg-surface px-3 py-1 font-heading text-xs font-bold text-warning shadow-ayi-xs">
                                <span>{{ $item->nama }}:</span>
                                <span class="tabular-nums text-chili">{{ $item->formattedStok() }}</span>
                                <span class="text-ink-muted">(Min: {{ $item->formattedBatasMinimum() }})</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Bagian 1: Daftar Bahan Baku + Form Tambah Bahan & Catat Mutasi --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-12 lg:items-start">
        {{-- Daftar Bahan Baku Outlet --}}
        <div class="space-y-4 lg:col-span-8">
            <div class="ayi-card p-6">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="font-heading text-[22px] font-bold text-ink">
                        DAFTAR STOK BAHAN BAKU ({{ $ingredients->count() }})
                    </h2>
                </div>

                @if ($ingredients->count() > 0)
                    <div class="mt-4 space-y-4">
                        @foreach ($ingredients as $ingredient)
                            <article class="rounded-[12px] border border-border-ayi bg-surface-cream p-4" x-data="{ openMutasi: false, openEdit: false }">
                                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-heading text-xl font-bold text-ink">{{ $ingredient->nama }}</h3>
                                            @if ($ingredient->isMenipis())
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-warning-bg px-3 py-0.5 font-heading text-xs font-bold text-warning">
                                                    <span class="h-2 w-2 rounded-full bg-sunny" aria-hidden="true"></span>
                                                    <span>Stok Menipis</span>
                                                </span>
                                            @else
                                                <span class="ayi-badge-selesai">
                                                    <span class="h-2 w-2 rounded-full bg-leaf" aria-hidden="true"></span>
                                                    <span>Aman</span>
                                                </span>
                                            @endif
                                            @if ($ingredient->is_sample)
                                                <span class="ayi-chip-sample">Data Contoh</span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-xs text-ink-secondary">
                                            Stok Saat Ini: <strong class="font-heading text-base text-ink tabular-nums">{{ $ingredient->formattedStok() }}</strong>
                                            · Batas Minimum: <strong class="tabular-nums">{{ $ingredient->formattedBatasMinimum() }}</strong>
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">
                                        <button
                                            type="button"
                                            @click="openMutasi = !openMutasi; openEdit = false"
                                            class="ayi-btn-primary min-h-[38px] px-3.5 py-1.5 text-xs"
                                        >
                                            + Catat Masuk / Keluar
                                        </button>
                                        <button
                                            type="button"
                                            @click="openEdit = !openEdit; openMutasi = false"
                                            class="ayi-btn-secondary min-h-[38px] px-3.5 py-1.5 text-xs"
                                        >
                                            Ubah Batas Min
                                        </button>
                                        <form
                                            action="{{ route('outlet.stok.ingredients.destroy', $ingredient) }}"
                                            method="POST"
                                            onsubmit="return confirm('Hapus bahan baku {{ addslashes($ingredient->nama) }}?');"
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
                                </div>

                                {{-- Panel Form Mutasi Stok Masuk / Keluar Manual --}}
                                <div x-cloak x-show="openMutasi" class="mt-4 border-t border-divider pt-4">
                                    <form action="{{ route('outlet.stok.movements.store', $ingredient) }}" method="POST" class="grid gap-3 sm:grid-cols-12">
                                        @csrf
                                        <div class="sm:col-span-3">
                                            <label class="block font-heading text-xs font-bold text-ink">Jenis Mutasi</label>
                                            <select name="jenis" class="ayi-input mt-1 min-h-[40px] py-1.5 text-xs">
                                                <option value="masuk">Stok Masuk (+)</option>
                                                <option value="keluar">Stok Keluar Manual (−)</option>
                                                <option value="penyesuaian">Penyesuaian (=)</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label class="block font-heading text-xs font-bold text-ink">Jumlah ({{ $ingredient->satuan }})</label>
                                            <input type="number" name="jumlah" step="0.01" min="0.01" required placeholder="1.5" class="ayi-input mt-1 min-h-[40px] py-1.5 text-xs tabular-nums">
                                        </div>
                                        <div class="sm:col-span-4">
                                            <label class="block font-heading text-xs font-bold text-ink">Keterangan</label>
                                            <input type="text" name="keterangan" placeholder="Belanja pasar pagi / pemakaian" class="ayi-input mt-1 min-h-[40px] py-1.5 text-xs">
                                        </div>
                                        <div class="flex items-end sm:col-span-2">
                                            <button type="submit" class="ayi-btn-primary min-h-[40px] w-full px-3 py-1.5 text-xs">
                                                Simpan
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                {{-- Panel Form Edit Bahan & Batas Minimum --}}
                                <div x-cloak x-show="openEdit" class="mt-4 border-t border-divider pt-4">
                                    <form action="{{ route('outlet.stok.ingredients.update', $ingredient) }}" method="POST" class="grid gap-3 sm:grid-cols-12">
                                        @csrf
                                        @method('PUT')
                                        <div class="sm:col-span-4">
                                            <label class="block font-heading text-xs font-bold text-ink">Nama Bahan</label>
                                            <input type="text" name="nama" value="{{ $ingredient->nama }}" required class="ayi-input mt-1 min-h-[40px] py-1.5 text-xs">
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label class="block font-heading text-xs font-bold text-ink">Satuan</label>
                                            <input type="text" name="satuan" value="{{ $ingredient->satuan }}" required class="ayi-input mt-1 min-h-[40px] py-1.5 text-xs">
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label class="block font-heading text-xs font-bold text-ink">Batas Minimum</label>
                                            <input type="number" name="batas_minimum" step="0.01" min="0" value="{{ (float) $ingredient->batas_minimum }}" required class="ayi-input mt-1 min-h-[40px] py-1.5 text-xs tabular-nums">
                                        </div>
                                        <div class="flex items-end sm:col-span-2">
                                            <button type="submit" class="ayi-btn-primary min-h-[40px] w-full px-3 py-1.5 text-xs">
                                                Perbarui
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4 text-sm text-ink-secondary">Belum ada bahan baku yang dicatat untuk outlet ini.</p>
                @endif
            </div>
        </div>

        {{-- Form Tambah Bahan Baku Baru --}}
        <aside class="ayi-card p-6 lg:col-span-4">
            <h2 class="font-heading text-xl font-bold text-ink">
                TAMBAH BAHAN BAKU BARU
            </h2>
            <p class="mt-1 text-xs text-ink-secondary">
                Bahan baku bersifat per outlet. Tetapkan batas minimum untuk mendapat peringatan saat stok menipis.
            </p>

            <form action="{{ route('outlet.stok.ingredients.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="ing-nama" class="block font-heading text-sm font-bold text-ink">Nama Bahan Baku</label>
                    <input id="ing-nama" type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Daging Ayam Segar" class="ayi-input mt-1">
                </div>

                <div>
                    <label for="ing-satuan" class="block font-heading text-sm font-bold text-ink">Satuan</label>
                    <input id="ing-satuan" type="text" name="satuan" value="{{ old('satuan', 'kg') }}" required placeholder="kg, liter, pcs, bungkus" class="ayi-input mt-1">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="ing-stok" class="block font-heading text-sm font-bold text-ink">Stok Awal</label>
                        <input id="ing-stok" type="number" name="stok" step="0.01" min="0" value="{{ old('stok', 10) }}" required class="ayi-input mt-1 tabular-nums">
                    </div>

                    <div>
                        <label for="ing-batas" class="block font-heading text-sm font-bold text-ink">Batas Minimum</label>
                        <input id="ing-batas" type="number" name="batas_minimum" step="0.01" min="0" value="{{ old('batas_minimum', 3) }}" required class="ayi-input mt-1 tabular-nums">
                    </div>
                </div>

                <button type="submit" class="ayi-btn-primary w-full">
                    + Simpan Bahan Baku
                </button>
            </form>
        </aside>
    </div>

    {{-- Bagian 2: Resep Sederhana per Menu (Opsional, 0..n Bahan Baku) --}}
    <section class="mt-8">
        <div class="ayi-card p-6">
            <h2 class="font-heading text-[24px] font-bold text-ink">
                RESEP SEDERHANA PER MENU (OPSIONAL · PENGURANGAN OTOMATIS SAAT SELESAI)
            </h2>
            <p class="mt-1 text-xs text-ink-secondary">
                Satu menu boleh dihubungkan ke 0..n bahan baku dengan takaran per 1 porsi. Bila menu memiliki resep, stok bahan baku akan <strong>berkurang otomatis saat pesanan berstatus Selesai</strong>. Bila tidak memiliki resep (takaran 0), hanya stok porsi menu yang bergerak.
            </p>

            @if ($ingredients->count() === 0)
                <p class="mt-4 rounded-[12px] bg-surface-cream p-4 text-xs text-ink-secondary">
                    Tambahkan bahan baku terlebih dahulu di atas untuk mengatur resep menu.
                </p>
            @else
                <div class="mt-4 space-y-4">
                    @foreach ($menus as $menu)
                        @php
                            $existingMap = $menu->menuIngredients->pluck('takaran', 'ingredient_id')->all();
                        @endphp
                        <div class="rounded-[12px] border border-border-ayi bg-surface-cream p-4" x-data="{ openResep: false }">
                            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-heading text-lg font-bold text-ink">{{ $menu->nama }}</h3>
                                        <span class="rounded-full bg-tint-navy px-2.5 py-0.5 font-heading text-[11px] font-bold text-navy">
                                            {{ $menu->category?->nama }}
                                        </span>
                                    </div>

                                    @if ($menu->menuIngredients->count() > 0)
                                        <p class="mt-1 text-xs text-leaf font-semibold">
                                            Resep Aktif (per 1 porsi):
                                            @foreach ($menu->menuIngredients as $ri)
                                                <span>{{ $ri->ingredient?->nama }} ({{ (float) $ri->takaran }} {{ $ri->ingredient?->satuan }})</span>@if (! $loop->last), @endif
                                            @endforeach
                                        </p>
                                    @else
                                        <p class="mt-1 text-xs text-ink-muted">
                                            Tanpa resep bahan baku (hanya menggunakan stok porsi: {{ $menu->stok_porsi }} porsi).
                                        </p>
                                    @endif
                                </div>

                                <button
                                    type="button"
                                    @click="openResep = !openResep"
                                    class="ayi-btn-secondary min-h-[38px] px-4 py-1.5 text-xs"
                                >
                                    Atur Resep Menu
                                </button>
                            </div>

                            <div x-cloak x-show="openResep" class="mt-4 border-t border-divider pt-4">
                                <form action="{{ route('outlet.stok.recipes.update', $menu) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <p class="mb-3 text-xs text-ink-secondary">
                                        Isi takaran bahan per 1 porsi <strong>{{ $menu->nama }}</strong> (isi <code>0</code> atau kosongkan bila bahan tidak dipakai pada menu ini):
                                    </p>

                                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                        @foreach ($ingredients as $idx => $ing)
                                            <div class="rounded-[10px] border border-border-ayi bg-surface p-3">
                                                <input type="hidden" name="resep[{{ $idx }}][ingredient_id]" value="{{ $ing->id }}">
                                                <label class="block font-heading text-xs font-bold text-ink">
                                                    {{ $ing->nama }} ({{ $ing->satuan }})
                                                </label>
                                                <input
                                                    type="number"
                                                    name="resep[{{ $idx }}][takaran]"
                                                    step="0.01"
                                                    min="0"
                                                    value="{{ isset($existingMap[$ing->id]) ? (float) $existingMap[$ing->id] : 0 }}"
                                                    class="ayi-input mt-1 min-h-[38px] py-1 text-xs tabular-nums"
                                                >
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-4 flex justify-end">
                                        <button type="submit" class="ayi-btn-primary min-h-[38px] px-5 py-1.5 text-xs">
                                            Simpan Resep {{ $menu->nama }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Bagian 3: Riwayat Pergerakan Stok (stock_movements) --}}
    <section class="mt-8">
        <div class="ayi-card p-6">
            <h2 class="font-heading text-[22px] font-bold text-ink">
                RIWAYAT PERGERAKAN STOK BAHAN TERBARU
            </h2>

            @if ($movements->count() > 0)
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase text-ink">
                            <tr>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Bahan Baku</th>
                                <th class="px-4 py-3">Jenis</th>
                                <th class="px-4 py-3 text-right">Jumlah</th>
                                <th class="px-4 py-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-divider">
                            @foreach ($movements as $mv)
                                <tr>
                                    <td class="px-4 py-3 text-xs text-ink-secondary">
                                        {{ $mv->created_at?->translatedFormat('d M Y, H:i') }} WITA
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-ink">
                                        {{ $mv->ingredient?->nama }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($mv->jenis === \App\Enums\StockMovementType::Masuk)
                                            <span class="ayi-badge-selesai">Stok Masuk</span>
                                        @elseif ($mv->jenis === \App\Enums\StockMovementType::Keluar)
                                            <span class="ayi-badge-diproses">Stok Keluar</span>
                                        @else
                                            <span class="ayi-badge-nonaktif">Penyesuaian</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-heading font-bold text-ink tabular-nums">
                                        {{ (float) $mv->jumlah }} {{ $mv->ingredient?->satuan }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-ink-secondary">
                                        {{ $mv->keterangan }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="mt-3 text-sm text-ink-secondary">Belum ada riwayat pergerakan stok bahan.</p>
            @endif
        </div>
    </section>
</x-layouts.panel>

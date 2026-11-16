<x-layouts.panel title="Tambah Menu Baru">
    <nav aria-label="Breadcrumb" class="mb-4 text-xs font-semibold text-ink-secondary">
        <ol class="flex items-center gap-2">
            <li><a href="{{ route('outlet.menus.index') }}" class="text-navy underline hover:text-chili">Menu Outlet</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-ink">Tambah Menu Baru</li>
        </ol>
    </nav>

    <div class="ayi-card max-w-2xl p-6 sm:p-8" data-reveal="up">
        <h1 class="font-heading text-[28px] font-bold text-ink">
            TAMBAH MENU BARU — {{ strtoupper($outlet->nama) }}
        </h1>
        <p class="mt-1 text-sm text-ink-secondary">
            Sesuai model franchise (b), penentuan menu, kategori, harga, dan stok porsi sepenuhnya menjadi kewenangan kantin mitra Anda.
        </p>

        <form action="{{ route('outlet.menus.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="nama" class="block font-heading text-sm font-bold text-ink">Nama Menu</label>
                <input
                    id="nama"
                    type="text"
                    name="nama"
                    value="{{ old('nama') }}"
                    required
                    placeholder="Contoh: Ayam Geprek Sambal Korek + Nasi"
                    class="ayi-input mt-1.5"
                >
                @error('nama')
                    <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label for="category_id" class="block font-heading text-sm font-bold text-ink">Kategori</label>
                    <select id="category_id" name="category_id" required class="ayi-input mt-1.5">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>
                                {{ $category->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="harga" class="block font-heading text-sm font-bold text-ink">Harga (Rp)</label>
                    <input
                        id="harga"
                        type="number"
                        name="harga"
                        min="1000"
                        step="500"
                        value="{{ old('harga', 15000) }}"
                        required
                        class="ayi-input mt-1.5 tabular-nums"
                    >
                    @error('harga')
                        <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stok_porsi" class="block font-heading text-sm font-bold text-ink">Stok Porsi Hari Ini</label>
                    <input
                        id="stok_porsi"
                        type="number"
                        name="stok_porsi"
                        min="0"
                        value="{{ old('stok_porsi', 25) }}"
                        required
                        class="ayi-input mt-1.5 tabular-nums"
                    >
                    @error('stok_porsi')
                        <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="deskripsi" class="block font-heading text-sm font-bold text-ink">Deskripsi Singkat Menu</label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="3"
                    placeholder="Jelaskan porsi, pilihan sambal/topping, atau keunikan menu..."
                    class="ayi-input mt-1.5"
                >{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="foto" class="block font-heading text-sm font-bold text-ink">Foto Menu (Opsional, Maks 2MB)</label>
                <input
                    id="foto"
                    type="file"
                    name="foto"
                    accept="image/jpeg,image/png,image/webp"
                    class="ayi-input mt-1.5 text-xs"
                >
                <p class="mt-1 text-xs text-ink-muted">Bila dikosongkan, sistem menampilkan kartu placeholder khas Ayiriz.</p>
                @error('foto')
                    <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                @enderror
            </div>

            {{-- Toggle Tersedia (Motion-Ayiriz.md §11) --}}
            <div x-data="{ enabled: true }" class="flex items-center justify-between rounded-[12px] bg-surface-cream p-4">
                <div>
                    <span class="font-heading text-sm font-bold text-ink">Tampilkan Menu sebagai Tersedia</span>
                    <p class="text-xs text-ink-secondary">Menu yang tidak tersedia atau stok porsinya 0 tidak bisa dipesan pelanggan.</p>
                </div>
                <input type="hidden" name="tersedia" :value="enabled ? 1 : 0">
                <button
                    type="button"
                    role="switch"
                    :aria-checked="enabled"
                    @click="enabled = !enabled"
                    class="relative h-7 w-12 rounded-full transition-colors duration-200"
                    :class="enabled ? 'bg-chili' : 'bg-border-ayi'"
                >
                    <span
                        class="absolute top-1 left-0 h-5 w-5 rounded-full bg-white shadow-ayi-xs transition-transform duration-200"
                        :class="enabled ? 'translate-x-6' : 'translate-x-1'"
                    ></span>
                </button>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3 border-t border-divider pt-5">
                <a href="{{ route('outlet.menus.index') }}" class="ayi-btn-secondary">Batal</a>
                <button type="submit" class="ayi-btn-primary">Simpan Menu</button>
            </div>
        </form>
    </div>
</x-layouts.panel>

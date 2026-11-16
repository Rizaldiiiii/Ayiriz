<x-layouts.panel :title="'Edit Menu ' . $menu->nama">
    <nav aria-label="Breadcrumb" class="mb-4 text-xs font-semibold text-ink-secondary">
        <ol class="flex items-center gap-2">
            <li><a href="{{ route('outlet.menus.index') }}" class="text-navy underline hover:text-chili">Menu Outlet</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-ink">{{ $menu->nama }}</li>
        </ol>
    </nav>

    <div class="ayi-card max-w-2xl p-6 sm:p-8" data-reveal="up">
        <h1 class="font-heading text-[28px] font-bold text-ink">
            EDIT MENU: {{ strtoupper($menu->nama) }}
        </h1>
        <p class="mt-1 text-sm text-ink-secondary">
            Mengubah harga menu hanya berlaku untuk pesanan baru dan tidak mengubah harga pada pesanan yang sudah dicheckout sebelumnya.
        </p>

        <form action="{{ route('outlet.menus.update', $menu) }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="nama" class="block font-heading text-sm font-bold text-ink">Nama Menu</label>
                <input
                    id="nama"
                    type="text"
                    name="nama"
                    value="{{ old('nama', $menu->nama) }}"
                    required
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
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $menu->category_id) === (int) $category->id)>
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
                        value="{{ old('harga', $menu->harga) }}"
                        required
                        class="ayi-input mt-1.5 tabular-nums"
                    >
                    @error('harga')
                        <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stok_porsi" class="block font-heading text-sm font-bold text-ink">Stok Porsi</label>
                    <input
                        id="stok_porsi"
                        type="number"
                        name="stok_porsi"
                        min="0"
                        value="{{ old('stok_porsi', $menu->stok_porsi) }}"
                        required
                        class="ayi-input mt-1.5 tabular-nums"
                    >
                    @error('stok_porsi')
                        <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="deskripsi" class="block font-heading text-sm font-bold text-ink">Deskripsi Singkat</label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="3"
                    class="ayi-input mt-1.5"
                >{{ old('deskripsi', $menu->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="foto" class="block font-heading text-sm font-bold text-ink">Ganti Foto Menu (Opsional)</label>
                <input
                    id="foto"
                    type="file"
                    name="foto"
                    accept="image/jpeg,image/png,image/webp"
                    class="ayi-input mt-1.5 text-xs"
                >
                @error('foto')
                    <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ enabled: {{ old('tersedia', $menu->tersedia) ? 'true' : 'false' }} }" class="flex items-center justify-between rounded-[12px] bg-surface-cream p-4">
                <div>
                    <span class="font-heading text-sm font-bold text-ink">Status Menu Tersedia</span>
                    <p class="text-xs text-ink-secondary">Nonaktifkan bila menu sedang tidak dijual hari ini.</p>
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
                <button type="submit" class="ayi-btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-layouts.panel>

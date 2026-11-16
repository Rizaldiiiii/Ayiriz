<x-layouts.panel title="Daftarkan Kantin Mitra Baru">
    <nav aria-label="Breadcrumb" class="mb-4 text-xs font-semibold text-ink-secondary">
        <ol class="flex items-center gap-2">
            <li><a href="{{ route('pusat.outlets.index') }}" class="text-navy underline hover:text-chili">Manajemen Outlet</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-ink">Daftarkan Kantin Mitra Baru</li>
        </ol>
    </nav>

    <div class="ayi-card max-w-3xl p-6 sm:p-8" data-reveal="up">
        <h1 class="font-heading text-[28px] font-bold text-ink">
            DAFTARKAN KANTIN MITRA BARU (KF-01)
        </h1>
        <p class="mt-1 text-sm text-ink-secondary">
            Outlet baru otomatis berstatus <strong>Belum Terverifikasi</strong> (tidak tampil di katalog publik sampai diverifikasi Pusat) dan memiliki bagi hasil royalti awal <strong>10%</strong>.
        </p>

        <form action="{{ route('pusat.outlets.store') }}" method="POST" class="mt-6 space-y-5">
            @csrf

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="nama" class="block font-heading text-sm font-bold text-ink">
                        Nama Kantin / Outlet Mitra
                    </label>
                    <input
                        id="nama"
                        type="text"
                        name="nama"
                        value="{{ old('nama') }}"
                        required
                        placeholder="Contoh: Ayiriz Bakar & Sambal"
                        class="ayi-input mt-1.5"
                    >
                    @error('nama')
                        <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pemilik" class="block font-heading text-sm font-bold text-ink">
                        Nama Pemilik UMKM
                    </label>
                    <input
                        id="pemilik"
                        type="text"
                        name="pemilik"
                        value="{{ old('pemilik') }}"
                        required
                        placeholder="Contoh: Ibu Rahmawati"
                        class="ayi-input mt-1.5"
                    >
                    @error('pemilik')
                        <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="no_hp" class="block font-heading text-sm font-bold text-ink">
                        Nomor HP / WhatsApp Kasir
                    </label>
                    <input
                        id="no_hp"
                        type="text"
                        name="no_hp"
                        value="{{ old('no_hp') }}"
                        required
                        placeholder="0812-4455-6677"
                        class="ayi-input mt-1.5"
                    >
                    @error('no_hp')
                        <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jam_buka" class="block font-heading text-sm font-bold text-ink">
                        Jam Buka Operasional
                    </label>
                    <input
                        id="jam_buka"
                        type="text"
                        name="jam_buka"
                        value="{{ old('jam_buka', 'Senin–Sabtu, 08:00–17:00 WITA') }}"
                        required
                        placeholder="Senin–Sabtu, 08:00–17:00 WITA"
                        class="ayi-input mt-1.5"
                    >
                    @error('jam_buka')
                        <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="alamat" class="block font-heading text-sm font-bold text-ink">
                    Alamat / Lokasi (Depan Gerbang Kampus UNG)
                </label>
                <input
                    id="alamat"
                    type="text"
                    name="alamat"
                    value="{{ old('alamat', 'Depan Gerbang Utama Kampus UNG, Jl. Jend. Sudirman, Kota Gorontalo') }}"
                    required
                    class="ayi-input mt-1.5"
                >
                @error('alamat')
                    <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-[12px] border border-border-ayi bg-surface-cream p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-heading text-lg font-bold text-navy">
                        Akun Login Admin Outlet & Persen Royalti Awal
                    </h2>
                    <span class="rounded-full bg-tint-navy px-3 py-1 font-heading text-xs font-bold text-navy">
                        Royalti Awal: 10%
                    </span>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="email" class="block font-heading text-sm font-bold text-ink">
                            Email Login Admin Outlet
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="outlet4@ayiriz.local"
                            class="ayi-input mt-1.5"
                        >
                        @error('email')
                            <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block font-heading text-sm font-bold text-ink">
                            Kata Sandi Awal
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            placeholder="Minimal 6 karakter"
                            class="ayi-input mt-1.5"
                        >
                        @error('password')
                            <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3 border-t border-divider pt-5">
                <a href="{{ route('pusat.outlets.index') }}" class="ayi-btn-secondary">
                    Batal
                </a>
                <button type="submit" class="ayi-btn-primary">
                    Simpan & Daftarkan Outlet
                </button>
            </div>
        </form>
    </div>
</x-layouts.panel>

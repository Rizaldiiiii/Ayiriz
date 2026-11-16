<x-layouts.panel :title="'Edit ' . $outlet->nama">
    <nav aria-label="Breadcrumb" class="mb-4 text-xs font-semibold text-ink-secondary">
        <ol class="flex items-center gap-2">
            <li><a href="{{ route('pusat.outlets.index') }}" class="text-navy underline hover:text-chili">Manajemen Outlet</a></li>
            <li aria-hidden="true">/</li>
            <li class="text-ink">{{ $outlet->nama }}</li>
        </ol>
    </nav>

    <div class="grid gap-6 lg:grid-cols-12 lg:items-start">
        <div class="ayi-card p-6 sm:p-8 lg:col-span-8" data-reveal="up">
            <h1 class="font-heading text-[28px] font-bold text-ink">
                PERBARUI DATA OUTLET: {{ strtoupper($outlet->nama) }}
            </h1>
            <p class="mt-1 text-sm text-ink-secondary">
                Sesuai model franchise (b), Pusat hanya mengelola profil & status verifikasi kantin mitra, tanpa mengubah menu atau harga milik outlet.
            </p>

            <form action="{{ route('pusat.outlets.update', $outlet) }}" method="POST" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="nama" class="block font-heading text-sm font-bold text-ink">Nama Kantin / Outlet</label>
                        <input id="nama" type="text" name="nama" value="{{ old('nama', $outlet->nama) }}" required class="ayi-input mt-1.5">
                        @error('nama')
                            <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pemilik" class="block font-heading text-sm font-bold text-ink">Nama Pemilik UMKM</label>
                        <input id="pemilik" type="text" name="pemilik" value="{{ old('pemilik', $outlet->pemilik) }}" required class="ayi-input mt-1.5">
                        @error('pemilik')
                            <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="no_hp" class="block font-heading text-sm font-bold text-ink">Nomor HP</label>
                        <input id="no_hp" type="text" name="no_hp" value="{{ old('no_hp', $outlet->no_hp) }}" required class="ayi-input mt-1.5">
                        @error('no_hp')
                            <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jam_buka" class="block font-heading text-sm font-bold text-ink">Jam Buka</label>
                        <input id="jam_buka" type="text" name="jam_buka" value="{{ old('jam_buka', $outlet->jam_buka) }}" required class="ayi-input mt-1.5">
                        @error('jam_buka')
                            <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="alamat" class="block font-heading text-sm font-bold text-ink">Alamat / Lokasi</label>
                    <input id="alamat" type="text" name="alamat" value="{{ old('alamat', $outlet->alamat) }}" required class="ayi-input mt-1.5">
                    @error('alamat')
                        <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">● {{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3 border-t border-divider pt-5">
                    <a href="{{ route('pusat.outlets.index') }}" class="ayi-btn-secondary">Kembali</a>
                    <button type="submit" class="ayi-btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>

        {{-- Panel Status & Verifikasi --}}
        <aside class="ayi-card bg-surface-cream p-6 lg:col-span-4">
            <h2 class="font-heading text-xl font-bold text-ink">Status & Royalti</h2>

            <div class="mt-3">
                <x-status-badge :status="$outlet->status" type="outlet" />
            </div>

            <dl class="mt-4 space-y-2 border-t border-divider pt-4 text-xs">
                <div class="flex justify-between">
                    <dt class="text-ink-secondary">Akun Login:</dt>
                    <dd class="font-bold text-ink">{{ $outlet->user?->email }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-ink-secondary">Persen Royalti Saat Ini:</dt>
                    <dd class="font-heading text-base font-bold text-chili">{{ number_format((float) $outlet->royalty_percent, 2, ',', '.') }}%</dd>
                </div>
            </dl>
            <p class="mt-2 text-[11px] text-ink-muted">
                Sesuai ERD §2.2, persen royalti awal adalah 10% dan perubahan royalti hanya dilakukan melalui Audit Royalti Semester (KF-10, Tahap 3).
            </p>

            <form action="{{ route('pusat.outlets.status', $outlet) }}" method="POST" class="mt-5 space-y-3 border-t border-divider pt-4">
                @csrf
                @method('PATCH')

                <label for="status-select" class="block font-heading text-xs font-bold uppercase tracking-wider text-ink">
                    Ubah Status Verifikasi / Aktif
                </label>
                <select id="status-select" name="status" class="ayi-input">
                    <option value="terverifikasi" @selected($outlet->status === \App\Enums\OutletStatus::Terverifikasi)>
                        Aktif (Terverifikasi)
                    </option>
                    <option value="belum_terverifikasi" @selected($outlet->status === \App\Enums\OutletStatus::BelumTerverifikasi)>
                        Belum Terverifikasi
                    </option>
                    <option value="nonaktif" @selected($outlet->status === \App\Enums\OutletStatus::Nonaktif)>
                        Nonaktif
                    </option>
                </select>

                <button type="submit" class="ayi-btn-secondary w-full">
                    Perbarui Status Outlet
                </button>
            </form>
        </aside>
    </div>
</x-layouts.panel>

<x-layouts.public title="Daftar Akun Pelanggan">
    <section class="mx-auto max-w-xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="ayi-card p-6 sm:p-8" data-reveal="up">
            <div>
                <a href="{{ route('catalog.index') }}" class="font-display-brand text-[42px] leading-none text-chili">
                    Ayiriz
                </a>
                <p class="mt-2 font-heading text-xs font-bold uppercase tracking-wider text-navy">
                    Sistem Informasi Pengelolaan UMKM Lokal Ayiriz
                </p>
                <h1 class="mt-3 font-heading text-[28px] font-bold text-ink">
                    Daftar Akun Pelanggan
                </h1>
                <p class="mt-1 text-sm text-ink-secondary">
                    Pesan makanan dan minuman dari kantin mitra di depan gerbang kampus UNG tanpa antre panjang. Bayar langsung di kasir saat ambil pesanan.
                </p>
            </div>

            <form action="{{ route('register.store') }}" method="POST" class="mt-6 space-y-5" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                <div>
                    <label for="name" class="block font-heading text-sm font-bold text-ink">
                        Nama Lengkap Mahasiswa / Pelanggan
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Contoh: Aldi Pratama"
                        class="ayi-input mt-1.5"
                    >
                    @error('name')
                        <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">
                            ● {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block font-heading text-sm font-bold text-ink">
                        Alamat Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        placeholder="mahasiswa@ung.ac.id"
                        class="ayi-input mt-1.5"
                    >
                    @error('email')
                        <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">
                            ● {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="block font-heading text-sm font-bold text-ink">
                            Kata Sandi
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Minimal 6 karakter"
                            class="ayi-input mt-1.5"
                        >
                        @error('password')
                            <p class="mt-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">
                                ● {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block font-heading text-sm font-bold text-ink">
                            Ulangi Kata Sandi
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ketik ulang kata sandi"
                            class="ayi-input mt-1.5"
                        >
                    </div>
                </div>

                <button type="submit" :disabled="loading" class="ayi-btn-primary w-full">
                    <span x-show="!loading">Buat Akun Pelanggan</span>
                    <span x-cloak x-show="loading" class="flex items-center gap-2">
                        <span class="loading-spinner"></span>
                        <span>Mendaftarkan...</span>
                    </span>
                </button>
            </form>

            <div class="mt-6 border-t border-divider pt-5">
                <p class="text-sm text-ink-secondary">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-bold text-chili underline decoration-chili/40 underline-offset-4 hover:text-chili-hover">
                        Masuk di sini
                    </a>
                </p>
            </div>
        </div>
    </section>
</x-layouts.public>

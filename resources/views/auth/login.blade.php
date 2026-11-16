<x-layouts.public title="Masuk Akun">
    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-12 lg:items-start">
            {{-- Form Login --}}
            <div class="ayi-card p-6 sm:p-8 lg:col-span-7" data-reveal="up">
                <div class="text-center sm:text-left">
                    <a href="{{ route('catalog.index') }}" class="font-display-brand text-[44px] leading-none text-chili">
                        Ayiriz
                    </a>
                    <p class="mt-2 font-heading text-sm font-bold uppercase tracking-wider text-navy">
                        Sistem Informasi Pengelolaan UMKM Lokal Ayiriz
                    </p>
                    <h1 class="mt-3 font-heading text-[28px] font-bold text-ink">
                        Masuk ke Akun Anda
                    </h1>
                    <p class="mt-1 text-sm text-ink-secondary">
                        Satu pintu masuk untuk Pelanggan (Mahasiswa), Admin Outlet (Kantin Mitra), dan Pusat Ayiriz.
                    </p>
                </div>

                <form action="{{ route('login.store') }}" method="POST" class="mt-6 space-y-5" x-data="{ loading: false }" @submit="loading = true">
                    @csrf

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
                            autofocus
                            autocomplete="email"
                            placeholder="nama@ayiriz.local"
                            class="ayi-input mt-1.5"
                            @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        >
                        @error('email')
                            <p id="email-error" class="mt-1.5 flex items-center gap-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">
                                <span>●</span>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block font-heading text-sm font-bold text-ink">
                            Kata Sandi
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Masukkan kata sandi"
                            class="ayi-input mt-1.5"
                            @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                        >
                        @error('password')
                            <p id="password-error" class="mt-1.5 flex items-center gap-1.5 rounded-[8px] bg-error-bg px-3 py-2 text-xs font-semibold text-error">
                                <span>●</span>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-ink-secondary">
                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                class="h-4 w-4 rounded-[6px] border-border-strong text-chili focus:ring-chili"
                            >
                            <span>Ingat sesi saya</span>
                        </label>
                    </div>

                    <button type="submit" :disabled="loading" class="ayi-btn-primary w-full">
                        <span x-show="!loading">Masuk Sekarang</span>
                        <span x-cloak x-show="loading" class="flex items-center gap-2">
                            <span class="loading-spinner"></span>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </form>

                <div class="mt-6 border-t border-divider pt-5 text-center sm:text-left">
                    <p class="text-sm text-ink-secondary">
                        Mahasiswa belum punya akun pelanggan?
                        <a href="{{ route('register') }}" class="font-bold text-chili underline decoration-chili/40 underline-offset-4 hover:text-chili-hover">
                            Daftar Akun Pelanggan
                        </a>
                    </p>
                </div>
            </div>

            {{-- Panel Info Akun Contoh (Data Contoh Seeder PRD §6) --}}
            <aside class="ayi-card bg-surface-cream p-6 lg:col-span-5" data-reveal="up" style="--reveal-delay: 100ms">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="font-heading text-[21px] font-bold text-navy">
                        Akun Demo (Seeder Fiktif)
                    </h2>
                    <span class="ayi-chip-sample">Data Contoh</span>
                </div>
                <p class="mt-1.5 text-xs leading-relaxed text-ink-secondary">
                    Gunakan akun contoh berikut untuk menguji alur end-to-end Tahap 1 (Pesan Online → Kasir Terima Bayar → Selesai). Klik tombol <strong>Isi</strong> untuk menyalin ke form login:
                </p>

                <div class="mt-4 space-y-3 text-xs">
                    <div class="rounded-[12px] border border-border-ayi bg-surface p-3.5">
                        <div class="flex items-center justify-between">
                            <span class="font-heading text-sm font-bold text-navy">Pusat Ayiriz (Owner Brand)</span>
                            <button
                                type="button"
                                onclick="document.getElementById('email').value='pusat@ayiriz.local';document.getElementById('password').value='Pusat123!';"
                                class="rounded-full bg-tint-navy px-3 py-1 font-heading text-xs font-bold text-navy hover:bg-navy hover:text-white transition"
                            >
                                Isi
                            </button>
                        </div>
                        <p class="mt-1 text-ink-secondary"><code>pusat@ayiriz.local</code> / <code>Pusat123!</code></p>
                    </div>

                    <div class="rounded-[12px] border border-border-ayi bg-surface p-3.5">
                        <div class="flex items-center justify-between">
                            <span class="font-heading text-sm font-bold text-chili">Admin Outlet 1 — Ayiriz Geprek</span>
                            <button
                                type="button"
                                onclick="document.getElementById('email').value='outlet1@ayiriz.local';document.getElementById('password').value='Outlet123!';"
                                class="rounded-full bg-tint-chili px-3 py-1 font-heading text-xs font-bold text-chili hover:bg-chili hover:text-white transition"
                            >
                                Isi
                            </button>
                        </div>
                        <p class="mt-1 text-ink-secondary"><code>outlet1@ayiriz.local</code> / <code>Outlet123!</code></p>
                        <p class="mt-1 text-[11px] text-ink-muted">Juga tersedia: <code>outlet2@ayiriz.local</code>, <code>outlet3@ayiriz.local</code> (sandi sama)</p>
                    </div>

                    <div class="rounded-[12px] border border-border-ayi bg-surface p-3.5">
                        <div class="flex items-center justify-between">
                            <span class="font-heading text-sm font-bold text-leaf">Pelanggan 1 (Mahasiswa UNG)</span>
                            <button
                                type="button"
                                onclick="document.getElementById('email').value='pelanggan1@ayiriz.local';document.getElementById('password').value='Pelanggan123!';"
                                class="rounded-full bg-tint-leaf px-3 py-1 font-heading text-xs font-bold text-leaf hover:bg-leaf hover:text-white transition"
                            >
                                Isi
                            </button>
                        </div>
                        <p class="mt-1 text-ink-secondary"><code>pelanggan1@ayiriz.local</code> / <code>Pelanggan123!</code></p>
                        <p class="mt-1 text-[11px] text-ink-muted">Juga tersedia: <code>pelanggan2@ayiriz.local</code> (sandi sama)</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.public>

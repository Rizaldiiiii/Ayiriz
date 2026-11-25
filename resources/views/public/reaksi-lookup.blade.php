<x-layouts.public title="Beri Reaksi Emote Pesanan">
    <section class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="ayi-card p-6 sm:p-8" data-reveal="up">
            <div class="ayi-accent-bar mb-2 w-12"></div>
            <h1 class="font-heading text-[32px] font-bold text-ink">
                BERI REAKSI EMOTE PESANAN (KF-07)
            </h1>
            <p class="mt-1 text-sm text-ink-secondary">
                Masukkan kode pesanan yang sudah Anda bayar tunai di kasir (atau pindai QR reaksi di layar kasir) untuk memberikan kesan terhadap menu yang Anda pesan — tanpa perlu daftar atau login.
            </p>

            <form action="{{ route('catalog.reaksi.lookup') }}" method="GET" class="mt-6 space-y-4">
                <div>
                    <label for="kode-pesanan" class="block font-heading text-sm font-bold text-ink">
                        Kode Pesanan
                    </label>
                    <input
                        id="kode-pesanan"
                        type="text"
                        name="kode"
                        required
                        placeholder="Contoh: AYZ-20261116-0001"
                        class="ayi-input mt-1.5 font-heading text-base uppercase"
                    >
                    <p class="mt-1 text-xs text-ink-muted">
                        Kode pesanan tertera pada struk kasir atau kartu bukti antrean Anda setelah dikonfirmasi bayar.
                    </p>
                </div>

                <button type="submit" class="ayi-btn-primary w-full">
                    Buka Halaman Reaksi Pesanan
                </button>
            </form>

            {{-- Arti Emote Dikunci (PRD §2 & KF-07) --}}
            <div class="mt-6 rounded-[12px] border border-border-ayi bg-surface-cream p-4">
                <p class="font-heading text-xs font-bold uppercase tracking-wider text-navy">
                    Arti Pilihan Reaksi Emote Ayiriz:
                </p>
                <div class="mt-3 grid gap-2.5 sm:grid-cols-3 text-xs">
                    <div class="rounded-[10px] bg-tint-chili p-3 text-ink">
                        <span class="text-xl" aria-hidden="true">😋</span>
                        <p class="mt-1 font-heading text-sm font-bold text-ink">Enak</p>
                        <p class="text-ink-secondary">Rasa gurih / nikmat cocok di lidah.</p>
                    </div>
                    <div class="rounded-[10px] bg-tint-tang p-3 text-ink">
                        <span class="text-xl" aria-hidden="true">🥵</span>
                        <p class="mt-1 font-heading text-sm font-bold text-ink">Pedas</p>
                        <p class="text-ink-secondary">Sensasi sambal / bumbu pedas mantap.</p>
                    </div>
                    <div class="rounded-[10px] bg-tint-sunny p-3 text-ink">
                        <span class="text-xl" aria-hidden="true">🫠</span>
                        <p class="mt-1 font-heading text-sm font-bold text-ink">Manis / menyejukkan</p>
                        <p class="text-ink-secondary">Cocok untuk minuman, es, & dessert dingin.</p>
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-ink-muted">
                    Reaksi dibaca sebagai kesan terhadap satu menu, bukan skor dan bukan peringkat antar kantin.
                </p>
            </div>
        </div>
    </section>
</x-layouts.public>

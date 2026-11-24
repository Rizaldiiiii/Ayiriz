<x-layouts.public :title="'Reaksi Pesanan ' . $kode">
    <section class="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="ayi-card p-8 text-center sm:p-10" data-reveal="up">
            <div class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full bg-tint-sunny text-warning">
                <span class="text-2xl" aria-hidden="true">😋</span>
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                <span class="rounded-full bg-tint-navy px-3 py-1 font-heading text-xs font-bold text-navy">
                    KF-07 · Kode {{ $kode }}
                </span>
                <span class="rounded-full bg-warning-bg px-3 py-1 font-heading text-xs font-bold text-warning">
                    Belum Tersedia di Tahap Ini (Tahap 2)
                </span>
            </div>

            <h1 class="mt-3 font-heading text-[28px] font-bold text-ink">
                Halaman Reaksi Emote Pelanggan
            </h1>

            <p class="mx-auto mt-2 max-w-xl text-sm leading-relaxed text-ink-secondary">
                Pemberian reaksi emote (<strong>😋 Enak</strong>, <strong>🥵 Pedas</strong>, <strong>🫠 Manis / menyejukkan</strong>) untuk pesanan <strong>{{ $kode }}</strong> yang sudah dibayar tunai di kasir dijadwalkan pada <strong>Tahap 2 — Operasional Outlet</strong>.
            </p>

            <div class="mt-6">
                <a href="{{ route('catalog.index') }}" class="ayi-btn-primary">
                    ← Kembali ke Katalog Kantin
                </a>
            </div>
        </div>
    </section>
</x-layouts.public>

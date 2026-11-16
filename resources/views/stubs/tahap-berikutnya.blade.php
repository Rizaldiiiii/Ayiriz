<x-layouts.panel :title="$kodeFitur . ' — ' . $namaFitur">
    <div class="ayi-card p-8 text-center sm:p-10" data-reveal="up">
        <div class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full bg-tint-sunny text-warning">
            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="12" cy="12" r="9" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
            </svg>
        </div>

        <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
            <span class="rounded-full bg-tint-navy px-3 py-1 font-heading text-xs font-bold text-navy">
                {{ $kodeFitur }}
            </span>
            <span class="rounded-full bg-warning-bg px-3 py-1 font-heading text-xs font-bold text-warning">
                Belum Tersedia di Tahap Ini ({{ $tahap }})
            </span>
        </div>

        <h1 class="mt-3 font-heading text-[28px] font-bold text-ink">
            {{ $namaFitur }}
        </h1>

        <p class="mx-auto mt-2 max-w-xl text-sm leading-relaxed text-ink-secondary">
            {{ $deskripsi }}
        </p>

        <div class="mt-6">
            <a href="{{ $backUrl }}" class="ayi-btn-primary">
                ← {{ $backLabel }}
            </a>
        </div>
    </div>
</x-layouts.panel>

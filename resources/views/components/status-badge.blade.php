@props(['status', 'type' => 'order'])

@php
    $value = is_object($status) && property_exists($status, 'value') ? $status->value : (string) $status;
@endphp

@if ($type === 'order')
    @if ($value === 'selesai')
        <span class="ayi-badge-selesai">
            <span class="h-2 w-2 rounded-full bg-leaf" aria-hidden="true"></span>
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
            </svg>
            <span>Selesai</span>
        </span>
    @else
        <span class="ayi-badge-diproses">
            <span class="h-2 w-2 rounded-full bg-tang" aria-hidden="true"></span>
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <circle cx="12" cy="12" r="9" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" />
            </svg>
            <span>Diproses</span>
        </span>
    @endif
@else
    @if ($value === 'terverifikasi')
        <span class="ayi-badge-selesai">
            <span class="h-2 w-2 rounded-full bg-leaf" aria-hidden="true"></span>
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
            </svg>
            <span>Aktif (Terverifikasi)</span>
        </span>
    @elseif ($value === 'belum_terverifikasi')
        <span class="inline-flex items-center gap-1.5 rounded-full bg-warning-bg px-3 py-1 font-heading text-[13px] font-bold text-warning">
            <span class="h-2 w-2 rounded-full bg-sunny" aria-hidden="true"></span>
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <circle cx="12" cy="12" r="9" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01" />
            </svg>
            <span>Belum Terverifikasi</span>
        </span>
    @else
        <span class="ayi-badge-nonaktif">
            <span class="h-2 w-2 rounded-full bg-ink-muted" aria-hidden="true"></span>
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <circle cx="12" cy="12" r="9" />
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 9-6 6m0-6 6 6" />
            </svg>
            <span>Nonaktif</span>
        </span>
    @endif
@endif

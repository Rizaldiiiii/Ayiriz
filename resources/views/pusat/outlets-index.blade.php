<x-layouts.panel title="Manajemen Outlet (KF-01)">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal="up">
        <div>
            <div class="ayi-accent-bar mb-2 w-12"></div>
            <h1 class="font-heading text-[32px] font-bold text-ink">
                MANAJEMEN OUTLET MITRA (KF-01)
            </h1>
            <p class="text-sm text-ink-secondary">
                Daftarkan kantin mitra baru, verifikasi outlet sebelum tampil di katalog publik, atau ubah status Aktif/Nonaktif.
            </p>
        </div>

        <a href="{{ route('pusat.outlets.create') }}" class="ayi-btn-primary">
            + Daftarkan Kantin Mitra Baru
        </a>
    </div>

    {{-- Tabel Desktop --}}
    <div class="ayi-card mt-6 hidden overflow-hidden md:block">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase tracking-wider text-ink">
                <tr>
                    <th class="px-5 py-3.5">Nama Outlet & Lokasi</th>
                    <th class="px-5 py-3.5">Pemilik & Akun</th>
                    <th class="px-5 py-3.5">Jam Buka</th>
                    <th class="px-5 py-3.5">Status Katalog</th>
                    <th class="px-5 py-3.5 text-right">Penjualan & Royalti</th>
                    <th class="px-5 py-3.5 text-right">Verifikasi & Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-divider">
                @forelse ($outlets as $outlet)
                    <tr class="hover:bg-surface-cream">
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="font-heading text-lg font-bold text-ink">{{ $outlet->nama }}</span>
                                @if ($outlet->is_sample)
                                    <span class="ayi-chip-sample">Data Contoh</span>
                                @endif
                            </div>
                            <p class="text-xs text-ink-secondary">{{ $outlet->alamat }}</p>
                            <p class="mt-0.5 text-[11px] text-ink-muted">Slug: <code>/outlet/{{ $outlet->slug }}</code> · {{ $outlet->menus_count }} menu</p>
                        </td>
                        <td class="px-5 py-4 text-xs">
                            <p class="font-bold text-ink">{{ $outlet->pemilik }}</p>
                            <p class="text-ink-secondary">{{ $outlet->no_hp }}</p>
                            <p class="text-ink-muted">{{ $outlet->user?->email }}</p>
                        </td>
                        <td class="px-5 py-4 text-xs text-ink-secondary">
                            {{ $outlet->jam_buka }}
                        </td>
                        <td class="px-5 py-4">
                            <x-status-badge :status="$outlet->status" type="outlet" />
                        </td>
                        <td class="px-5 py-4 text-right tabular-nums">
                            <div class="font-heading text-base font-bold text-ink">
                                Rp {{ number_format((int) $outlet->total_penjualan, 0, ',', '.') }}
                            </div>
                            <div class="text-xs font-semibold text-chili">
                                Royalti ({{ number_format((float) $outlet->royalty_percent, 0) }}%): Rp {{ number_format((int) $outlet->total_royalti, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                @if ($outlet->status === \App\Enums\OutletStatus::BelumTerverifikasi)
                                    <form action="{{ route('pusat.outlets.status', $outlet) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="terverifikasi">
                                        <button
                                            type="submit"
                                            class="rounded-full bg-leaf px-3.5 py-2 font-heading text-xs font-bold text-white shadow-ayi-xs hover:opacity-90 transition"
                                        >
                                            Verifikasi (Aktifkan)
                                        </button>
                                    </form>
                                @elseif ($outlet->status === \App\Enums\OutletStatus::Terverifikasi)
                                    <form action="{{ route('pusat.outlets.status', $outlet) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="nonaktif">
                                        <button
                                            type="submit"
                                            class="rounded-full bg-tint-chili px-3.5 py-2 font-heading text-xs font-bold text-chili hover:bg-chili hover:text-white transition"
                                        >
                                            Nonaktifkan
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('pusat.outlets.status', $outlet) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="terverifikasi">
                                        <button
                                            type="submit"
                                            class="rounded-full bg-tint-leaf px-3.5 py-2 font-heading text-xs font-bold text-leaf hover:bg-leaf hover:text-white transition"
                                        >
                                            Aktifkan Kembali
                                        </button>
                                    </form>
                                @endif

                                <a
                                    href="{{ route('pusat.outlets.edit', $outlet) }}"
                                    class="ayi-btn-secondary min-h-[36px] px-3.5 py-1.5 text-xs"
                                >
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-sm text-ink-secondary">
                            Belum ada outlet yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Kartu Mobile --}}
    <div class="mt-6 space-y-4 md:hidden">
        @foreach ($outlets as $outlet)
            <article class="ayi-card p-5">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h2 class="font-heading text-lg font-bold text-ink">{{ $outlet->nama }}</h2>
                        <p class="text-xs text-ink-secondary">{{ $outlet->pemilik }} · {{ $outlet->no_hp }}</p>
                        <p class="text-xs text-ink-muted">{{ $outlet->alamat }}</p>
                    </div>
                    <x-status-badge :status="$outlet->status" type="outlet" />
                </div>

                <div class="mt-3 rounded-[10px] bg-surface-cream p-3 text-xs">
                    <div class="flex justify-between">
                        <span>Penjualan Selesai:</span>
                        <strong class="tabular-nums">Rp {{ number_format((int) $outlet->total_penjualan, 0, ',', '.') }}</strong>
                    </div>
                    <div class="mt-1 flex justify-between text-chili">
                        <span>Royalti ({{ number_format((float) $outlet->royalty_percent, 0) }}%):</span>
                        <strong class="tabular-nums">Rp {{ number_format((int) $outlet->total_royalti, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                    @if ($outlet->status !== \App\Enums\OutletStatus::Terverifikasi)
                        <form action="{{ route('pusat.outlets.status', $outlet) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="terverifikasi">
                            <button type="submit" class="rounded-full bg-leaf px-3.5 py-2 font-heading text-xs font-bold text-white">
                                Verifikasi / Aktifkan
                            </button>
                        </form>
                    @else
                        <form action="{{ route('pusat.outlets.status', $outlet) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="nonaktif">
                            <button type="submit" class="rounded-full bg-tint-chili px-3.5 py-2 font-heading text-xs font-bold text-chili">
                                Nonaktifkan
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('pusat.outlets.edit', $outlet) }}" class="ayi-btn-secondary min-h-[36px] px-3.5 py-1.5 text-xs">
                        Edit
                    </a>
                </div>
            </article>
        @endforeach
    </div>
</x-layouts.panel>

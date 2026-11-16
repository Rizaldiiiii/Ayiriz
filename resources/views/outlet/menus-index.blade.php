<x-layouts.panel :title="'Menu ' . $outlet->nama">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal="up">
        <div>
            <div class="ayi-accent-bar mb-2 w-12"></div>
            <h1 class="font-heading text-[32px] font-bold text-ink">
                MANAJEMEN MENU & STOK PORSI (KF-02)
            </h1>
            <p class="text-sm text-ink-secondary">
                Kelola daftar menu, harga, status tersedia, dan stok porsi harian untuk <strong>{{ $outlet->nama }}</strong>.
            </p>
        </div>

        <a href="{{ route('outlet.menus.create') }}" class="ayi-btn-primary">
            + Tambah Menu Baru
        </a>
    </div>

    {{-- Tabel Desktop --}}
    <div class="ayi-card mt-6 hidden overflow-hidden md:block">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase tracking-wider text-ink">
                <tr>
                    <th class="px-5 py-3.5">Menu</th>
                    <th class="px-5 py-3.5">Kategori</th>
                    <th class="px-5 py-3.5 text-right">Harga</th>
                    <th class="px-5 py-3.5 text-right">Stok Porsi</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-divider">
                @forelse ($menus as $menu)
                    <tr class="hover:bg-surface-cream">
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="font-heading text-lg font-bold text-ink">{{ $menu->nama }}</span>
                                @if ($menu->is_sample)
                                    <span class="ayi-chip-sample">Data Contoh</span>
                                @endif
                            </div>
                            @if ($menu->deskripsi)
                                <p class="mt-0.5 line-clamp-1 text-xs text-ink-secondary">{{ $menu->deskripsi }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="rounded-full bg-tint-navy px-2.5 py-1 font-heading text-xs font-bold text-navy">
                                {{ $menu->category?->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right font-heading text-lg font-bold text-chili tabular-nums">
                            {{ $menu->formattedHarga() }}
                        </td>
                        <td class="px-5 py-4 text-right font-heading text-base font-bold tabular-nums {{ $menu->stok_porsi > 0 ? 'text-ink' : 'text-error' }}">
                            {{ $menu->stok_porsi }} porsi
                        </td>
                        <td class="px-5 py-4">
                            @if ($menu->tersedia && $menu->stok_porsi > 0)
                                <span class="ayi-badge-selesai">
                                    <span class="h-2 w-2 rounded-full bg-leaf" aria-hidden="true"></span>
                                    <span>Tersedia</span>
                                </span>
                            @else
                                <span class="ayi-badge-nonaktif">
                                    <span class="h-2 w-2 rounded-full bg-ink-muted" aria-hidden="true"></span>
                                    <span>{{ ! $menu->tersedia ? 'Nonaktif' : 'Stok Habis (0)' }}</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('outlet.menus.edit', $menu) }}" class="ayi-btn-secondary min-h-[36px] px-3.5 py-1.5 text-xs">
                                    Edit
                                </a>

                                <form
                                    action="{{ route('outlet.menus.destroy', $menu) }}"
                                    method="POST"
                                    onsubmit="return confirm('Hapus menu {{ addslashes($menu->nama) }}?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="rounded-full bg-tint-chili px-3.5 py-1.5 font-heading text-xs font-bold text-chili hover:bg-chili hover:text-white transition"
                                    >
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-sm text-ink-secondary">
                            Belum ada menu untuk outlet ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Kartu Mobile --}}
    <div class="mt-6 space-y-4 md:hidden">
        @foreach ($menus as $menu)
            <article class="ayi-card p-5">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <span class="rounded-full bg-tint-navy px-2.5 py-0.5 font-heading text-[11px] font-bold text-navy">
                            {{ $menu->category?->nama }}
                        </span>
                        <h2 class="mt-1 font-heading text-lg font-bold text-ink">{{ $menu->nama }}</h2>
                        <p class="font-heading text-xl font-bold text-chili tabular-nums">{{ $menu->formattedHarga() }}</p>
                    </div>
                    <span class="font-heading text-sm font-bold tabular-nums {{ $menu->stok_porsi > 0 ? 'text-leaf' : 'text-error' }}">
                        Stok: {{ $menu->stok_porsi }}
                    </span>
                </div>

                <div class="mt-4 flex items-center justify-end gap-2 border-t border-divider pt-3">
                    <a href="{{ route('outlet.menus.edit', $menu) }}" class="ayi-btn-secondary min-h-[38px] px-4 py-1.5 text-xs">
                        Edit Menu
                    </a>
                    <form
                        action="{{ route('outlet.menus.destroy', $menu) }}"
                        method="POST"
                        onsubmit="return confirm('Hapus menu ini?');"
                    >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-full bg-tint-chili px-3.5 py-2 font-heading text-xs font-bold text-chili">
                            Hapus
                        </button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
</x-layouts.panel>

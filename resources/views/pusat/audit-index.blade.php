<x-layouts.panel title="Audit Royalti Semester (KF-10)">
    {{-- Header --}}
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal="up">
        <div>
            <div class="ayi-accent-bar mb-2 w-12"></div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="font-heading text-[32px] font-bold text-ink">
                    AUDIT ROYALTI SEMESTER (KF-10)
                </h1>
                <span class="ayi-chip-sample">Data Contoh</span>
            </div>
            <p class="mt-1 text-sm text-ink-secondary">
                Evaluasi penjualan pesanan <strong>Selesai</strong> tiap kantin mitra per semester. Pusat memutuskan royalti periode berikutnya (<strong>Tetap 10%</strong> atau <strong>Naik</strong> beserta alasan). Keputusan berlaku untuk pesanan Selesai sesudahnya dan <strong>tidak mengubah royalti pesanan lama</strong>.
            </p>
        </div>

        <a href="{{ route('pusat.laporan', ['periode' => 'semester', 'semester' => $selectedSemester]) }}" class="ayi-btn-secondary">
            ← Lihat Laporan Gabungan Pusat
        </a>
    </div>

    {{-- Pemilih Semester Aktif --}}
    <div class="mt-6 flex flex-col justify-between gap-4 rounded-[12px] border border-border-ayi bg-surface p-4 shadow-ayi-xs sm:flex-row sm:items-center">
        <div>
            <span class="font-heading text-xs font-bold uppercase tracking-wider text-navy">Semester Audit Aktif</span>
            <p class="font-heading text-lg font-bold text-ink">
                {{ $availableSemesters[$selectedSemester] ?? $selectedSemester }}
            </p>
        </div>

        <form action="{{ route('pusat.audit') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <label for="filter_semester" class="font-heading text-xs font-bold uppercase tracking-wider text-ink-secondary">
                Ganti Semester:
            </label>
            <select
                id="filter_semester"
                name="semester"
                class="ayi-input min-h-[40px] w-auto py-1.5 text-xs font-semibold"
                onchange="this.form.submit()"
            >
                @foreach ($availableSemesters as $semCode => $semLabel)
                    <option value="{{ $semCode }}" @selected($selectedSemester === $semCode)>
                        {{ $semLabel }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="ayi-btn-secondary min-h-[40px] px-4 py-1.5 text-xs">
                Tampilkan
            </button>
        </form>
    </div>

    {{-- Grid Utama: Kiri Snapshot per Outlet + Form Audit, Kanan Riwayat Audit & Riwayat Perubahan Royalti --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-12 lg:items-start">
        {{-- Kolom Kiri (5 cols): Form Buat Audit Semester --}}
        <section class="ayi-card p-6 lg:col-span-5" x-data="{ keputusan: '{{ old('keputusan', 'tetap') }}' }">
            <h2 class="font-heading text-xl font-bold text-ink">
                BUAT KEPUTUSAN AUDIT SEMESTER
            </h2>
            <p class="mt-1 text-xs text-ink-secondary">
                Satu outlet hanya dapat diaudit <strong>satu kali per semester</strong> (`unique(outlet_id, semester)`). Tidak ada rumus kenaikan otomatis — keputusan dibuat manual oleh Pusat.
            </p>

            <form action="{{ route('pusat.audit.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label for="audit_semester" class="ayi-label">Periode Semester Audit</label>
                    <input
                        type="text"
                        id="audit_semester"
                        name="semester"
                        value="{{ old('semester', $selectedSemester) }}"
                        class="ayi-input font-semibold"
                        placeholder="Contoh: 2026/2027 Ganjil"
                        required
                    />
                    <p class="mt-1 text-[11px] text-ink-muted">
                        Format eksplisit: <code>YYYY/YYYY Ganjil</code> atau <code>YYYY/YYYY Genap</code>.
                    </p>
                    @error('semester')
                        <p class="mt-1 text-xs font-semibold text-chili">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="audit_outlet_id" class="ayi-label">Pilih Kantin Mitra</label>
                    <select
                        id="audit_outlet_id"
                        name="outlet_id"
                        class="ayi-input"
                        required
                    >
                        @foreach ($outlets as $outlet)
                            @php
                                $snap = $outletSnapshots[$outlet->id] ?? ['total_penjualan' => 0, 'total_royalti_terhitung' => 0, 'jumlah_selesai' => 0];
                                $already = $existingAuditsByOutlet->has($outlet->id);
                            @endphp
                            <option
                                value="{{ $outlet->id }}"
                                @selected((int) old('outlet_id', $selectedOutletId) === (int) $outlet->id)
                            >
                                {{ $outlet->nama }} (Saat ini {{ number_format((float) $outlet->royalty_percent, 2, ',', '.') }}% · Penjualan: Rp {{ number_format($snap['total_penjualan'], 0, ',', '.') }}){{ $already ? ' — [Sudah Diaudit]' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('outlet_id')
                        <p class="mt-1 text-xs font-semibold text-chili">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pilihan Keputusan: Tetap vs Naik --}}
                <div>
                    <span class="ayi-label">Keputusan Royalti Semester Berikutnya</span>
                    <div class="mt-2 grid grid-cols-2 gap-3">
                        <label
                            class="flex cursor-pointer items-center gap-2.5 rounded-[12px] border p-3 text-sm font-semibold transition"
                            :class="keputusan === 'tetap' ? 'border-2 border-navy bg-tint-navy text-navy' : 'border-border-ayi bg-surface text-ink-secondary'"
                        >
                            <input
                                type="radio"
                                name="keputusan"
                                value="tetap"
                                x-model="keputusan"
                                class="text-navy focus:ring-navy"
                            />
                            <div>
                                <span class="block font-heading text-sm font-bold">Tetap</span>
                                <span class="block text-[11px] font-normal">Persen tidak berubah</span>
                            </div>
                        </label>

                        <label
                            class="flex cursor-pointer items-center gap-2.5 rounded-[12px] border p-3 text-sm font-semibold transition"
                            :class="keputusan === 'naik' ? 'border-2 border-chili bg-tint-chili text-chili' : 'border-border-ayi bg-surface text-ink-secondary'"
                        >
                            <input
                                type="radio"
                                name="keputusan"
                                value="naik"
                                x-model="keputusan"
                                class="text-chili focus:ring-chili"
                            />
                            <div>
                                <span class="block font-heading text-sm font-bold">Naik</span>
                                <span class="block text-[11px] font-normal">Tentukan % baru + alasan</span>
                            </div>
                        </label>
                    </div>
                    @error('keputusan')
                        <p class="mt-1 text-xs font-semibold text-chili">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Input Persen Royalti Baru (muncul/aktif bila keputusan = naik) --}}
                <div x-show="keputusan === 'naik'" x-cloak>
                    <label for="audit_percent_baru" class="ayi-label">
                        Persen Royalti Baru (%) <span class="text-chili">*</span>
                    </label>
                    <input
                        type="number"
                        step="0.5"
                        min="10"
                        max="50"
                        id="audit_percent_baru"
                        name="percent_baru"
                        value="{{ old('percent_baru', '12.00') }}"
                        class="ayi-input tabular-nums"
                        placeholder="Contoh: 12.00"
                    />
                    <p class="mt-1 text-[11px] text-ink-muted">
                        Harus lebih besar dari persen royalti outlet saat ini. Berlaku untuk pesanan Selesai sesudah audit ini disimpan.
                    </p>
                    @error('percent_baru')
                        <p class="mt-1 text-xs font-semibold text-chili">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="audit_catatan" class="ayi-label">
                        Alasan / Catatan Audit Pusat
                        <span x-show="keputusan === 'naik'" class="text-chili">*</span>
                    </label>
                    <textarea
                        id="audit_catatan"
                        name="catatan"
                        rows="3"
                        class="ayi-input"
                        placeholder="Tulis dasar pertimbangan audit dari angka penjualan semester ini..."
                    >{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-xs font-semibold text-chili">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-[10px] bg-surface-cream p-3 text-xs leading-relaxed text-ink-secondary">
                    <p class="font-semibold text-navy">Aturan Non-Retroaktif (PRD KF-10 & ERD §2.7):</p>
                    <ul class="mt-1 list-disc pl-4 space-y-0.5">
                        <li>Keputusan <strong>Naik</strong> otomatis mencatat jejak di <code>royalty_histories</code> dan memperbarui persen royalti outlet untuk pesanan yang selesai berikutnya.</li>
                        <li>Keputusan <strong>Tetap</strong> menyimpan rekam audit tanpa menambah baris perubahan di <code>royalty_histories</code>.</li>
                        <li>Seluruh pesanan yang sudah <strong>Selesai</strong> sebelumnya tetap mengunci <code>royalty_percent_saat_selesai</code> dan <code>royalty_nominal</code> lama.</li>
                    </ul>
                </div>

                <button type="submit" class="ayi-btn-primary w-full">
                    Simpan Keputusan Audit Semester
                </button>
            </form>
        </section>

        {{-- Kolom Kanan (7 cols): Ringkasan Angka Semester per Outlet --}}
        <section class="space-y-6 lg:col-span-7">
            <div class="ayi-card p-6">
                <h2 class="font-heading text-xl font-bold text-ink">
                    ANGKA PENJUALAN & ROYALTI OUTLET ({{ strtoupper($selectedSemester) }})
                </h2>
                <p class="mt-1 text-xs text-ink-secondary">
                    Angka snapshot di bawah dihitung dari pesanan berstatus <strong>Selesai</strong> pada semester <strong>{{ $selectedSemester }}</strong>. Klik "Pilih Audit" untuk memuat outlet ke formulir di samping.
                </p>

                <div class="mt-4 space-y-3">
                    @foreach ($outlets as $outlet)
                        @php
                            $snap = $outletSnapshots[$outlet->id] ?? ['total_penjualan' => 0, 'total_royalti_terhitung' => 0, 'jumlah_selesai' => 0];
                            /** @var \App\Models\RoyaltyAudit|null $existingAudit */
                            $existingAudit = $existingAuditsByOutlet->get($outlet->id);
                        @endphp
                        <div class="rounded-[12px] border {{ $existingAudit ? 'border-leaf bg-tint-leaf/35' : 'border-border-ayi bg-surface-cream' }} p-4">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-heading text-base font-bold text-ink">{{ $outlet->nama }}</h3>
                                        <x-status-badge :status="$outlet->status" type="outlet" />
                                        @if ($outlet->is_sample)
                                            <span class="ayi-chip-sample">Data Contoh</span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 text-xs text-ink-secondary">
                                        Pemilik: {{ $outlet->pemilik }} · Royalti Berlaku Saat Ini: <strong class="text-navy">{{ number_format((float) $outlet->royalty_percent, 2, ',', '.') }}%</strong>
                                    </p>
                                </div>

                                @if ($existingAudit)
                                    <span class="ayi-badge-selesai">
                                        <span class="h-2 w-2 rounded-full bg-leaf" aria-hidden="true"></span>
                                        <span>Sudah Diaudit ({{ $existingAudit->keputusan->label() }} → {{ number_format((float) $existingAudit->percent_baru, 2, ',', '.') }}%)</span>
                                    </span>
                                @else
                                    <a
                                        href="{{ route('pusat.audit', ['semester' => $selectedSemester, 'outlet_id' => $outlet->id]) }}"
                                        class="ayi-btn-secondary min-h-[34px] px-3 py-1 text-xs"
                                    >
                                        Pilih Audit
                                    </a>
                                @endif
                            </div>

                            <div class="mt-3 grid grid-cols-3 gap-2 rounded-[10px] bg-surface p-3 text-xs">
                                <div>
                                    <span class="text-ink-muted">Pesanan Selesai</span>
                                    <p class="font-heading text-sm font-bold text-ink tabular-nums">
                                        {{ $snap['jumlah_selesai'] }} Pesanan
                                    </p>
                                </div>
                                <div>
                                    <span class="text-ink-muted">Total Penjualan Semester</span>
                                    <p class="font-heading text-sm font-bold text-navy tabular-nums">
                                        Rp {{ number_format($snap['total_penjualan'], 0, ',', '.') }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-ink-muted">Royalti Terhitung</span>
                                    <p class="font-heading text-sm font-bold text-chili tabular-nums">
                                        Rp {{ number_format($snap['total_royalti_terhitung'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            @if ($existingAudit && $existingAudit->catatan)
                                <p class="mt-2 text-xs text-ink-secondary">
                                    <strong>Catatan Audit:</strong> {{ $existingAudit->catatan }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    {{-- Tabel Daftar Rekam Audit Royalti Semester (royalty_audits) --}}
    <section class="mt-10">
        <h2 class="font-heading text-[24px] font-bold text-ink">
            DAFTAR AUDIT ROYALTI SEMESTER TERCATAT
        </h2>
        <p class="text-xs text-ink-secondary">
            Seluruh keputusan audit semester per outlet yang tersimpan di tabel <code>royalty_audits</code>.
        </p>

        <div class="ayi-card mt-4 overflow-x-auto">
            @if ($audits->isEmpty())
                <div class="p-8 text-center text-sm text-ink-muted">
                    Belum ada rekam audit royalti semester yang disimpan.
                </div>
            @else
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase tracking-wider text-ink">
                        <tr>
                            <th class="px-5 py-3.5">Semester</th>
                            <th class="px-5 py-3.5">Kantin Mitra</th>
                            <th class="px-5 py-3.5 text-right">Snapshot Penjualan</th>
                            <th class="px-5 py-3.5 text-right">Snapshot Royalti</th>
                            <th class="px-5 py-3.5">Keputusan</th>
                            <th class="px-5 py-3.5 text-right">Persen Baru</th>
                            <th class="px-5 py-3.5">Catatan / Alasan Pusat</th>
                            <th class="px-5 py-3.5">Auditor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach ($audits as $audit)
                            <tr class="hover:bg-surface-cream">
                                <td class="px-5 py-4 font-heading font-bold text-navy">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span>{{ $audit->semester }}</span>
                                        @if ($audit->is_sample)
                                            <span class="ayi-chip-sample">Data Contoh</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 font-semibold text-ink">
                                    {{ $audit->outlet?->nama ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-right font-heading font-bold text-ink tabular-nums">
                                    Rp {{ number_format($audit->total_penjualan, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-right font-heading font-bold text-chili tabular-nums">
                                    Rp {{ number_format($audit->total_royalti_terhitung, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4">
                                    @if ($audit->keputusan === \App\Enums\RoyaltyDecision::Naik)
                                        <span class="inline-flex items-center rounded-full bg-tint-chili px-2.5 py-0.5 font-heading text-xs font-bold text-chili">
                                            Naik
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-tint-navy px-2.5 py-0.5 font-heading text-xs font-bold text-navy">
                                            Tetap
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right font-heading font-bold text-navy tabular-nums">
                                    {{ number_format((float) $audit->percent_baru, 2, ',', '.') }}%
                                </td>
                                <td class="px-5 py-4 text-xs text-ink-secondary max-w-xs">
                                    {{ $audit->catatan ?: '-' }}
                                </td>
                                <td class="px-5 py-4 text-xs text-ink-secondary">
                                    <div class="font-semibold text-ink">{{ $audit->pembuat?->name ?? 'Pusat Ayiriz' }}</div>
                                    <div>{{ $audit->created_at?->translatedFormat('d M Y H:i') }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>

    {{-- Tabel Riwayat Perubahan Persen Royalti per Outlet (royalty_histories) --}}
    <section class="mt-10">
        <h2 class="font-heading text-[24px] font-bold text-ink">
            RIWAYAT PERUBAHAN PERSEN ROYALTI OUTLET
        </h2>
        <p class="text-xs text-ink-secondary">
            Jejak perubahan persen royalti di tabel <code>royalty_histories</code> (hanya tercatat ketika keputusan audit adalah <strong>Naik</strong>).
        </p>

        <div class="ayi-card mt-4 overflow-x-auto">
            @if ($histories->isEmpty())
                <div class="p-8 text-center text-sm text-ink-muted">
                    Belum ada riwayat perubahan kenaikan persen royalti outlet.
                </div>
            @else
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-divider bg-tint-sunny font-heading text-xs font-bold uppercase tracking-wider text-ink">
                        <tr>
                            <th class="px-5 py-3.5">Kantin Mitra</th>
                            <th class="px-5 py-3.5 text-right">Persen Lama</th>
                            <th class="px-5 py-3.5 text-right">Persen Baru</th>
                            <th class="px-5 py-3.5">Alasan Kenaikan</th>
                            <th class="px-5 py-3.5">Berlaku Mulai</th>
                            <th class="px-5 py-3.5">Diubah Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach ($histories as $history)
                            <tr class="hover:bg-surface-cream">
                                <td class="px-5 py-4 font-semibold text-ink">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span>{{ $history->outlet?->nama ?? '-' }}</span>
                                        @if ($history->is_sample)
                                            <span class="ayi-chip-sample">Data Contoh</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right font-heading text-ink-secondary tabular-nums">
                                    {{ number_format((float) $history->percent_lama, 2, ',', '.') }}%
                                </td>
                                <td class="px-5 py-4 text-right font-heading font-bold text-chili tabular-nums">
                                    {{ number_format((float) $history->percent_baru, 2, ',', '.') }}%
                                </td>
                                <td class="px-5 py-4 text-xs text-ink-secondary max-w-sm">
                                    {{ $history->alasan }}
                                </td>
                                <td class="px-5 py-4 text-xs font-semibold text-navy">
                                    {{ $history->berlaku_mulai?->translatedFormat('d M Y H:i') }}
                                </td>
                                <td class="px-5 py-4 text-xs text-ink-secondary">
                                    {{ $history->pengubah?->name ?? 'Pusat Ayiriz' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>
</x-layouts.panel>

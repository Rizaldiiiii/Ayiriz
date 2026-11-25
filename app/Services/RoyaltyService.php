<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\RoyaltyDecision;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\RoyaltyAudit;
use App\Models\RoyaltyHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoyaltyService
{
    /**
     * Hitung nominal royalti dari total pesanan dan persen royalti outlet.
     */
    public function calculateNominal(int $total, float|string $royaltyPercent): int
    {
        $percent = (float) $royaltyPercent;

        return (int) round($total * ($percent / 100));
    }

    /**
     * Daftar opsi semester eksplisit (contoh: "2026/2027 Ganjil").
     *
     * @return array<string, string>
     */
    public function availableSemesters(): array
    {
        $current = $this->currentSemesterLabel();

        $list = [
            '2025/2026 Ganjil' => '2025/2026 Ganjil (Jul–Des 2025)',
            '2025/2026 Genap' => '2025/2026 Genap (Jan–Jun 2026)',
            '2026/2027 Ganjil' => '2026/2027 Ganjil (Jul–Des 2026)',
            '2026/2027 Genap' => '2026/2027 Genap (Jan–Jun 2027)',
        ];

        if (! isset($list[$current])) {
            $list[$current] = $current;
        }

        return $list;
    }

    /**
     * Label semester berjalan berdasarkan tanggal saat ini.
     * Bulan Juli–Desember (7..12) = "{Y}/{Y+1} Ganjil", Januari–Juni (1..6) = "{Y-1}/{Y} Genap".
     */
    public function currentSemesterLabel(?Carbon $reference = null): string
    {
        $now = $reference ?? now();
        $year = (int) $now->year;
        $month = (int) $now->month;

        if ($month >= 7) {
            return sprintf('%d/%d Ganjil', $year, $year + 1);
        }

        return sprintf('%d/%d Genap', $year - 1, $year);
    }

    /**
     * Rentang tanggal [start, end] untuk kode semester seperti "2026/2027 Ganjil" atau "2026-ganjil".
     *
     * @return array{0: Carbon, 1: Carbon}|null
     */
    public function resolveSemesterRange(string $semester): ?array
    {
        $trimmed = trim($semester);

        // Format "2026/2027 Ganjil" atau "2025/2026 Genap"
        if (preg_match('/^(\d{4})\/(\d{4})\s+(Ganjil|Genap)$/i', $trimmed, $m)) {
            $year1 = (int) $m[1];
            $year2 = (int) $m[2];
            $term = strtolower($m[3]);

            if ($term === 'ganjil') {
                return [
                    Carbon::create($year1, 7, 1, 0, 0, 0)->startOfDay(),
                    Carbon::create($year1, 12, 31, 23, 59, 59)->endOfDay(),
                ];
            }

            return [
                Carbon::create($year2, 1, 1, 0, 0, 0)->startOfDay(),
                Carbon::create($year2, 6, 30, 23, 59, 59)->endOfDay(),
            ];
        }

        // Format alternatif ERD "2026-ganjil" atau "2026-genap"
        if (preg_match('/^(\d{4})[\-\s]+(ganjil|genap)$/i', $trimmed, $m)) {
            $year = (int) $m[1];
            $term = strtolower($m[2]);

            if ($term === 'ganjil') {
                return [
                    Carbon::create($year, 7, 1, 0, 0, 0)->startOfDay(),
                    Carbon::create($year, 12, 31, 23, 59, 59)->endOfDay(),
                ];
            }

            return [
                Carbon::create($year, 1, 1, 0, 0, 0)->startOfDay(),
                Carbon::create($year, 6, 30, 23, 59, 59)->endOfDay(),
            ];
        }

        return null;
    }

    /**
     * Terapkan filter periode ('harian', 'mingguan', 'bulanan', 'semester', 'semua') pada query Order.
     */
    public function applyPeriodFilter(Builder $query, string $periode, ?string $semester = null): Builder
    {
        if ($periode === 'harian') {
            return $query->whereDate('created_at', now()->toDateString());
        }

        if ($periode === 'mingguan') {
            return $query->where('created_at', '>=', now()->subDays(7)->startOfDay());
        }

        if ($periode === 'bulanan') {
            return $query->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month);
        }

        if ($periode === 'semester') {
            $semLabel = $semester ?: $this->currentSemesterLabel();
            $range = $this->resolveSemesterRange($semLabel);
            if ($range !== null) {
                return $query->whereBetween('created_at', [$range[0], $range[1]]);
            }
        }

        return $query;
    }

    /**
     * Hitung snapshot penjualan Selesai dan royalti terhitung untuk satu outlet pada semester tertentu.
     *
     * @return array{total_penjualan: int, total_royalti_terhitung: int, jumlah_selesai: int}
     */
    public function calculateSemesterSnapshot(Outlet $outlet, string $semester): array
    {
        $query = Order::where('outlet_id', $outlet->id)
            ->where('status', OrderStatus::Selesai);

        $range = $this->resolveSemesterRange($semester);
        if ($range !== null) {
            $query->whereBetween('created_at', [$range[0], $range[1]]);
        }

        $totalPenjualan = (int) (clone $query)->sum('total');
        $totalRoyalti = (int) (clone $query)->sum('royalty_nominal');
        $jumlahSelesai = (int) (clone $query)->count();

        return [
            'total_penjualan' => $totalPenjualan,
            'total_royalti_terhitung' => $totalRoyalti,
            'jumlah_selesai' => $jumlahSelesai,
        ];
    }

    /**
     * Buat audit royalti semester untuk satu outlet oleh Pusat (KF-10).
     * - Satu baris per (outlet_id, semester) — menolak duplikasi.
     * - Keputusan Tetap: persen royalti tidak berubah, tidak membuat baris di royalty_histories.
     * - Keputusan Naik: persen baru wajib > persen lama, catatan/alasan wajib diisi,
     *   membuat baris baru di royalty_histories dan memperbarui outlets.royalty_percent untuk pesanan Selesai berikutnya.
     * - Tidak pernah mengubah royalti pesanan lama yang sudah Selesai.
     */
    public function recordSemesterAudit(
        Outlet $outlet,
        string $semester,
        RoyaltyDecision|string $keputusan,
        ?float $percentBaruInput,
        ?string $catatan,
        User $actor
    ): RoyaltyAudit {
        if (! $actor->isPusat()) {
            abort(403, 'Hanya Pusat Ayiriz yang dapat membuat audit royalti semester.');
        }

        $decision = $keputusan instanceof RoyaltyDecision
            ? $keputusan
            : RoyaltyDecision::from($keputusan);

        $semesterClean = trim($semester);

        return DB::transaction(function () use ($outlet, $semesterClean, $decision, $percentBaruInput, $catatan, $actor): RoyaltyAudit {
            /** @var Outlet $lockedOutlet */
            $lockedOutlet = Outlet::lockForUpdate()->findOrFail($outlet->id);

            $alreadyAudited = RoyaltyAudit::where('outlet_id', $lockedOutlet->id)
                ->where('semester', $semesterClean)
                ->exists();

            if ($alreadyAudited) {
                throw ValidationException::withMessages([
                    'semester' => "Audit royalti untuk outlet \"{$lockedOutlet->nama}\" pada semester \"{$semesterClean}\" sudah pernah dibuat dan tidak dapat dibuat dua kali.",
                ]);
            }

            $percentLama = (float) $lockedOutlet->royalty_percent;

            if ($decision === RoyaltyDecision::Tetap) {
                $percentBaru = $percentLama;
            } else {
                if ($percentBaruInput === null || $percentBaruInput <= $percentLama) {
                    throw ValidationException::withMessages([
                        'percent_baru' => sprintf(
                            'Untuk keputusan Naik, persen royalti baru harus lebih besar dari persen royalti saat ini (%s%%).',
                            number_format($percentLama, 2, ',', '.')
                        ),
                    ]);
                }

                if ($catatan === null || trim($catatan) === '') {
                    throw ValidationException::withMessages([
                        'catatan' => 'Alasan / catatan pusat wajib diisi ketika memutuskan kenaikan royalti.',
                    ]);
                }

                $percentBaru = round($percentBaruInput, 2);
            }

            $snapshot = $this->calculateSemesterSnapshot($lockedOutlet, $semesterClean);

            $audit = RoyaltyAudit::create([
                'outlet_id' => $lockedOutlet->id,
                'semester' => $semesterClean,
                'total_penjualan' => $snapshot['total_penjualan'],
                'total_royalti_terhitung' => $snapshot['total_royalti_terhitung'],
                'keputusan' => $decision,
                'percent_baru' => $percentBaru,
                'catatan' => $catatan !== null && trim($catatan) !== '' ? trim($catatan) : 'Keputusan audit semester: royalti tetap.',
                'dibuat_oleh' => $actor->id,
                'is_sample' => false,
            ]);

            if ($decision === RoyaltyDecision::Naik) {
                RoyaltyHistory::create([
                    'outlet_id' => $lockedOutlet->id,
                    'percent_lama' => $percentLama,
                    'percent_baru' => $percentBaru,
                    'alasan' => trim((string) $catatan),
                    'diubah_oleh' => $actor->id,
                    'berlaku_mulai' => now(),
                    'is_sample' => false,
                    'created_at' => now(),
                ]);

                $lockedOutlet->update([
                    'royalty_percent' => $percentBaru,
                ]);
            }

            return $audit->fresh(['outlet', 'pembuat']);
        });
    }

    /**
     * Ringkasan penjualan, royalti, pesanan, dan daftar tunggu (antrean) satu outlet (penjualan & royalti hanya dari pesanan Selesai).
     *
     * @return array{total_penjualan: int, total_royalti: int, jumlah_selesai: int, jumlah_diproses: int, jumlah_antrean: int}
     */
    public function summarizeOutlet(Outlet $outlet): array
    {
        $selesaiQuery = $outlet->orders()->where('status', OrderStatus::Selesai);
        $diprosesQuery = $outlet->orders()->where('status', OrderStatus::Diproses);
        $antreanQuery = $outlet->antreans();

        return [
            'total_penjualan' => (int) (clone $selesaiQuery)->sum('total'),
            'total_royalti' => (int) (clone $selesaiQuery)->sum('royalty_nominal'),
            'jumlah_selesai' => (int) (clone $selesaiQuery)->count(),
            'jumlah_diproses' => (int) $diprosesQuery->count(),
            'jumlah_antrean' => (int) $antreanQuery->count(),
        ];
    }
}

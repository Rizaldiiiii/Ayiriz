<?php

namespace App\Http\Controllers\Pusat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pusat\StoreRoyaltyAuditRequest;
use App\Models\Outlet;
use App\Models\RoyaltyAudit;
use App\Models\RoyaltyHistory;
use App\Services\RoyaltyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoyaltyAuditController extends Controller
{
    public function __construct(
        private readonly RoyaltyService $royaltyService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', RoyaltyAudit::class);

        $availableSemesters = $this->royaltyService->availableSemesters();
        $currentSemester = $this->royaltyService->currentSemesterLabel();

        $selectedSemester = (string) $request->query('semester', $currentSemester);
        if (trim($selectedSemester) === '') {
            $selectedSemester = $currentSemester;
        }

        $outlets = Outlet::with('user')
            ->orderBy('nama')
            ->get();

        $selectedOutletId = (int) $request->query('outlet_id', $outlets->first()?->id ?? 0);

        // Hitung pratinjau snapshot penjualan & royalti semester terpilih untuk setiap outlet
        $outletSnapshots = [];
        $existingAuditsByOutlet = RoyaltyAudit::where('semester', $selectedSemester)
            ->get()
            ->keyBy('outlet_id');

        foreach ($outlets as $outlet) {
            $outletSnapshots[$outlet->id] = $this->royaltyService->calculateSemesterSnapshot($outlet, $selectedSemester);
        }

        $audits = RoyaltyAudit::with(['outlet', 'pembuat'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $histories = RoyaltyHistory::with(['outlet', 'pengubah'])
            ->orderByDesc('berlaku_mulai')
            ->orderByDesc('id')
            ->get();

        return view('pusat.audit-index', [
            'outlets' => $outlets,
            'availableSemesters' => $availableSemesters,
            'selectedSemester' => $selectedSemester,
            'selectedOutletId' => $selectedOutletId,
            'outletSnapshots' => $outletSnapshots,
            'existingAuditsByOutlet' => $existingAuditsByOutlet,
            'audits' => $audits,
            'histories' => $histories,
        ]);
    }

    public function store(StoreRoyaltyAuditRequest $request): RedirectResponse
    {
        $this->authorize('create', RoyaltyAudit::class);

        $validated = $request->validated();
        $outlet = Outlet::findOrFail((int) $validated['outlet_id']);

        $audit = $this->royaltyService->recordSemesterAudit(
            outlet: $outlet,
            semester: (string) $validated['semester'],
            keputusan: (string) $validated['keputusan'],
            percentBaruInput: isset($validated['percent_baru']) && $validated['percent_baru'] !== ''
                ? (float) $validated['percent_baru']
                : null,
            catatan: $validated['catatan'] ?? null,
            actor: $request->user()
        );

        $keputusanLabel = $audit->keputusan->label();
        $persenFormatted = number_format((float) $audit->percent_baru, 2, ',', '.');

        return redirect()
            ->route('pusat.audit', [
                'semester' => $audit->semester,
                'outlet_id' => $outlet->id,
            ])
            ->with(
                'toast_success',
                "Audit royalti semester \"{$audit->semester}\" untuk outlet \"{$outlet->nama}\" berhasil disimpan (Keputusan: {$keputusanLabel} — {$persenFormatted}% untuk periode berikutnya)."
            );
    }
}

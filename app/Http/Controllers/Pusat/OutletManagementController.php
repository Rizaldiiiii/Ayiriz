<?php

namespace App\Http\Controllers\Pusat;

use App\Enums\OrderStatus;
use App\Enums\OutletStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pusat\StoreOutletRequest;
use App\Http\Requests\Pusat\UpdateOutletRequest;
use App\Http\Requests\Pusat\UpdateOutletStatusRequest;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OutletManagementController extends Controller
{
    public function dashboard(): View
    {
        $outlets = Outlet::with('user')
            ->withCount([
                'orders as orders_selesai_count' => fn ($q) => $q->where('status', OrderStatus::Selesai),
                'orders as orders_diproses_count' => fn ($q) => $q->where('status', OrderStatus::Diproses),
                'menus',
            ])
            ->withSum(
                ['orders as total_penjualan' => fn ($q) => $q->where('status', OrderStatus::Selesai)],
                'total'
            )
            ->withSum(
                ['orders as total_royalti' => fn ($q) => $q->where('status', OrderStatus::Selesai)],
                'royalty_nominal'
            )
            ->orderBy('nama')
            ->get();

        $summary = [
            'total_outlet' => $outlets->count(),
            'outlet_aktif' => $outlets->where('status', OutletStatus::Terverifikasi)->count(),
            'outlet_menunggu' => $outlets->where('status', OutletStatus::BelumTerverifikasi)->count(),
            'total_penjualan_selesai' => (int) Order::where('status', OrderStatus::Selesai)->sum('total'),
            'total_royalti_selesai' => (int) Order::where('status', OrderStatus::Selesai)->sum('royalty_nominal'),
        ];

        return view('pusat.dashboard', [
            'outlets' => $outlets,
            'summary' => $summary,
        ]);
    }

    public function index(): View
    {
        $this->authorize('viewAny', Outlet::class);

        $outlets = Outlet::with('user')
            ->withCount([
                'orders as orders_selesai_count' => fn ($q) => $q->where('status', OrderStatus::Selesai),
                'orders as orders_diproses_count' => fn ($q) => $q->where('status', OrderStatus::Diproses),
                'menus',
            ])
            ->withSum(
                ['orders as total_penjualan' => fn ($q) => $q->where('status', OrderStatus::Selesai)],
                'total'
            )
            ->withSum(
                ['orders as total_royalti' => fn ($q) => $q->where('status', OrderStatus::Selesai)],
                'royalty_nominal'
            )
            ->orderByDesc('created_at')
            ->get();

        return view('pusat.outlets-index', [
            'outlets' => $outlets,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Outlet::class);

        return view('pusat.outlets-create');
    }

    public function store(StoreOutletRequest $request): RedirectResponse
    {
        $this->authorize('create', Outlet::class);
        $validated = $request->validated();

        $outlet = DB::transaction(function () use ($validated): Outlet {
            $user = User::create([
                'name' => $validated['pemilik'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => UserRole::Outlet,
            ]);

            $baseSlug = Str::slug($validated['nama']);
            if ($baseSlug === '') {
                $baseSlug = 'outlet-ayiriz';
            }

            $slug = $baseSlug;
            $counter = 1;
            while (Outlet::where('slug', $slug)->exists()) {
                $counter++;
                $slug = $baseSlug . '-' . $counter;
            }

            return Outlet::create([
                'user_id' => $user->id,
                'nama' => $validated['nama'],
                'slug' => $slug,
                'pemilik' => $validated['pemilik'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'],
                'jam_buka' => $validated['jam_buka'],
                'status' => OutletStatus::BelumTerverifikasi,
                'royalty_percent' => 10.00,
                'is_sample' => false,
            ]);
        });

        return redirect()->route('pusat.outlets.index')
            ->with(
                'toast_success',
                "Outlet \"{$outlet->nama}\" berhasil didaftarkan dengan status Belum Terverifikasi (Royalti awal 10%)."
            );
    }

    public function edit(Outlet $outlet): View
    {
        $this->authorize('update', $outlet);

        $outlet->load('user');

        return view('pusat.outlets-edit', [
            'outlet' => $outlet,
        ]);
    }

    public function update(UpdateOutletRequest $request, Outlet $outlet): RedirectResponse
    {
        $this->authorize('update', $outlet);
        $validated = $request->validated();

        $outlet->update([
            'nama' => $validated['nama'],
            'pemilik' => $validated['pemilik'],
            'no_hp' => $validated['no_hp'],
            'alamat' => $validated['alamat'],
            'jam_buka' => $validated['jam_buka'],
        ]);

        if ($outlet->user) {
            $outlet->user->update([
                'name' => $validated['pemilik'],
            ]);
        }

        return redirect()->route('pusat.outlets.index')
            ->with('toast_success', "Data outlet \"{$outlet->nama}\" berhasil diperbarui.");
    }

    public function updateStatus(UpdateOutletStatusRequest $request, Outlet $outlet): RedirectResponse
    {
        $this->authorize('changeStatus', $outlet);

        $newStatus = OutletStatus::from($request->validated()['status']);
        $outlet->update([
            'status' => $newStatus,
        ]);

        return back()->with(
            'toast_success',
            "Status outlet \"{$outlet->nama}\" diubah menjadi {$newStatus->label()}."
        );
    }

    public function stubLaporan(): View
    {
        return view('stubs.tahap-berikutnya', [
            'kodeFitur' => 'KF-08',
            'namaFitur' => 'Laporan dan Dashboard Pusat Per Periode',
            'tahap' => 'Tahap 3 — Pusat dan Audit',
            'deskripsi' => 'Modul rekapitulasi penjualan gabungan per periode (harian, mingguan, bulanan, semester) dan menu terlaris per outlet dijadwalkan pada Tahap 3.',
            'backUrl' => route('pusat.dashboard'),
            'backLabel' => 'Kembali ke Dashboard Pusat',
        ]);
    }

    public function stubAudit(): View
    {
        return view('stubs.tahap-berikutnya', [
            'kodeFitur' => 'KF-10',
            'namaFitur' => 'Audit Royalti Semester',
            'tahap' => 'Tahap 3 — Pusat dan Audit',
            'deskripsi' => 'Modul audit royalti per semester per outlet (keputusan Tetap 10% atau Naik beserta riwayat royalti) dijadwalkan pada Tahap 3.',
            'backUrl' => route('pusat.dashboard'),
            'backLabel' => 'Kembali ke Dashboard Pusat',
        ]);
    }
}

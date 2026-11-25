<?php

namespace App\Http\Controllers;

use App\Http\Requests\Public\StoreReactionRequest;
use App\Models\Antrean;
use App\Models\Order;
use App\Services\ReactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicReactionController extends Controller
{
    public function __construct(
        private readonly ReactionService $reactionService
    ) {}

    /**
     * Halaman cari pesanan lewat kode pesanan untuk memberi reaksi emote tanpa QR (KF-07).
     */
    public function lookup(Request $request): View|RedirectResponse
    {
        $kode = trim((string) $request->query('kode', ''));
        if ($kode !== '') {
            return redirect()->route('catalog.reaksi', $kode);
        }

        return view('public.reaksi-lookup');
    }

    /**
     * Halaman reaksi pesanan (/reaksi/{kode}) yang dibuka lewat QR kasir atau input kode pesanan (tanpa login).
     * Reaksi hanya terbuka untuk pesanan yang sudah dibayar (berstatus Diproses atau Selesai).
     */
    public function show(string $kode): View
    {
        $kode = trim($kode);

        $order = Order::with([
            'outlet',
            'items.menu' => fn ($q) => $q->withCount([
                'reactions as enak_count' => fn ($rq) => $rq->where('emote', 'enak'),
                'reactions as pedas_count' => fn ($rq) => $rq->where('emote', 'pedas'),
                'reactions as manis_count' => fn ($rq) => $rq->where('emote', 'manis'),
            ]),
            'reactions',
        ])->where('kode', $kode)->first();

        $antrean = null;
        if (! $order) {
            $antrean = Antrean::with('outlet')->where('kode', $kode)->first();
        }

        $reactionsByMenu = [];
        if ($order) {
            foreach ($order->reactions as $reaction) {
                $reactionsByMenu[(int) $reaction->menu_id] = $reaction->emote->value;
            }
        }

        return view('public.reaksi-show', [
            'kode' => $kode,
            'order' => $order,
            'antrean' => $antrean,
            'reactionsByMenu' => $reactionsByMenu,
        ]);
    }

    /**
     * Simpan atau ganti reaksi emote (1 reaksi per menu per pesanan yang sudah dibayar).
     */
    public function store(StoreReactionRequest $request, string $kode): RedirectResponse
    {
        $validated = $request->validated();

        $reaction = $this->reactionService->submitReaction(
            $kode,
            (int) $validated['menu_id'],
            $validated['emote']
        );

        return redirect()->route('catalog.reaksi', $kode)
            ->with(
                'toast_success',
                "Reaksi {$reaction->emote->fullLabel()} berhasil disimpan untuk pesanan {$kode}!"
            );
    }
}

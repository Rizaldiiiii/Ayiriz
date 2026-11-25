<?php

namespace App\Models;

use App\Enums\RoyaltyDecision;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoyaltyAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'semester',
        'total_penjualan',
        'total_royalti_terhitung',
        'keputusan',
        'percent_baru',
        'catatan',
        'dibuat_oleh',
        'is_sample',
    ];

    protected function casts(): array
    {
        return [
            'total_penjualan' => 'integer',
            'total_royalti_terhitung' => 'integer',
            'keputusan' => RoyaltyDecision::class,
            'percent_baru' => 'decimal:2',
            'is_sample' => 'boolean',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}

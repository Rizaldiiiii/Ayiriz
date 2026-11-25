<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoyaltyHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'outlet_id',
        'percent_lama',
        'percent_baru',
        'alasan',
        'diubah_oleh',
        'berlaku_mulai',
        'is_sample',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'percent_lama' => 'decimal:2',
            'percent_baru' => 'decimal:2',
            'berlaku_mulai' => 'datetime',
            'is_sample' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}

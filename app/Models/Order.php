<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'user_id',
        'outlet_id',
        'status',
        'total',
        'royalty_percent_saat_selesai',
        'royalty_nominal',
        'metode_bayar',
        'selesai_at',
        'is_sample',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total' => 'integer',
            'royalty_percent_saat_selesai' => 'decimal:2',
            'royalty_nominal' => 'integer',
            'selesai_at' => 'datetime',
            'is_sample' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeSelesai(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Selesai);
    }

    public function scopeDiproses(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Diproses);
    }

    public function isDiproses(): bool
    {
        return $this->status === OrderStatus::Diproses;
    }

    public function isSelesai(): bool
    {
        return $this->status === OrderStatus::Selesai;
    }

    public function formattedTotal(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function formattedRoyalty(): string
    {
        return 'Rp ' . number_format($this->royalty_nominal, 0, ',', '.');
    }
}

<?php

namespace App\Models;

use App\Enums\OutletStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'category_id',
        'nama',
        'deskripsi',
        'harga',
        'stok_porsi',
        'tersedia',
        'foto',
        'is_sample',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'stok_porsi' => 'integer',
            'tersedia' => 'boolean',
            'is_sample' => 'boolean',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function antreanItems(): HasMany
    {
        return $this->hasMany(AntreanItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeKatalogPublik(Builder $query): Builder
    {
        return $query->where('tersedia', true)
            ->whereHas('outlet', fn (Builder $q) => $q->where('status', OutletStatus::Terverifikasi));
    }

    public function bisaDipesan(): bool
    {
        return $this->tersedia
            && $this->stok_porsi > 0
            && $this->outlet !== null
            && $this->outlet->status === OutletStatus::Terverifikasi;
    }

    public function formattedHarga(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}

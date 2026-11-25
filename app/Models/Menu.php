<?php

namespace App\Models;

use App\Enums\OutletStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function menuIngredients(): HasMany
    {
        return $this->hasMany(MenuIngredient::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'menu_ingredients')
            ->withPivot('takaran');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
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

    /**
     * Hitungan reaksi per emote untuk satu menu (tanpa skor total, rata-rata, atau ranking).
     *
     * @return array{enak: int, pedas: int, manis: int}
     */
    public function reactionCounts(): array
    {
        if (isset($this->enak_count, $this->pedas_count, $this->manis_count)) {
            return [
                'enak' => (int) $this->enak_count,
                'pedas' => (int) $this->pedas_count,
                'manis' => (int) $this->manis_count,
            ];
        }

        $grouped = $this->reactions()
            ->selectRaw('emote, COUNT(*) as aggregate')
            ->groupBy('emote')
            ->pluck('aggregate', 'emote');

        return [
            'enak' => (int) ($grouped['enak'] ?? 0),
            'pedas' => (int) ($grouped['pedas'] ?? 0),
            'manis' => (int) ($grouped['manis'] ?? 0),
        ];
    }
}

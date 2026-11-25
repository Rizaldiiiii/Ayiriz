<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'nama',
        'satuan',
        'stok',
        'batas_minimum',
        'is_sample',
    ];

    protected function casts(): array
    {
        return [
            'stok' => 'decimal:2',
            'batas_minimum' => 'decimal:2',
            'is_sample' => 'boolean',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function menuIngredients(): HasMany
    {
        return $this->hasMany(MenuIngredient::class);
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_ingredients')
            ->withPivot('takaran');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeMenipis(Builder $query): Builder
    {
        return $query->whereColumn('stok', '<=', 'batas_minimum');
    }

    public function isMenipis(): bool
    {
        return (float) $this->stok <= (float) $this->batas_minimum;
    }

    public function formattedStok(): string
    {
        $val = (float) $this->stok;
        $formatted = floor($val) == $val
            ? number_format($val, 0, ',', '.')
            : number_format($val, 2, ',', '.');

        return $formatted . ' ' . $this->satuan;
    }

    public function formattedBatasMinimum(): string
    {
        $val = (float) $this->batas_minimum;
        $formatted = floor($val) == $val
            ? number_format($val, 0, ',', '.')
            : number_format($val, 2, ',', '.');

        return $formatted . ' ' . $this->satuan;
    }
}

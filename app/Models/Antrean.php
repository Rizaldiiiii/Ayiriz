<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Antrean extends Model
{
    use HasFactory;

    protected $table = 'antrean';

    protected $fillable = [
        'kode',
        'outlet_id',
        'nama_pemesan',
        'total',
        'is_sample',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'integer',
            'is_sample' => 'boolean',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(AntreanItem::class, 'antrean_id');
    }

    public function formattedTotal(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}

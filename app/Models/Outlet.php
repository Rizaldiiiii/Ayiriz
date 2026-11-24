<?php

namespace App\Models;

use App\Enums\OutletStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'slug',
        'pemilik',
        'no_hp',
        'alamat',
        'jam_buka',
        'status',
        'royalty_percent',
        'is_sample',
    ];

    protected function casts(): array
    {
        return [
            'status' => OutletStatus::class,
            'royalty_percent' => 'decimal:2',
            'is_sample' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function antreans(): HasMany
    {
        return $this->hasMany(Antrean::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', OutletStatus::Terverifikasi);
    }

    public function isAktif(): bool
    {
        return $this->status === OutletStatus::Terverifikasi;
    }
}

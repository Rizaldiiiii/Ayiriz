<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AntreanItem extends Model
{
    use HasFactory;

    protected $table = 'antrean_items';

    public $timestamps = false;

    protected $fillable = [
        'antrean_id',
        'menu_id',
        'nama_menu_saat_submit',
        'harga_saat_submit',
        'jumlah',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga_saat_submit' => 'integer',
            'jumlah' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    public function antrean(): BelongsTo
    {
        return $this->belongsTo(Antrean::class, 'antrean_id');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function formattedHarga(): string
    {
        return 'Rp ' . number_format($this->harga_saat_submit, 0, ',', '.');
    }

    public function formattedSubtotal(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}

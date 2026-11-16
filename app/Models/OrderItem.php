<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'menu_id',
        'nama_menu_saat_checkout',
        'harga_saat_checkout',
        'jumlah',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga_saat_checkout' => 'integer',
            'jumlah' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function formattedHarga(): string
    {
        return 'Rp ' . number_format($this->harga_saat_checkout, 0, ',', '.');
    }

    public function formattedSubtotal(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}

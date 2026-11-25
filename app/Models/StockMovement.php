<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $table = 'stock_movements';

    public $timestamps = false;

    protected $fillable = [
        'ingredient_id',
        'jenis',
        'jumlah',
        'keterangan',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'jenis' => StockMovementType::class,
            'jumlah' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}

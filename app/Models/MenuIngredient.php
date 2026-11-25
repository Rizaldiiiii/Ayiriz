<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuIngredient extends Model
{
    use HasFactory;

    protected $table = 'menu_ingredients';

    public $timestamps = false;

    protected $fillable = [
        'menu_id',
        'ingredient_id',
        'takaran',
    ];

    protected function casts(): array
    {
        return [
            'takaran' => 'decimal:2',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}

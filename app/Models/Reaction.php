<?php

namespace App\Models;

use App\Enums\ReactionEmote;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_id',
        'emote',
        'is_sample',
    ];

    protected function casts(): array
    {
        return [
            'emote' => ReactionEmote::class,
            'is_sample' => 'boolean',
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
}

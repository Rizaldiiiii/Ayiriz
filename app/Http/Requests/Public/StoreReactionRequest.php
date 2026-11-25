<?php

namespace App\Http\Requests\Public;

use App\Enums\ReactionEmote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'menu_id' => ['required', 'integer', 'exists:menus,id'],
            'emote' => ['required', Rule::enum(ReactionEmote::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'menu_id.required' => 'Menu yang diberi reaksi wajib dipilih.',
            'menu_id.exists' => 'Menu tidak ditemukan.',
            'emote.required' => 'Pilih salah satu reaksi emote (Enak, Pedas, atau Manis / menyejukkan).',
        ];
    }
}

<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jumlah' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'jumlah.required' => 'Jumlah porsi wajib diisi.',
            'jumlah.min' => 'Jumlah porsi tidak boleh negatif.',
        ];
    }
}

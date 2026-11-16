<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isPelanggan();
    }

    public function rules(): array
    {
        return [
            'menu_id' => ['required', 'integer', 'exists:menus,id'],
            'jumlah' => ['nullable', 'integer', 'min:1', 'max:100'],
            'replace_outlet' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'menu_id.required' => 'Pilih menu yang ingin ditambahkan ke keranjang.',
            'menu_id.exists' => 'Menu tidak ditemukan.',
            'jumlah.min' => 'Jumlah minimal 1 porsi.',
        ];
    }
}

<?php

namespace App\Http\Requests\Outlet;

use Illuminate\Foundation\Http\FormRequest;

class StoreIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && $this->user()->isOutlet()
            && $this->user()->outlet !== null;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:120'],
            'satuan' => ['required', 'string', 'max:40'],
            'stok' => ['required', 'numeric', 'min:0', 'max:999999'],
            'batas_minimum' => ['required', 'numeric', 'min:0', 'max:999999'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama bahan baku wajib diisi.',
            'satuan.required' => 'Satuan bahan baku (misal kg, liter, pcs) wajib diisi.',
            'stok.required' => 'Stok awal bahan baku wajib diisi.',
            'stok.min' => 'Stok awal tidak boleh negatif.',
            'batas_minimum.required' => 'Batas minimum peringatan stok wajib diisi.',
            'batas_minimum.min' => 'Batas minimum tidak boleh negatif.',
        ];
    }
}

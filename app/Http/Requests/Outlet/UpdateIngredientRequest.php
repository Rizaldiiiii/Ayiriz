<?php

namespace App\Http\Requests\Outlet;

use App\Models\Ingredient;
use Illuminate\Foundation\Http\FormRequest;

class UpdateIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Ingredient|null $ingredient */
        $ingredient = $this->route('ingredient');

        return $this->user() !== null
            && $ingredient !== null
            && $this->user()->can('update', $ingredient);
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:120'],
            'satuan' => ['required', 'string', 'max:40'],
            'batas_minimum' => ['required', 'numeric', 'min:0', 'max:999999'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama bahan baku wajib diisi.',
            'satuan.required' => 'Satuan bahan baku wajib diisi.',
            'batas_minimum.required' => 'Batas minimum peringatan stok wajib diisi.',
            'batas_minimum.min' => 'Batas minimum tidak boleh negatif.',
        ];
    }
}

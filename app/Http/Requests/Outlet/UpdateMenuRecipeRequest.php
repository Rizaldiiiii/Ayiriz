<?php

namespace App\Http\Requests\Outlet;

use App\Models\Menu;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Menu|null $menu */
        $menu = $this->route('menu');

        return $this->user() !== null
            && $menu !== null
            && $this->user()->can('update', $menu);
    }

    public function rules(): array
    {
        return [
            'resep' => ['nullable', 'array'],
            'resep.*.ingredient_id' => ['nullable', 'integer', 'exists:ingredients,id'],
            'resep.*.takaran' => ['nullable', 'numeric', 'min:0', 'max:99999'],
        ];
    }

    public function messages(): array
    {
        return [
            'resep.*.ingredient_id.exists' => 'Bahan baku yang dipilih tidak ditemukan.',
            'resep.*.takaran.min' => 'Takaran resep per porsi tidak boleh negatif.',
        ];
    }
}

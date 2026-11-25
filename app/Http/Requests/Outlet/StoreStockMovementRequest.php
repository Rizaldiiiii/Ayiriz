<?php

namespace App\Http\Requests\Outlet;

use App\Enums\StockMovementType;
use App\Models\Ingredient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Ingredient|null $ingredient */
        $ingredient = $this->route('ingredient');

        return $this->user() !== null
            && $ingredient !== null
            && $this->user()->can('recordMovement', $ingredient);
    }

    public function rules(): array
    {
        return [
            'jenis' => ['required', Rule::enum(StockMovementType::class)],
            'jumlah' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis.required' => 'Jenis mutasi stok (masuk/keluar/penyesuaian) wajib dipilih.',
            'jumlah.required' => 'Jumlah stok wajib diisi.',
            'jumlah.min' => 'Jumlah mutasi stok minimal 0,01.',
        ];
    }
}

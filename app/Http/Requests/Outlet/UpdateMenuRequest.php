<?php

namespace App\Http\Requests\Outlet;

use App\Models\Menu;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
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
            'nama' => ['required', 'string', 'max:120'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'harga' => ['required', 'integer', 'min:1000', 'max:500000'],
            'stok_porsi' => ['required', 'integer', 'min:0', 'max:5000'],
            'tersedia' => ['nullable', 'boolean'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama menu wajib diisi.',
            'category_id.required' => 'Kategori menu wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'harga.required' => 'Harga menu wajib diisi.',
            'harga.min' => 'Harga menu minimal Rp 1.000.',
            'stok_porsi.required' => 'Stok porsi wajib diisi.',
            'stok_porsi.min' => 'Stok porsi tidak boleh kurang dari 0.',
            'foto.image' => 'File foto menu harus berupa gambar.',
            'foto.max' => 'Ukuran foto menu maksimal 2 MB.',
        ];
    }
}

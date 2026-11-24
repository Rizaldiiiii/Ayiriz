<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAntreanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_pemesan' => ['required', 'string', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pemesan.required' => 'Nama pemesan wajib diisi agar kasir dapat memanggil pesanan Anda.',
            'nama_pemesan.max' => 'Nama pemesan maksimal 120 karakter.',
        ];
    }
}

<?php

namespace App\Http\Requests\Pusat;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOutletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isPusat();
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:120'],
            'pemilik' => ['required', 'string', 'max:120'],
            'no_hp' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string', 'max:255'],
            'jam_buka' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama outlet/kantin wajib diisi.',
            'pemilik.required' => 'Nama pemilik UMKM wajib diisi.',
            'no_hp.required' => 'Nomor HP pemilik kantin wajib diisi.',
            'alamat.required' => 'Alamat/lokasi kantin wajib diisi.',
            'jam_buka.required' => 'Jam operasional buka wajib diisi.',
        ];
    }
}

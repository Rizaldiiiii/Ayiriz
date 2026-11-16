<?php

namespace App\Http\Requests\Pusat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreOutletRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', Password::min(6)],
            'no_hp' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string', 'max:255'],
            'jam_buka' => ['required', 'string', 'max:100'],
            'royalty_percent' => ['nullable', 'numeric', 'min:10', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama outlet/kantin wajib diisi.',
            'pemilik.required' => 'Nama pemilik UMKM wajib diisi.',
            'email.required' => 'Email login akun outlet wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai oleh akun lain.',
            'password.required' => 'Kata sandi akun outlet wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'no_hp.required' => 'Nomor HP pemilik kantin wajib diisi.',
            'alamat.required' => 'Alamat/lokasi kantin wajib diisi.',
            'jam_buka.required' => 'Jam operasional buka wajib diisi.',
        ];
    }
}

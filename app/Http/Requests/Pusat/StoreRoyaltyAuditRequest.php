<?php

namespace App\Http\Requests\Pusat;

use App\Enums\RoyaltyDecision;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoyaltyAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isPusat();
    }

    public function rules(): array
    {
        return [
            'outlet_id' => ['required', 'integer', 'exists:outlets,id'],
            'semester' => ['required', 'string', 'max:50'],
            'keputusan' => ['required', 'string', Rule::in(array_map(fn (RoyaltyDecision $c) => $c->value, RoyaltyDecision::cases()))],
            'percent_baru' => ['nullable', 'numeric', 'min:10', 'max:50'],
            'catatan' => ['nullable', 'string', 'max:1000', 'required_if:keputusan,naik'],
        ];
    }

    public function messages(): array
    {
        return [
            'outlet_id.required' => 'Silakan pilih kantin mitra yang akan diaudit.',
            'outlet_id.exists' => 'Kantin mitra yang dipilih tidak valid.',
            'semester.required' => 'Periode semester audit wajib diisi.',
            'keputusan.required' => 'Keputusan royalti periode berikutnya wajib dipilih (Tetap atau Naik).',
            'keputusan.in' => 'Keputusan audit hanya boleh Tetap atau Naik.',
            'percent_baru.numeric' => 'Persen royalti baru harus berupa angka.',
            'percent_baru.min' => 'Persen royalti minimal adalah 10%.',
            'percent_baru.max' => 'Persen royalti maksimal adalah 50%.',
            'catatan.required_if' => 'Alasan kenaikan royalti wajib ditulis oleh Pusat saat memilih keputusan Naik.',
        ];
    }
}

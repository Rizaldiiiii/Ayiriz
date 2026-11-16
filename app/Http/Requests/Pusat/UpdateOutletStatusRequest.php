<?php

namespace App\Http\Requests\Pusat;

use App\Enums\OutletStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOutletStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isPusat();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(OutletStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status outlet wajib dipilih.',
            'status.Illuminate\Validation\Rules\Enum' => 'Status outlet tidak valid.',
        ];
    }
}

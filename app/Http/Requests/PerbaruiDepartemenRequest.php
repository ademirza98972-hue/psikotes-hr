<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerbaruiDepartemenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $departemen = $this->route('departemen');

        return [
            'nama_departemen' => [
                'required',
                'string',
                'max:100',
                Rule::unique('departemen', 'nama_departemen')
                    ->whereNull('deleted_at')
                    ->ignore($departemen?->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max'      => ':attribute maksimal :max karakter.',
            'unique'   => ':attribute sudah terdaftar.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_departemen' => 'nama departemen',
        ];
    }
}

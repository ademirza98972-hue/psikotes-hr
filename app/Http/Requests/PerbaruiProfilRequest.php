<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerbaruiProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'no_hp' => ['required', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max'      => ':attribute maksimal 20 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'no_hp' => 'Nomor HP',
        ];
    }
}

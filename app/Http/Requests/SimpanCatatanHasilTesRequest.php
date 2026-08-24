<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanCatatanHasilTesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'catatan_hr' => 'nullable|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'max' => ':attribute maksimal :max karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'catatan_hr' => 'catatan HR',
        ];
    }
}

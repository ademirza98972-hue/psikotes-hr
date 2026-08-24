<?php

namespace App\Http\Requests;

use App\Rules\UniqueDepartemen;
use Illuminate\Foundation\Http\FormRequest;

class SimpanDepartemenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_departemen' => [
                'required',
                'string',
                'max:100',
                new UniqueDepartemen(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'max'      => ':attribute maksimal :max karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_departemen' => 'nama departemen',
        ];
    }
}

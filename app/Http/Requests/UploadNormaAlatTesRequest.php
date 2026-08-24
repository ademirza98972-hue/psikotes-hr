<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadNormaAlatTesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file_csv' => 'required|file|mimes:csv,txt|max:2048',
            'hapus_lama' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'mimes'    => ':attribute harus berformat csv atau txt.',
            'max'      => ':attribute maksimal :max kilobyte.',
        ];
    }

    public function attributes(): array
    {
        return [
            'file_csv' => 'file norma CSV',
            'hapus_lama' => 'hapus data lama',
        ];
    }
}

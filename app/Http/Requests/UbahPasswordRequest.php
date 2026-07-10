<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UbahPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password_lama'       => ['required', 'string'],
            'password_baru'       => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'min'      => ':attribute minimal 8 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'password_lama'       => 'Password lama',
            'password_baru'       => 'Password baru',
            'konfirmasi_password_baru' => 'Konfirmasi password baru',
        ];
    }
}

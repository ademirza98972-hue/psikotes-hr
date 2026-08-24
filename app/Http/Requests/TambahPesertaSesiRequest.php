<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TambahPesertaSesiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_ids'     => 'required|array|min:1',
            'user_ids.*'   => 'exists:users,id',
            'alat_tes_ids' => 'required|array|min:1',
            'alat_tes_ids.*' => 'exists:alat_tes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'min'      => ':attribute minimal :min item.',
        ];
    }
}

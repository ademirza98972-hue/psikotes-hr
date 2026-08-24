<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerbaruiPosisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_posisi'   => 'required|string|max:100',
            'departemen_id' => 'required|exists:departemen,id',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'exists'   => ':attribute tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_posisi'   => 'nama posisi',
            'departemen_id' => 'departemen',
        ];
    }
}

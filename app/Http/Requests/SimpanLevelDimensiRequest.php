<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanLevelDimensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'levels'           => 'required|array|min:1',
            'levels.*.label'   => 'required|string|max:50',
            'levels.*.skor_min'=> 'required|numeric',
            'levels.*.skor_max'=> 'required|numeric',
            'levels.*.urutan'  => 'required|integer|min:0',
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

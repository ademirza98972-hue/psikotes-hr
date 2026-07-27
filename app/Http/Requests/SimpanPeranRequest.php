<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SimpanPeranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_peran' => [
                'required',
                'string',
                'max:255',
                Rule::unique('peran', 'nama_peran')->whereNull('deleted_at'),
            ],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'izin' => ['required', 'array', 'min:1'],
            'izin.*' => [
                'integer',
                function ($attribute, $value, $fail) {
                    if (! DB::table('izin')
                        ->where('id', $value)
                        ->exists()) {
                        $fail(':attribute tidak valid.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'unique' => ':attribute sudah terdaftar.',
            'izin.required' => 'Pilih minimal satu izin.',
            'izin.min' => 'Pilih minimal satu izin.',
            'izin.*.exists' => 'Izin tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_peran' => 'nama peran',
            'deskripsi' => 'deskripsi',
            'izin' => 'izin',
        ];
    }
}
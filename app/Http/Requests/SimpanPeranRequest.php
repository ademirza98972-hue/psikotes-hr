<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanPeranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_peran' => ['required', 'string', 'max:255', 'unique:peran,nama_peran'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'izin' => ['required', 'array', 'min:1'],
            'izin.*' => ['integer', 'exists:izin,id'],
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
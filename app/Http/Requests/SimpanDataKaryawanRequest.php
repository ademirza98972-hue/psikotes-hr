<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanDataKaryawanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik_karyawan' => [
                'required',
                'string',
                'max:30',
                \Illuminate\Validation\Rule::unique('data_karyawan', 'nik_karyawan')->whereNull('deleted_at'),
            ],
            'nama_karyawan' => ['required', 'string', 'max:255'],
            'departemen_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!\Illuminate\Support\Facades\DB::table('departemen')
                        ->whereNull('deleted_at')
                        ->where('id', $value)
                        ->exists()) {
                        $fail(':attribute tidak valid.');
                    }
                },
            ],
            'posisi_id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!\Illuminate\Support\Facades\DB::table('posisi')
                        ->whereNull('deleted_at')
                        ->where('id', $value)
                        ->exists()) {
                        $fail(':attribute tidak valid.');
                    }
                },
            ],
            'departemen' => ['required', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'unique' => ':attribute sudah terdaftar.',
            'exists' => ':attribute tidak valid.',
            'max' => ':attribute maksimal :max karakter.',
            'integer' => ':attribute harus berupa angka.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nik_karyawan' => 'NIK',
            'nama_karyawan' => 'nama karyawan',
            'departemen_id' => 'departemen',
            'posisi_id' => 'jabatan',
            'departemen' => 'departemen',
            'jabatan' => 'jabatan',
        ];
    }
}
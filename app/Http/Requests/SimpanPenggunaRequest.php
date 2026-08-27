<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanPenggunaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasIzin('pengguna.tambah');
    }

    public function rules(): array
    {
        return [
            'nama_karyawan' => ['required', 'string', 'max:255'],
            'nik_karyawan' => [
                'required',
                'string',
                'max:30',
                function ($attribute, $value, $fail) {
                    if (\Illuminate\Support\Facades\DB::table('profil_karyawan')
                        ->where('nik_karyawan', $value)
                        ->exists()) {
                        $fail('NIK sudah terdaftar pada sistem.');
                    }
                },
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->whereNull('deleted_at')
                    ->where('tipe_akun', 'karyawan'),
            ],
            'no_hp' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'email' => 'Format :attribute tidak valid.',
            'unique' => ':attribute sudah terdaftar.',
            'min' => ':attribute minimal :min karakter.',
            'confirmed' => 'Konfirmasi :attribute tidak cocok.',
            'max' => ':attribute maksimal :max karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_karyawan' => 'nama karyawan',
            'nik_karyawan' => 'NIK',
            'email' => 'email',
            'no_hp' => 'no HP',
            'password' => 'password',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
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
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->whereNull('deleted_at')
                    ->where('tipe_akun', 'karyawan'),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'no_hp' => ['required', 'string', 'max:30'],
            'nik_karyawan' => [
                'required',
                'string',
                'max:30',
                function ($attribute, $value, $fail) {
                    // Unique check against profil_karyawan (no soft-delete column exists for this table)
                    if (\Illuminate\Support\Facades\DB::table('profil_karyawan')
                        ->where('nik_karyawan', $value)
                        ->exists()) {
                        $fail('NIK sudah terdaftar pada sistem.');
                    }
                },
            ],
            'nama_karyawan' => ['required', 'string', 'max:255'],
            'departemen' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (! DB::table('departemen')
                        ->whereNull('deleted_at')
                        ->where('id', $value)
                        ->exists()) {
                        $fail(':attribute tidak valid.');
                    }
                },
            ],
            'jabatan' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if (null !== $value
                        && ! DB::table('posisi')
                            ->whereNull('deleted_at')
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
            'email' => 'Format :attribute tidak valid.',
            'unique' => ':attribute sudah terdaftar.',
            'min' => ':attribute minimal :min karakter.',
            'confirmed' => 'Konfirmasi :attribute tidak cocok.',
            'in' => ':attribute tidak valid.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'max' => ':attribute maksimal :max karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'email',
            'password' => 'password',
            'no_hp' => 'no HP',
            'nik_karyawan' => 'NIK',
            'nama_karyawan' => 'nama karyawan',
            'departemen' => 'departemen',
            'jabatan' => 'jabatan',
        ];
    }
}
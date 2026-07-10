<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanPenggunaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasIzin('pengguna.tambah');
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'no_hp' => ['required', 'string', 'max:30'],
            'nik_karyawan' => ['required', 'string', 'max:30', 'unique:profil_karyawan,nik_karyawan'],
            'nama_karyawan' => ['required', 'string', 'max:255'],
            'departemen' => ['required', 'integer', 'exists:departemen,id'],
            'jabatan' => ['nullable', 'integer', 'exists:posisi,id'],
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
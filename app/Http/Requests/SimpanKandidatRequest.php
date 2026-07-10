<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanKandidatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasIzin('pengguna.tambah');
    }

    public function rules(): array
    {
        return [
            'nama_kandidat' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'no_hp' => ['required', 'string', 'max:30'],
            'posisi_dilamar' => ['required', 'string', 'max:255'],
            'pendidikan_terakhir' => ['required', 'string', 'max:255'],
            'nik_kandidat' => ['nullable', 'string', 'max:30', 'unique:profil_kandidat,nik_kandidat'],
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
            'nama_kandidat' => 'nama kandidat',
            'email' => 'email',
            'password' => 'password',
            'no_hp' => 'no HP',
            'posisi_dilamar' => 'posisi yang dilamar',
            'pendidikan_terakhir' => 'pendidikan terakhir',
            'nik_kandidat' => 'NIK KTP',
        ];
    }
}
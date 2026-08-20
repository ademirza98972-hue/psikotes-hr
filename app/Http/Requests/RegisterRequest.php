<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $aturan = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'no_hp' => ['required', 'string', 'max:30'],
            'tipe_akun' => ['required', 'in:kandidat,karyawan,custom'],
        ];

        if ($this->input('tipe_akun') === 'kandidat') {
            $aturan['posisi_dilamar'] = ['required', 'string', 'max:255'];
            $aturan['pendidikan_terakhir'] = ['required', 'string', 'max:255'];
            $aturan['nik_kandidat'] = ['required', 'string', 'digits:16'];
            $aturan['tanggal_lahir'] = ['nullable', 'date'];
        } elseif ($this->input('tipe_akun') === 'karyawan') {
            $aturan['nik_karyawan'] = ['required', 'string', 'max:30'];
            $aturan['tanggal_lahir'] = ['nullable', 'date'];
        }

        return $aturan;
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
            'max' => ':attribute maksimal :max karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'email',
            'password' => 'password',
            'no_hp' => 'no HP',
            'tipe_akun' => 'tipe akun',
            'posisi_dilamar' => 'posisi yang dilamar',
            'pendidikan_terakhir' => 'pendidikan terakhir',
            'nik_kandidat' => 'nik KTP',
            'nik_karyawan' => 'NIK',
        ];
    }
}
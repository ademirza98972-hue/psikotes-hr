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
            $aturan['posisi_dilamar'] = ['required', 'integer', 'exists:posisi,id'];
            $aturan['pendidikan_terakhir'] = ['required', 'string', 'max:255'];
            $aturan['nik_kandidat'] = ['required', 'string', 'digits:16'];
            $aturan['tanggal_lahir'] = ['nullable', 'date'];
            $aturan['jenis_kelamin'] = ['required', 'in:L,P'];
        } elseif ($this->input('tipe_akun') === 'karyawan') {
            $aturan['nik_karyawan'] = ['required', 'string', 'max:30'];
        }

        return $aturan;
    }

    public function messages(): array
    {
        return [
            'required' => ':Attribute wajib diisi.',
            'email' => 'Format :attribute tidak valid.',
            'unique' => ':Attribute sudah terdaftar.',
            'min' => ':Attribute minimal :min karakter.',
            'confirmed' => 'Konfirmasi :attribute tidak cocok.',
            'in' => ':Attribute tidak valid.',
            'max' => ':Attribute maksimal :max karakter.',
            'digits' => ':Attribute harus :digits digit angka.',
            'integer' => ':Attribute tidak valid.',
            'exists' => ':Attribute tidak ditemukan.',
            'date' => ':Attribute bukan tanggal yang valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'email',
            'password' => 'password',
            'no_hp' => 'no. HP',
            'tipe_akun' => 'tipe akun',
            'posisi_dilamar' => 'posisi yang dilamar',
            'pendidikan_terakhir' => 'pendidikan terakhir',
            'nik_kandidat' => 'NIK KTP',
            'nik_karyawan' => 'NIK',
            'jenis_kelamin' => 'jenis kelamin',
        ];
    }
}
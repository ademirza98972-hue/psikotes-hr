<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerbaruiPenggunaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasIzin('pengguna.edit');
    }

    public function rules(): array
    {
        $idPengguna = $this->route('pengguna');

        $aturan = [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($idPengguna)
                    ->whereNull('deleted_at'),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'no_hp' => ['required', 'string', 'max:30'],
            'tipe_akun' => ['nullable', Rule::in(['kandidat', 'karyawan'])],
        ];

        $tipe = $this->input('tipe_akun');

        if ($tipe === 'kandidat') {
            $aturan['nama_kandidat'] = ['required', 'string', 'max:255'];
            $aturan['posisi_dilamar'] = ['required', 'string', 'max:255'];
            $aturan['pendidikan_terakhir'] = ['required', 'string', 'max:255'];
            $aturan['nik_kandidat'] = [
                'required', 'string', 'digits:16',
                Rule::unique('profil_kandidat', 'nik_kandidat')->ignore($idPengguna, 'user_id'),
            ];
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
            'tipe_akun' => 'tipe akun',
            'nama_kandidat' => 'nama kandidat',
            'posisi_dilamar' => 'posisi yang dilamar',
            'pendidikan_terakhir' => 'pendidikan terakhir',
            'nik_kandidat' => 'NIK KTP',
        ];
    }
}
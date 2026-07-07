<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanPenggunaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $aturan = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'no_hp' => ['required', 'string', 'max:30'],
            'tipe_akun' => ['required', 'in:kandidat,karyawan'],
            'peran_id' => ['required', 'integer', 'exists:peran,id'],
            'status' => ['required', 'in:aktif,menunggu_verifikasi,nonaktif'],
        ];

        if ($this->input('tipe_akun') === 'kandidat') {
            $aturan['posisi_dilamar'] = ['required', 'string', 'max:255'];
            $aturan['pendidikan_terakhir'] = ['required', 'string', 'max:255'];
            $aturan['no_ktp'] = ['nullable', 'string', 'max:30'];
        } elseif ($this->input('tipe_akun') === 'karyawan') {
            $aturan['nik'] = ['required', 'string', 'max:30', 'unique:profil_karyawan,nik'];
            $aturan['departemen'] = ['required', 'string', 'max:255'];
            $aturan['jabatan'] = ['nullable', 'string', 'max:255'];
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
            'name' => 'nama',
            'email' => 'email',
            'password' => 'password',
            'no_hp' => 'no HP',
            'tipe_akun' => 'tipe akun',
            'peran_id' => 'peran',
            'status' => 'status',
            'posisi_dilamar' => 'posisi yang dilamar',
            'pendidikan_terakhir' => 'pendidikan terakhir',
            'no_ktp' => 'no KTP',
            'nik' => 'NIK',
            'departemen' => 'departemen',
            'jabatan' => 'jabatan',
        ];
    }
}
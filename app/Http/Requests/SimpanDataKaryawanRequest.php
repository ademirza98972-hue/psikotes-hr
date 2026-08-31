<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanDataKaryawanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_karyawan'         => ['required', 'string', 'max:30', Rule::unique('users', 'id_karyawan')->whereNull('deleted_at')],
            'nik_karyawan'        => ['required', 'string', 'max:30', Rule::unique('data_karyawan', 'nik_karyawan')->whereNull('deleted_at')],
            'nama_karyawan'       => ['required', 'string', 'max:255'],
            'jenis_kelamin'       => ['required', 'in:L,P'],
            'tanggal_lahir'       => ['required', 'date'],
            'pendidikan_terakhir' => ['nullable', 'string', 'max:255'],
            'departemen_id'       => ['required', 'integer', 'exists:departemen,id'],
            'posisi_id'           => ['required', 'integer', 'exists:posisi,id'],
            'email'               => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'no_hp'               => ['nullable', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'unique'   => ':attribute sudah terdaftar.',
            'exists'   => ':attribute tidak valid.',
            'max'      => ':attribute maksimal :max karakter.',
            'integer'  => ':attribute harus berupa angka.',
            'email'    => 'Format :attribute tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_karyawan'         => 'ID karyawan',
            'nik_karyawan'        => 'NIK',
            'nama_karyawan'       => 'nama karyawan',
            'jenis_kelamin'       => 'jenis kelamin',
            'tanggal_lahir'       => 'tanggal lahir',
            'pendidikan_terakhir' => 'pendidikan terakhir',
            'departemen_id'       => 'departemen',
            'posisi_id'           => 'posisi',
            'email'               => 'email',
            'no_hp'               => 'nomor HP',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerbaruiPenggunaAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasIzin('pengguna_admin.kelola');
    }

    public function rules(): array
    {
        $idPengguna = $this->route('pengguna_admin');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($idPengguna)
                    ->whereNull('deleted_at')
                    ->where('tipe_akun', 'custom'),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'no_hp' => ['required', 'string', 'max:30'],
            'peran_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (! \Illuminate\Support\Facades\DB::table('peran')
                        ->whereNull('deleted_at')
                        ->whereNotIn('nama_peran', ['Kandidat', 'Karyawan'])
                        ->where('id', $value)
                        ->exists()) {
                        $fail(':attribute tidak valid.');
                    }
                },
            ],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
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
            'name' => 'nama',
            'email' => 'email',
            'password' => 'password',
            'no_hp' => 'no HP',
            'peran_id' => 'peran',
            'status' => 'status',
        ];
    }
}
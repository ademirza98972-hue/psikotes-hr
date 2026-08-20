<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PerbaruiKandidatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasIzin('pengguna.edit');
    }

    public function rules(): array
    {
        $userId = $this->route('kandidat') ?? $this->route('pengguna');

        $profilId = null;
        if ($userId) {
            $profilId = \App\Models\ProfilKandidat::where('user_id', $userId)->value('id');
        }

        return [
            'departemen' => [
                'required',
                'int',
                function ($attribute, $value, $fail) {
                    if (! DB::table('departemen')->whereNull('deleted_at')->where('id', $value)->exists()) {
                        $fail(':attribute tidak valid.');
                    }
                },
            ],
            'posisi_dilamar' => [
                'required',
                'int',
                function ($attribute, $value, $fail) {
                    if (! DB::table('posisi')->whereNull('deleted_at')->where('id', $value)->exists()) {
                        $fail(':attribute tidak valid.');
                    }
                },
            ],
            'nama_kandidat' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at'),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'no_hp' => ['required', 'string', 'max:30'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'pendidikan_terakhir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'nik_kandidat' => ['required', 'string', 'digits:16'],
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
            'departemen' => 'departemen',
            'posisi_dilamar' => 'posisi yang dilamar',
            'nama_kandidat' => 'nama kandidat',
            'email' => 'email',
            'password' => 'password',
            'no_hp' => 'no HP',
            'jenis_kelamin' => 'jenis kelamin',
            'pendidikan_terakhir' => 'pendidikan terakhir',
            'nik_kandidat' => 'NIK KTP',
        ];
    }
}
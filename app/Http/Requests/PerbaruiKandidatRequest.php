<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
            'departemen' => ['required', 'int', 'exists:departemen,id'],
            'posisi_dilamar' => ['required', 'int', 'exists:posisi,id'],
            'nama_kandidat' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'no_hp' => ['required', 'string', 'max:30'],
            'pendidikan_terakhir' => ['required', 'string', 'max:255'],
            'nik_kandidat' => ['required', 'string', 'digits:16', Rule::unique('profil_kandidat', 'nik_kandidat')->ignore($profilId)],
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
            'pendidikan_terakhir' => 'pendidikan terakhir',
            'nik_kandidat' => 'NIK KTP',
        ];
    }
}
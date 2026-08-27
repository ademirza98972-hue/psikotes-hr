<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerbaruiAlatTesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'nama' => 'required|string|max:150',
            'kode' => ['required', 'string', 'max:20', Rule::unique('alat_tes', 'kode')->ignore($id)],
            'format_dasar' => 'required|string|max:50',
            'pola_skoring' => 'required|string|in:kognitif,forced_choice,forced_choice_rollup,grid',
            'jumlah_soal' => 'nullable|integer|min:0',
            'durasi_total_menit' => 'nullable|integer|min:0',
            'batas_waktu_per_soal_aktif' => 'boolean',
            'batas_waktu_per_soal_detik' => 'nullable|integer|min:1',
            'is_sensitif' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'unique'   => ':attribute sudah terdaftar.',
            'in'       => ':attribute tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama alat tes',
            'kode' => 'kode alat tes',
            'format_dasar' => 'format dasar',
            'pola_skoring' => 'pola skoring',
            'jumlah_soal' => 'jumlah soal',
            'durasi_total_menit' => 'durasi total (menit)',
            'batas_waktu_per_soal_detik' => 'batas waktu per soal (detik)',
            'is_sensitif' => 'sensitif',
        ];
    }
}

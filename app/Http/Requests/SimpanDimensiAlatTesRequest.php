<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanDimensiAlatTesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_dimensi'   => 'required|string|max:50',
            'nama_dimensi'   => 'required|string|max:150',
            'deskripsi_aspek'=> 'nullable|string',
            'tipe_kategori'  => 'required|in:psikogram,klinis',
            'arah_skor'      => 'required|in:tinggi_baik,rendah_baik',
            'urutan'         => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'in'       => ':attribute tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'kode_dimensi' => 'kode dimensi',
            'nama_dimensi' => 'nama dimensi',
            'deskripsi_aspek' => 'deskripsi aspek',
            'tipe_kategori' => 'tipe kategori',
            'arah_skor' => 'arah skor',
            'urutan' => 'urutan',
        ];
    }
}

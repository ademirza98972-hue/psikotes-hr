<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerbaruiSesiTesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_sesi'      => 'required|string|max:255',
            'departemen_ids' => 'nullable|array',
            'departemen_ids.*' => 'exists:departemen,id',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alat_tes_ids'   => 'nullable|array',
            'alat_tes_ids.*' => 'exists:alat_tes,id',
            'status'         => 'required|' . Rule::in(['Draft', 'Aktif', 'Selesai']),
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_sesi' => 'nama sesi',
            'tanggal_mulai' => 'tanggal mulai',
            'tanggal_selesai' => 'tanggal selesai',
            'status' => 'status',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnggahFotoProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foto_profil' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Silakan pilih foto untuk diunggah.',
            'image'    => ':attribute harus berupa gambar.',
            'mimes'    => ':attribute berformat tidak didukung. Gunakan format JPG, JPEG, atau PNG.',
            'max'      => ':attribute maksimal berukuran 2 MB (2048 KB).',
        ];
    }

    public function attributes(): array
    {
        return [
            'foto_profil' => 'Foto profil',
        ];
    }
}

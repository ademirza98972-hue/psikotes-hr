<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueDepartemen implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = DB::table('departemen')
            ->where('nama_departemen', $value)
            ->exists();

        if ($exists) {
            $fail('Nama departemen sudah terdaftar atau pernah digunakan sebelumnya. Gunakan nama lain atau pulihkan dari Data Terhapus.');
        }
    }
}

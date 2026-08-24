<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class DistinctDepartemen implements ValidationRule
{
    protected int $excludeId;

    public function __construct(int $excludeId)
    {
        $this->excludeId = $excludeId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = DB::table('departemen')
            ->whereNull('deleted_at')
            ->where('nama_departemen', $value)
            ->where('id', '!=', $this->excludeId)
            ->exists();

        if ($exists) {
            $fail('Nama departemen sudah terdaftar.');
        }
    }
}

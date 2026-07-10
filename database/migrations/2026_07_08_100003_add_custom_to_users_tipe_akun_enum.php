<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN tipe_akun ENUM('kandidat', 'karyawan', 'custom') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN tipe_akun ENUM('kandidat', 'karyawan') NULL");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE soal MODIFY COLUMN tipe_format ENUM('pilihan_ganda', 'grid', 'forced_choice', 'isian_teks', 'isian_angka', 'pilihan_gambar', 'memori') NOT NULL DEFAULT 'pilihan_ganda'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE soal MODIFY COLUMN tipe_format ENUM('pilihan_ganda', 'grid', 'forced_choice') NOT NULL DEFAULT 'pilihan_ganda'");
    }
};

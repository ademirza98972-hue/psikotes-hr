<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('aktif', 'menunggu_verifikasi', 'nonaktif', 'ditolak') NOT NULL DEFAULT 'menunggu_verifikasi'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('aktif', 'menunggu_verifikasi', 'nonaktif') NOT NULL DEFAULT 'menunggu_verifikasi'");
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta_alat_tes', function (Blueprint $table) {
            $table->timestamp('waktu_mulai_sesi')->nullable()->after('waktu_mulai_kolom');
            $table->timestamp('waktu_mulai_subtes')->nullable()->after('waktu_mulai_sesi');
        });
    }

    public function down(): void
    {
        Schema::table('peserta_alat_tes', function (Blueprint $table) {
            $table->dropColumn(['waktu_mulai_sesi', 'waktu_mulai_subtes']);
        });
    }
};

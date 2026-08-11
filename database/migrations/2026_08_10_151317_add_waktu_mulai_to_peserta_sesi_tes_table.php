<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peserta_sesi_tes', function (Blueprint $table) {
            $table->timestamp('waktu_mulai')->nullable()->after('tanggal_pengerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta_sesi_tes', function (Blueprint $table) {
            $table->dropColumn('waktu_mulai');
        });
    }
};

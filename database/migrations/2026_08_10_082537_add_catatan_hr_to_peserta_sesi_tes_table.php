<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta_sesi_tes', function (Blueprint $table) {
            $table->text('catatan_hr')->nullable()->after('tanggal_pengerjaan');
        });
    }

    public function down(): void
    {
        Schema::table('peserta_sesi_tes', function (Blueprint $table) {
            $table->dropColumn('catatan_hr');
        });
    }
};

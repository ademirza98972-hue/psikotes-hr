<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta_alat_tes', function (Blueprint $table) {
            $table->unsignedInteger('kolom_ke')->nullable()->after('waktu_mulai_kolom');
        });
    }

    public function down(): void
    {
        Schema::table('peserta_alat_tes', function (Blueprint $table) {
            $table->dropColumn('kolom_ke');
        });
    }
};

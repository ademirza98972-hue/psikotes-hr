<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->string('gambar_soal')->nullable()->after('teks_soal');
        });

        Schema::table('opsi_jawaban', function (Blueprint $table) {
            $table->string('gambar_opsi')->nullable()->after('teks_opsi');
        });
    }

    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->dropColumn('gambar_soal');
        });

        Schema::table('opsi_jawaban', function (Blueprint $table) {
            $table->dropColumn('gambar_opsi');
        });
    }
};

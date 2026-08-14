<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dimensi_alat_tes', function (Blueprint $table) {
            $table->integer('durasi_detik')->nullable()->after('urutan');
            $table->text('instruksi_subtes')->nullable()->after('durasi_detik');
        });

        Schema::table('soal', function (Blueprint $table) {
            $table->string('set_opsi')->nullable()->after('gambar_soal');
        });

        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->string('pendidikan_terakhir')->nullable()->after('jenis_kelamin');
        });
    }

    public function down(): void
    {
        Schema::table('dimensi_alat_tes', function (Blueprint $table) {
            $table->dropColumn(['durasi_detik', 'instruksi_subtes']);
        });

        Schema::table('soal', function (Blueprint $table) {
            $table->dropColumn('set_opsi');
        });

        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->dropColumn('pendidikan_terakhir');
        });
    }
};

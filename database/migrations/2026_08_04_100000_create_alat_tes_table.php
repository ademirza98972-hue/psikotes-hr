<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat_tes', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 150);
            $table->string('kategori', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('format_dasar', 50)->nullable(); // 'Skala Likert', 'Pilihan Ganda', 'Forced Choice'
            $table->unsignedInteger('durasi_total_menit')->nullable();
            $table->boolean('batas_waktu_per_soal_aktif')->default(false);
            $table->unsignedInteger('batas_waktu_per_soal_detik')->nullable();
            $table->boolean('is_sensitif')->default(false); // terhubung ke izin hasil_tes.lihat_sensitif
            $table->unsignedInteger('jumlah_soal')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat_tes');
    }
};

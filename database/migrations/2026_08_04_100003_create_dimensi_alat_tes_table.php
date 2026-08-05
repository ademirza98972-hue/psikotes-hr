<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dimensi_alat_tes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_tes_id')->constrained('alat_tes')->cascadeOnDelete();
            $table->string('kode_dimensi', 20);
            $table->string('nama_dimensi', 150);
            $table->text('deskripsi_aspek')->nullable(); // teks di bawah nama dimensi di psikogram
            $table->enum('tipe_kategori', ['psikogram', 'klinis'])->default('psikogram');
            $table->enum('arah_skor', ['tinggi_baik', 'rendah_baik'])->default('tinggi_baik');
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
            $table->unique(['alat_tes_id', 'kode_dimensi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dimensi_alat_tes');
    }
};

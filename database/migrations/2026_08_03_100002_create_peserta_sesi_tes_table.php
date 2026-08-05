<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_sesi_tes', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sesi_tes_id')->constrained('sesi_tes')->cascadeOnDelete();
            $table->enum('status_pengerjaan', ['Belum Mengerjakan', 'Sedang Berjalan', 'Selesai'])->default('Belum Mengerjakan');
            $table->date('tanggal_pengerjaan')->nullable();
            $table->primary(['user_id', 'sesi_tes_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_sesi_tes');
    }
};

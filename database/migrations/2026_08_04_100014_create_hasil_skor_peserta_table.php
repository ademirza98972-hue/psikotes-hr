<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_skor_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('sesi_tes_id')->constrained('sesi_tes');
            $table->foreignId('alat_tes_id')->constrained('alat_tes');
            $table->foreignId('dimensi_id')->constrained('dimensi_alat_tes');
            $table->decimal('skor_mentah', 10, 2)->nullable();
            $table->decimal('skor_akhir', 10, 2)->nullable();
            $table->foreignId('level_id')->nullable()->constrained('level_dimensi');
            $table->timestamps();
            $table->unique(['user_id', 'sesi_tes_id', 'dimensi_id'], 'hasil_skor_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_skor_peserta');
    }
};

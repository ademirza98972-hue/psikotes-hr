<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('sesi_tes_id')->constrained('sesi_tes');
            $table->foreignId('soal_id')->constrained('soal');
            $table->foreignId('opsi_dipilih_id')->nullable()->constrained('opsi_jawaban');
            $table->text('jawaban_teks')->nullable();
            $table->decimal('nilai_input', 10, 2)->nullable();
            $table->timestamp('waktu_jawab')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'sesi_tes_id', 'soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_peserta');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grid_input_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('sesi_tes_id')->constrained('sesi_tes');
            $table->foreignId('alat_tes_id')->constrained('alat_tes');
            $table->unsignedInteger('kolom_ke');
            $table->unsignedInteger('baris_ke');
            $table->unsignedTinyInteger('jawaban_peserta')->nullable();
            $table->unsignedTinyInteger('jawaban_benar');
            $table->boolean('is_benar')->default(false);
            $table->timestamp('waktu_input')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'sesi_tes_id', 'alat_tes_id', 'kolom_ke'], 'grid_input_peserta_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grid_input_peserta');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_alat_tes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_sesi_tes_id')
                  ->constrained('peserta_sesi_tes')
                  ->cascadeOnDelete();
            $table->foreignId('alat_tes_id')
                  ->constrained('alat_tes')
                  ->restrictOnDelete();
            $table->timestamps();
            $table->unique(['peserta_sesi_tes_id', 'alat_tes_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_alat_tes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('norma_konversi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_tes_id')->constrained('alat_tes')->cascadeOnDelete();
            $table->foreignId('dimensi_id')->nullable()->constrained('dimensi_alat_tes')->nullOnDelete();
            $table->foreignId('sumber_dimensi_id')->nullable()->constrained('dimensi_alat_tes')->nullOnDelete();
            $table->string('kelompok_segmen', 50)->default('default');
            $table->unsignedTinyInteger('tahap')->default(1);
            $table->decimal('skor_mentah_min', 10, 2);
            $table->decimal('skor_mentah_max', 10, 2);
            $table->decimal('skor_hasil', 10, 2);
            $table->timestamps();
            $table->index(['alat_tes_id', 'dimensi_id', 'kelompok_segmen', 'tahap'], 'norma_konversi_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('norma_konversi');
    }
};

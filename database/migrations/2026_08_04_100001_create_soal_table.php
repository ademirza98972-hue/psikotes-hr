<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_tes_id')->constrained('alat_tes')->cascadeOnDelete();
            $table->unsignedInteger('nomor');
            $table->text('teks_soal');
            $table->enum('tipe_format', ['pilihan_ganda', 'skala_likert', 'forced_choice', 'grid', 'naratif']);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['alat_tes_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal');
    }
};

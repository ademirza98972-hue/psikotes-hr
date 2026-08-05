<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interpretasi_teks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimensi_id')->constrained('dimensi_alat_tes')->cascadeOnDelete();
            $table->foreignId('level_id')->constrained('level_dimensi')->cascadeOnDelete();
            $table->text('teks_narasi')->nullable();
            $table->timestamps();
            $table->unique(['dimensi_id', 'level_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interpretasi_teks');
    }
};

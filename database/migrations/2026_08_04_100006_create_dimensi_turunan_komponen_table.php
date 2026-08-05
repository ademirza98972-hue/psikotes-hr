<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dimensi_turunan_komponen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimensi_turunan_id')->constrained('dimensi_alat_tes')->cascadeOnDelete();
            $table->foreignId('dimensi_komponen_id')->constrained('dimensi_alat_tes')->cascadeOnDelete();
            $table->decimal('bobot', 8, 2)->default(1);
            $table->timestamps();
            $table->unique(['dimensi_turunan_id', 'dimensi_komponen_id'], 'turunan_komponen_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dimensi_turunan_komponen');
    }
};

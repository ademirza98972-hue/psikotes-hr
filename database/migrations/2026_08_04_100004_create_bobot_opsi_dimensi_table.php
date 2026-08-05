<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bobot_opsi_dimensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opsi_jawaban_id')->constrained('opsi_jawaban')->cascadeOnDelete();
            $table->foreignId('dimensi_id')->constrained('dimensi_alat_tes')->cascadeOnDelete();
            $table->decimal('bobot', 8, 2);
            $table->boolean('is_reverse')->default(false);
            $table->timestamps();
            $table->unique(['opsi_jawaban_id', 'dimensi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bobot_opsi_dimensi');
    }
};

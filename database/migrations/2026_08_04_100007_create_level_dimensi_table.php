<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('level_dimensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_tes_id')->constrained('alat_tes')->cascadeOnDelete();
            $table->foreignId('dimensi_id')->nullable()->constrained('dimensi_alat_tes')->cascadeOnDelete();
            $table->string('label', 50);
            $table->decimal('skor_min', 10, 2);
            $table->decimal('skor_max', 10, 2);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('level_dimensi');
    }
};

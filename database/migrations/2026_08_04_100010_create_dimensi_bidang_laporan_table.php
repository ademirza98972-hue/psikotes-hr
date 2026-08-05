<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dimensi_bidang_laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimensi_id')->constrained('dimensi_alat_tes')->cascadeOnDelete();
            $table->foreignId('bidang_laporan_id')->constrained('bidang_laporan')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['dimensi_id', 'bidang_laporan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dimensi_bidang_laporan');
    }
};

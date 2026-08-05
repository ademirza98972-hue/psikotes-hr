<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat_tes_sesi_tes', function (Blueprint $table) {
            $table->foreignId('sesi_tes_id')->constrained('sesi_tes')->cascadeOnDelete();
            $table->foreignId('alat_tes_id')->constrained('alat_tes')->cascadeOnDelete();
            $table->primary(['sesi_tes_id', 'alat_tes_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat_tes_sesi_tes');
    }
};

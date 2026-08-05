<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_kolom_grid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('sesi_tes_id')->constrained('sesi_tes');
            $table->foreignId('alat_tes_id')->constrained('alat_tes');
            $table->unsignedInteger('kolom_ke');
            $table->unsignedInteger('jumlah_benar')->default(0);
            $table->unsignedInteger('jumlah_salah')->default(0);
            $table->unsignedInteger('jumlah_kelewat')->default(0);
            $table->unsignedInteger('waktu_pakai_detik')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'sesi_tes_id', 'alat_tes_id', 'kolom_ke'], 'hasil_kolom_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_kolom_grid');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_aktivitas_tes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_sesi_tes_id')->constrained('peserta_sesi_tes')->cascadeOnDelete();
            $table->string('tipe_kejadian', 50);
            $table->boolean('mencurigakan')->default(true);
            $table->string('keterangan', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas_tes');
    }
};

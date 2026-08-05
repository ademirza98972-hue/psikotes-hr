<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_tes', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sesi', 150);
            $table->foreignId('departemen_terkait_id')->nullable()->constrained('departemen')->nullOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['Aktif', 'Selesai', 'Draft'])->default('Draft');
            $table->unsignedInteger('jumlah_peserta')->default(0);
            $table->unsignedInteger('jumlah_selesai')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['departemen_terkait_id', 'status']);
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_tes');
    }
};

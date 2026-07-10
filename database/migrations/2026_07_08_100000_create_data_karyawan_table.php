<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 30)->unique();
            $table->string('nama_karyawan');
            $table->string('departemen');
            $table->string('jabatan')->nullable();
            $table->enum('status', ['belum_terpakai', 'sudah_terpakai'])->default('belum_terpakai');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_karyawan');
    }
};

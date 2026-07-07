<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peran_izin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peran_id')->constrained('peran')->cascadeOnDelete();
            $table->foreignId('izin_id')->constrained('izin')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['peran_id', 'izin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peran_izin');
    }
};
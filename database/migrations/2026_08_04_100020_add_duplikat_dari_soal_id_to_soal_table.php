<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->foreignId('duplikat_dari_soal_id')
                ->nullable()
                ->constrained('soal')
                ->nullOnDelete()
                ->after('tipe_format');

            $table->index('duplikat_dari_soal_id');
        });
    }

    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->dropConstrainedForeignId('duplikat_dari_soal_id');
        });
    }
};
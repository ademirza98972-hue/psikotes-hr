<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->renameColumn('nik', 'nik_karyawan');
        });
    }

    public function down(): void
    {
        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->renameColumn('nik_karyawan', 'nik');
        });
    }
};
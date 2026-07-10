<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profil_karyawan', function (Blueprint $table) {
            $table->foreignId('data_karyawan_id')
                ->nullable()
                ->after('user_id')
                ->constrained('data_karyawan')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('profil_karyawan', function (Blueprint $table) {
            $table->dropForeign(['data_karyawan_id']);
            $table->dropColumn('data_karyawan_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('profil_karyawan')->whereNull('jabatan')->update([
            'jabatan' => DB::table('data_karyawan')
                ->select('jabatan')
                ->whereColumn('id', 'profil_karyawan.data_karyawan_id')
                ->limit(1),
        ]);

        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->dropForeign(['posisi_id']);
        });

        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->string('jabatan')->nullable(false)->change();
            $table->foreignId('posisi_id')->nullable(false)->change();
        });

        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->foreign('posisi_id')
                ->references('id')
                ->on('posisi')
                ->onDelete('restrict');
        });

        Schema::table('profil_karyawan', function (Blueprint $table) {
            $table->string('jabatan')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->dropForeign(['posisi_id']);
            $table->string('jabatan')->nullable()->change();
            $table->foreignId('posisi_id')->nullable()->change();
        });

        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->foreign('posisi_id')
                ->references('id')
                ->on('posisi')
                ->onDelete('set null');
        });

        Schema::table('profil_karyawan', function (Blueprint $table) {
            $table->string('jabatan')->nullable()->change();
        });
    }
};

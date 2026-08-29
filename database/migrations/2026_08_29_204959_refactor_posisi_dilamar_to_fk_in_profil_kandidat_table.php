<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profil_kandidat', function (Blueprint $table) {
            $table->unsignedBigInteger('posisi_id')->nullable()->after('posisi_dilamar');
        });

        // Map string → posisi.id
        DB::statement('
            UPDATE profil_kandidat pk
            JOIN posisi p ON p.nama_posisi = pk.posisi_dilamar
            SET pk.posisi_id = p.id
        ');

        Schema::table('profil_kandidat', function (Blueprint $table) {
            $table->foreign('posisi_id')->references('id')->on('posisi')->nullOnDelete();
            $table->dropColumn('posisi_dilamar');
        });
    }

    public function down(): void
    {
        Schema::table('profil_kandidat', function (Blueprint $table) {
            $table->string('posisi_dilamar')->nullable()->after('posisi_id');
        });

        DB::statement('
            UPDATE profil_kandidat pk
            JOIN posisi p ON p.id = pk.posisi_id
            SET pk.posisi_dilamar = p.nama_posisi
        ');

        Schema::table('profil_kandidat', function (Blueprint $table) {
            $table->dropForeign(['posisi_id']);
            $table->dropColumn('posisi_id');
        });
    }
};

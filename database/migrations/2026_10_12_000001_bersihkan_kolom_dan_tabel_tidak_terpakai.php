<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Departemen kandidat diturunkan dari posisi->departemen_id; kolom string ini redundan & sering NULL
        if (Schema::hasColumn('profil_kandidat', 'departemen')) {
            Schema::table('profil_kandidat', function (Blueprint $table) {
                $table->dropColumn('departemen');
            });
        }

        // Tidak pernah dipakai kode & selalu kosong
        Schema::dropIfExists('interpretasi_teks');
        Schema::dropIfExists('standar_kompetensi_posisi');
    }

    public function down(): void
    {
        Schema::table('profil_kandidat', function (Blueprint $table) {
            $table->string('departemen')->nullable()->after('user_id');
        });
    }
};

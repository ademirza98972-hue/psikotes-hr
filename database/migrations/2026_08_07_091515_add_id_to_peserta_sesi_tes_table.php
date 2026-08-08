<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peserta_sesi_tes', function (Blueprint $table) {
            // Index substitute for FK on user_id before dropping composite PK
            $table->index('user_id', 'peserta_sesi_tes_user_id_idx');
            $table->dropPrimary(['user_id', 'sesi_tes_id']);
            $table->bigIncrements('id')->first();
            $table->timestamps();
            $table->unique(['user_id', 'sesi_tes_id'], 'peserta_sesi_tes_user_sesi_unique');
        });
    }

    public function down(): void
    {
        Schema::table('peserta_sesi_tes', function (Blueprint $table) {
            $table->dropUnique('peserta_sesi_tes_user_sesi_unique');
            $table->dropTimestamps();
            $table->dropPrimary(['id']);
            $table->dropColumn('id');
            $table->dropIndex('peserta_sesi_tes_user_id_idx');
            $table->primary(['user_id', 'sesi_tes_id']);
        });
    }
};

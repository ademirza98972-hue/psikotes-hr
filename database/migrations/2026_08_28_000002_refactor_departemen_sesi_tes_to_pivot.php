<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departemen_sesi_tes', function (Blueprint $table) {
            $table->foreignId('sesi_tes_id')->constrained('sesi_tes')->cascadeOnDelete();
            $table->foreignId('departemen_id')->constrained('departemen')->cascadeOnDelete();
            $table->primary(['sesi_tes_id', 'departemen_id']);
        });

        // Migrate existing single-department links into the pivot
        DB::table('sesi_tes')
            ->whereNotNull('departemen_terkait_id')
            ->get(['id', 'departemen_terkait_id'])
            ->each(fn ($row) => DB::table('departemen_sesi_tes')->insert([
                'sesi_tes_id'   => $row->id,
                'departemen_id' => $row->departemen_terkait_id,
            ]));

        Schema::table('sesi_tes', function (Blueprint $table) {
            $table->dropForeign(['departemen_terkait_id']);
            $table->dropIndex('sesi_tes_departemen_terkait_id_status_index');
            $table->dropColumn('departemen_terkait_id');
        });
    }

    public function down(): void
    {
        Schema::table('sesi_tes', function (Blueprint $table) {
            $table->foreignId('departemen_terkait_id')->nullable()->constrained('departemen')->nullOnDelete()->after('nama_sesi');
            $table->index(['departemen_terkait_id', 'status']);
        });

        // Restore first department per sesi into the column
        DB::table('departemen_sesi_tes')
            ->orderBy('sesi_tes_id')
            ->get()
            ->groupBy('sesi_tes_id')
            ->each(fn ($rows, $sesiId) => DB::table('sesi_tes')
                ->where('id', $sesiId)
                ->update(['departemen_terkait_id' => $rows->first()->departemen_id]));

        Schema::dropIfExists('departemen_sesi_tes');
    }
};

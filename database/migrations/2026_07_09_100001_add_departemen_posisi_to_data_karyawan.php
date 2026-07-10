<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->foreignId('departemen_id')->nullable()->after('jabatan')->constrained('departemen')->onDelete('set null');
            $table->foreignId('posisi_id')->nullable()->after('departemen_id')->constrained('posisi')->onDelete('set null');
        });

        // Copy data to populate the new FK fields based on existing department/position names
        \DB::table('data_karyawan')->orderBy('id')->each(function ($row) {
            $departemen = \DB::table('departemen')->where('nama_departemen', $row->departemen)->first();
            $departemenId = $departemen?->id ?? null;

            if ($departemenId && $row->jabatan) {
                $posisi = \DB::table('posisi')->where('departemen_id', $departemenId)->where('nama_posisi', $row->jabatan)->first();
                $posisiId = $posisi?->id ?? null;
            } else {
                $posisiId = null;
            }

            \DB::table('data_karyawan')->where('id', $row->id)->update([
                'departemen_id' => $departemenId,
                'posisi_id' => $posisiId,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('data_karyawan', function (Blueprint $table) {
            $table->dropForeign(['departemen_id']);
            $table->dropColumn('departemen_id');
            $table->dropForeign(['posisi_id']);
            $table->dropColumn('posisi_id');
        });
    }
};

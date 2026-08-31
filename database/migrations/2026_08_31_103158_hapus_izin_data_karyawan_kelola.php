<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $id = DB::table('izin')->where('kode_izin', 'data_karyawan.kelola')->value('id');

        if ($id) {
            DB::table('peran_izin')->where('izin_id', $id)->delete();
            DB::table('izin')->where('id', $id)->delete();
        }
    }

    public function down(): void
    {
        DB::table('izin')->insertOrIgnore([
            'kode_izin' => 'data_karyawan.kelola',
            'deskripsi' => 'Kelola Data Karyawan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};

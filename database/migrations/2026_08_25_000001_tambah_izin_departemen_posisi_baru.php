<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insert dua izin baru
        DB::table('izin')->insertOrIgnore([
            ['kode_izin' => 'departemen.kelola', 'deskripsi' => 'Kelola Data Departemen', 'created_at' => now(), 'updated_at' => now()],
            ['kode_izin' => 'posisi.kelola',      'deskripsi' => 'Kelola Data Posisi',       'created_at' => now(), 'updated_at' => now()],
        ]);

        // Migrasi pivot: role mana pun yang punya master_data.kelola
        // sekarang juga dapat departemen.kelola + posisi.kelola
        $lama = DB::table('izin')->where('kode_izin', 'master_data.kelola')->first();
        $baruDepartemen = DB::table('izin')->where('kode_izin', 'departemen.kelola')->value('id');
        $baruPosisi     = DB::table('izin')->where('kode_izin', 'posisi.kelola')->value('id');

        if ($lama && $baruDepartemen && $baruPosisi) {
            // Ambil semua peran_id yang sebelumnya punya master_data.kelola
            $roleIds = DB::table('peran_izin')
                ->where('izin_id', $lama->id)
                ->pluck('peran_id');

            if ($roleIds->isNotEmpty()) {
                // Tambahkan departemen.kelola ke role yang sama
                foreach ($roleIds as $roleId) {
                    DB::table('peran_izin')->insertOrIgnore([
                        'peran_id' => $roleId,
                        'izin_id'  => $baruDepartemen,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('peran_izin')->insertOrIgnore([
                        'peran_id' => $roleId,
                        'izin_id'  => $baruPosisi,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Hapus izin lama dari tabel izin (tapi tetap simpan ref di pivot untuk backward compat)
            // Opsional: biarkan master_data.kelola tetap ada sebagai fallback
        }
    }

    public function down(): void
    {
        $baruIds = DB::table('izin')
            ->whereIn('kode_izin', ['departemen.kelola', 'posisi.kelola'])
            ->pluck('id');

        if ($baruIds->isNotEmpty()) {
            DB::table('peran_izin')->whereIn('izin_id', $baruIds)->delete();
            DB::table('izin')->whereIn('kode_izin', ['departemen.kelola', 'posisi.kelola'])->delete();
        }
    }
};

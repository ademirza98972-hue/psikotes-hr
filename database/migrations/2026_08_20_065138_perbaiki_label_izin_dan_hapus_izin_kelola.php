<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus izin.kelola — tidak ada route yang menggunakannya
        DB::table('peran_izin')
            ->whereIn('izin_id', DB::table('izin')->where('kode_izin', 'izin.kelola')->pluck('id'))
            ->delete();
        DB::table('izin')->where('kode_izin', 'izin.kelola')->delete();

        // Klarifikasi deskripsi soal.*: sebenarnya mengontrol Bank Soal & Alat Tes
        DB::table('izin')->where('kode_izin', 'soal.lihat')
            ->update(['deskripsi' => 'Akses menu Alat Tes, Bank Soal & Penjadwalan']);
        DB::table('izin')->where('kode_izin', 'soal.tambah')
            ->update(['deskripsi' => 'Tambah soal ke Bank Soal']);
        DB::table('izin')->where('kode_izin', 'soal.edit')
            ->update(['deskripsi' => 'Edit soal di Bank Soal']);
        DB::table('izin')->where('kode_izin', 'soal.hapus')
            ->update(['deskripsi' => 'Hapus soal dari Bank Soal']);

        // Klarifikasi deskripsi pengguna.*: mengontrol Akun Karyawan + Kandidat
        DB::table('izin')->where('kode_izin', 'pengguna.lihat')
            ->update(['deskripsi' => 'Lihat daftar Akun Karyawan & Kandidat']);
        DB::table('izin')->where('kode_izin', 'pengguna.tambah')
            ->update(['deskripsi' => 'Tambah Akun Karyawan & Kandidat baru']);
        DB::table('izin')->where('kode_izin', 'pengguna.edit')
            ->update(['deskripsi' => 'Edit & nonaktifkan Akun Karyawan / Kandidat']);
        DB::table('izin')->where('kode_izin', 'pengguna.hapus')
            ->update(['deskripsi' => 'Hapus Akun Karyawan & Kandidat']);
        DB::table('izin')->where('kode_izin', 'pengguna.verifikasi')
            ->update(['deskripsi' => 'Verifikasi / tolak pendaftaran Kandidat']);
    }

    public function down(): void
    {
        DB::table('izin')->where('kode_izin', 'soal.lihat')->update(['deskripsi' => 'Lihat Soal']);
        DB::table('izin')->where('kode_izin', 'soal.tambah')->update(['deskripsi' => 'Tambah Soal']);
        DB::table('izin')->where('kode_izin', 'soal.edit')->update(['deskripsi' => 'Edit Soal']);
        DB::table('izin')->where('kode_izin', 'soal.hapus')->update(['deskripsi' => 'Hapus Soal']);

        DB::table('izin')->where('kode_izin', 'pengguna.lihat')->update(['deskripsi' => 'Lihat Pengguna']);
        DB::table('izin')->where('kode_izin', 'pengguna.tambah')->update(['deskripsi' => 'Tambah Pengguna']);
        DB::table('izin')->where('kode_izin', 'pengguna.edit')->update(['deskripsi' => 'Edit Pengguna']);
        DB::table('izin')->where('kode_izin', 'pengguna.hapus')->update(['deskripsi' => 'Hapus Pengguna']);
        DB::table('izin')->where('kode_izin', 'pengguna.verifikasi')->update(['deskripsi' => 'Verifikasi Kandidat']);

        DB::table('izin')->insertOrIgnore([
            'kode_izin' => 'izin.kelola',
            'deskripsi' => 'Kelola Izin Sistem',
        ]);
    }
};

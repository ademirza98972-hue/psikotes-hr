<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $peta = [
            'dashboard.lihat' => 'Akses Dashboard',
            'data_karyawan.kelola' => 'Kelola Data Karyawan',
            'hasil_tes.lihat' => 'Lihat Hasil Tes',
            'hasil_tes.review' => 'Review Hasil Tes',
            'izin.kelola' => 'Kelola Izin Sistem',
            'kategori_tes.kelola' => 'Kelola Kategori Tes',
            'pengguna.tambah' => 'Tambah Pengguna',
            'pengguna.edit' => 'Edit Pengguna',
            'pengguna.hapus' => 'Hapus Pengguna',
            'pengguna.lihat' => 'Lihat Pengguna',
            'pengguna_admin.kelola' => 'Kelola Admin & Staff',
            'peran.kelola' => 'Kelola Peran',
            'soal.tambah' => 'Tambah Soal',
            'soal.edit' => 'Edit Soal',
            'soal.hapus' => 'Hapus Soal',
            'soal.lihat' => 'Lihat Soal',
        ];

        foreach ($peta as $kode => $deskripsiBaru) {
            DB::table('izin')
                ->where('kode_izin', $kode)
                ->update(['deskripsi' => $deskripsiBaru]);
        }
    }

    public function down(): void
    {
        $peta = [
            'dashboard.lihat' => 'Mengakses dashboard admin',
            'data_karyawan.kelola' => 'Mengelola master data karyawan (tambah, ubah, hapus, tandai terpakai)',
            'hasil_tes.lihat' => 'Melihat hasil tes peserta',
            'hasil_tes.review' => 'Mereview dan memberi catatan hasil tes',
            'izin.kelola' => 'Mengelola daftar izin sistem',
            'kategori_tes.kelola' => 'Mengelola kategori tes (tambah, ubah, hapus)',
            'pengguna.tambah' => 'Menambahkan pengguna baru',
            'pengguna.edit' => 'Mengubah data pengguna',
            'pengguna.hapus' => 'Menghapus pengguna',
            'pengguna.lihat' => 'Melihat daftar dan detail pengguna',
            'pengguna_admin.kelola' => 'Mengelola akun pengguna admin/custom/staf internal',
            'peran.kelola' => 'Mengelola peran (tambah, ubah, hapus, atur izin di dalamnya)',
            'soal.tambah' => 'Menambahkan soal ke bank soal',
            'soal.edit' => 'Mengubah soal di bank soal',
            'soal.hapus' => 'Menghapus soal dari bank soal',
            'soal.lihat' => 'Melihat daftar soal',
        ];

        foreach ($peta as $kode => $deskripsiLama) {
            DB::table('izin')
                ->where('kode_izin', $kode)
                ->update(['deskripsi' => $deskripsiLama]);
        }
    }
};
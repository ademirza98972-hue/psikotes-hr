<?php

namespace Database\Seeders;

use App\Models\Izin;
use Illuminate\Database\Seeder;

class IzinSeeder extends Seeder
{
    public function run(): void
    {
        $daftarIzin = [
            ['kode_izin' => 'pengguna.tambah', 'deskripsi' => 'Menambahkan pengguna baru'],
            ['kode_izin' => 'pengguna.edit', 'deskripsi' => 'Mengubah data pengguna'],
            ['kode_izin' => 'pengguna.hapus', 'deskripsi' => 'Menghapus pengguna'],
            ['kode_izin' => 'pengguna.lihat', 'deskripsi' => 'Melihat daftar dan detail pengguna'],
            ['kode_izin' => 'soal.tambah', 'deskripsi' => 'Menambahkan soal ke bank soal'],
            ['kode_izin' => 'soal.edit', 'deskripsi' => 'Mengubah soal di bank soal'],
            ['kode_izin' => 'soal.hapus', 'deskripsi' => 'Menghapus soal dari bank soal'],
            ['kode_izin' => 'soal.lihat', 'deskripsi' => 'Melihat daftar soal'],
            ['kode_izin' => 'kategori_tes.kelola', 'deskripsi' => 'Mengelola kategori tes (tambah, ubah, hapus)'],
            ['kode_izin' => 'hasil_tes.lihat', 'deskripsi' => 'Melihat hasil tes peserta'],
            ['kode_izin' => 'hasil_tes.review', 'deskripsi' => 'Mereview dan memberi catatan hasil tes'],
            ['kode_izin' => 'dashboard.lihat', 'deskripsi' => 'Mengakses dashboard admin'],
            ['kode_izin' => 'peran.kelola', 'deskripsi' => 'Mengelola peran (tambah, ubah, hapus, atur izin di dalamnya)'],
            ['kode_izin' => 'izin.kelola', 'deskripsi' => 'Mengelola daftar izin sistem'],
        ];

        foreach ($daftarIzin as $izin) {
            Izin::updateOrCreate(
                ['kode_izin' => $izin['kode_izin']],
                ['deskripsi' => $izin['deskripsi']]
            );
        }
    }
}
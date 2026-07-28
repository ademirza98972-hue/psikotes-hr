<?php

namespace Database\Seeders;

use App\Models\Izin;
use Illuminate\Database\Seeder;

class IzinSeeder extends Seeder
{
    public function run(): void
    {
        $daftarIzin = [
            ['kode_izin' => 'pengguna.tambah', 'deskripsi' => 'Tambah Pengguna'],
            ['kode_izin' => 'pengguna.edit', 'deskripsi' => 'Edit Pengguna'],
            ['kode_izin' => 'pengguna.hapus', 'deskripsi' => 'Hapus Pengguna'],
            ['kode_izin' => 'pengguna.lihat', 'deskripsi' => 'Lihat Pengguna'],
            ['kode_izin' => 'pengguna.verifikasi', 'deskripsi' => 'Verifikasi Kandidat'],
            ['kode_izin' => 'soal.tambah', 'deskripsi' => 'Tambah Soal'],
            ['kode_izin' => 'soal.edit', 'deskripsi' => 'Edit Soal'],
            ['kode_izin' => 'soal.hapus', 'deskripsi' => 'Hapus Soal'],
            ['kode_izin' => 'soal.lihat', 'deskripsi' => 'Lihat Soal'],
            ['kode_izin' => 'kategori_tes.kelola', 'deskripsi' => 'Kelola Kategori Tes'],
            ['kode_izin' => 'hasil_tes.lihat', 'deskripsi' => 'Lihat Hasil Tes'],
            ['kode_izin' => 'hasil_tes.review', 'deskripsi' => 'Review Hasil Tes'],
            ['kode_izin' => 'dashboard.lihat', 'deskripsi' => 'Akses Dashboard'],
            ['kode_izin' => 'peran.kelola', 'deskripsi' => 'Kelola Peran'],
            ['kode_izin' => 'izin.kelola', 'deskripsi' => 'Kelola Izin Sistem'],
            ['kode_izin' => 'data_karyawan.kelola', 'deskripsi' => 'Kelola Data Karyawan'],
            ['kode_izin' => 'pengguna_admin.kelola', 'deskripsi' => 'Kelola Admin & Staff'],
            ['kode_izin' => 'master_data.kelola', 'deskripsi' => 'Kelola Master Data Departemen & Posisi'],
            ['kode_izin' => 'data_terhapus.kelola', 'deskripsi' => 'Kelola Data Terhapus'],
            ['kode_izin' => 'hasil_tes.lihat_sensitif', 'deskripsi' => 'Lihat Hasil Tes Sensitif (MMPI-2)'],
        ];

        foreach ($daftarIzin as $izin) {
            Izin::updateOrCreate(
                ['kode_izin' => $izin['kode_izin']],
                ['deskripsi' => $izin['deskripsi']]
            );
        }
    }
}
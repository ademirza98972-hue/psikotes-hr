<?php

namespace Database\Seeders;

use App\Models\Izin;
use App\Models\Peran;
use Illuminate\Database\Seeder;

class PeranSeeder extends Seeder
{
    public function run(): void
    {
        $semuaIzin = Izin::pluck('kode_izin')->all();

        $konfigurasiPeran = [
            [
                'nama_peran' => 'Super Admin',
                'deskripsi' => 'Akses penuh ke seluruh sistem, termasuk kelola peran dan izin.',
                'izin' => $semuaIzin,
            ],
            [
                'nama_peran' => 'Admin HR',
                'deskripsi' => 'Kelola pengguna, soal, kategori tes, review hasil tes, dan master data karyawan.',
                'izin' => array_merge(
                    array_values(array_diff($semuaIzin, ['peran.kelola', 'izin.kelola', 'pengguna_admin.kelola'])),
                    ['hasil_tes.lihat_sensitif']
                ),
            ],
            [
                'nama_peran' => 'HR Viewer',
                'deskripsi' => 'Hanya dapat melihat dashboard dan hasil tes.',
                'izin' => [
                    'dashboard.lihat',
                    'hasil_tes.lihat',
                ],
            ],
            [
                'nama_peran' => 'Kandidat',
                'deskripsi' => 'Akun pelamar yang dapat mengikuti tes psikotes.',
                'izin' => [],
            ],
            [
                'nama_peran' => 'Karyawan',
                'deskripsi' => 'Akun karyawan internal yang dapat mengikuti tes evaluasi.',
                'izin' => [],
            ],
        ];

        foreach ($konfigurasiPeran as $data) {
            $peran = Peran::updateOrCreate(
                ['nama_peran' => $data['nama_peran']],
                ['deskripsi' => $data['deskripsi']]
            );

            $izinIds = Izin::whereIn('kode_izin', $data['izin'])->pluck('id')->all();
            $peran->izin()->sync($izinIds);
        }
    }
}
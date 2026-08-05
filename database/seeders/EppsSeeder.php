<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\DimensiAlatTes;
use Illuminate\Database\Seeder;

class EppsSeeder extends Seeder
{
    public function run(): void
    {
        $alatTes = AlatTes::updateOrCreate(
            ['kode' => 'EPPS'],
            [
                'nama' => 'EPPS (Edwards Personal Preference Schedule)',
                'kategori' => null,
                'deskripsi' => null,
                'format_dasar' => 'Forced Choice',
                'durasi_total_menit' => null,
                'batas_waktu_per_soal_aktif' => false,
                'batas_waktu_per_soal_detik' => null,
                'is_sensitif' => false,
                'jumlah_soal' => 225,
                'is_aktif' => true,
            ]
        );

        $dimensi = [
            ['kode' => 'ach', 'nama' => 'Achievement', 'urutan' => 0],
            ['kode' => 'def', 'nama' => 'Deference', 'urutan' => 1],
            ['kode' => 'ord', 'nama' => 'Order', 'urutan' => 2],
            ['kode' => 'exh', 'nama' => 'Exhibition', 'urutan' => 3],
            ['kode' => 'aut', 'nama' => 'Autonomy', 'urutan' => 4],
            ['kode' => 'aff', 'nama' => 'Affiliation', 'urutan' => 5],
            ['kode' => 'int', 'nama' => 'Introjection', 'urutan' => 6],
            ['kode' => 'suc', 'nama' => 'Success', 'urutan' => 7],
            ['kode' => 'dom', 'nama' => 'Dominance', 'urutan' => 8],
            ['kode' => 'aba', 'nama' => 'Abasement', 'urutan' => 9],
            ['kode' => 'nur', 'nama' => 'Nurture', 'urutan' => 10],
            ['kode' => 'chg', 'nama' => 'Change', 'urutan' => 11],
            ['kode' => 'end', 'nama' => 'Endurance', 'urutan' => 12],
            ['kode' => 'het', 'nama' => 'Heterosexuality', 'urutan' => 13],
            ['kode' => 'agg', 'nama' => 'Aggression', 'urutan' => 14],
            ['kode' => 'con', 'nama' => 'Consistency', 'urutan' => 15],
        ];

        foreach ($dimensi as $d) {
            DimensiAlatTes::updateOrCreate(
                ['alat_tes_id' => $alatTes->id, 'kode_dimensi' => $d['kode']],
                [
                    'nama_dimensi' => $d['nama'],
                    'deskripsi_aspek' => null,
                    'tipe_kategori' => 'psikogram',
                    'arah_skor' => 'tinggi_baik',
                    'urutan' => $d['urutan'],
                ]
            );
        }

        $this->command->info('EPPS alat_tes & dimensi_alat_tes berhasil diseder.');
    }
}

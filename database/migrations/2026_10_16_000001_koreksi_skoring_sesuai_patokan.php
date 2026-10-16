<?php

use Database\Seeders\EppsSoalSeeder;
use Database\Seeders\ISTSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Koreksi data skoring di DB yang sudah berjalan agar sesuai file patokan "4 ALAT TES.zip".
 * Instalasi baru tidak butuh ini (seeder sudah benar) — setiap langkah dilewati bila datanya tidak ada.
 * Setelah migrate, jalankan `php artisan psikotes:hitung-ulang` agar hasil lama ikut terkoreksi.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->epps();
        $this->ist();
        $this->kraepelin();
    }

    private function epps(): void
    {
        $id = DB::table('alat_tes')->where('kode', 'EPPS')->value('id');
        if (! $id) {
            return;
        }

        // Kunci item sesuai EPPS.xls, pasangan konsistensi, opsi ganda soal 7–12 dibersihkan
        (new EppsSoalSeeder)->run();

        $dim = DB::table('dimensi_alat_tes')->where('alat_tes_id', $id)->pluck('id', 'kode_dimensi');

        foreach (['int' => 'Intraception', 'suc' => 'Succorance', 'nur' => 'Nurturance'] as $kode => $nama) {
            DB::table('dimensi_alat_tes')->where('alat_tes_id', $id)->where('kode_dimensi', $kode)->update(['nama_dimensi' => $nama]);
        }

        // Salah ketik di EPPS.xls: het skor 11 General Adult (Men) = 544 → 54
        if (isset($dim['het'])) {
            DB::table('norma_konversi')->where('dimensi_id', $dim['het'])->where('kelompok_segmen', '3')
                ->where('skor_mentah_min', 11)->where('skor_hasil', 544)->update(['skor_hasil' => 54]);
        }

        // Kategori persentil sesuai EPPS.xls
        foreach ([['Sangat Rendah', 0, 3], ['Rendah', 4, 16], ['Sedang', 17, 84], ['Tinggi', 85, 96], ['Sangat Tinggi', 97, 100]] as [$label, $min, $max]) {
            DB::table('level_dimensi')->where('alat_tes_id', $id)->where('label', $label)
                ->update(['skor_min' => $min, 'skor_max' => $max]);
        }
    }

    private function ist(): void
    {
        $id = DB::table('alat_tes')->where('kode', 'IST')->value('id');
        if (! $id) {
            return;
        }
        $dim = DB::table('dimensi_alat_tes')->where('alat_tes_id', $id)->pluck('id', 'kode_dimensi');

        // Kunci yang terbukti salah dari isi soalnya
        foreach ([18 => 'e', 93 => '24', 96 => '3'] as $nomor => $kunci) {
            DB::table('soal')->where('alat_tes_id', $id)->where('nomor', $nomor)->update(['kunci_jawaban' => $kunci]);
        }
        foreach (ISTSeeder::KUNCI_GE as $nomor => $kunci) {
            DB::table('soal')->where('alat_tes_id', $id)->where('nomor', $nomor)->update(['kunci_jawaban' => json_encode($kunci)]);
        }

        // Salah ketik tabel norma (nilai SW harus naik seiring RW)
        foreach ([['AN', 'USIA_21_25', 6, 81, 91], ['GE', 'USIA_21_25', 8, 82, 92], ['GE', 'USIA_51_60', 17, 127, 124]] as [$kode, $seg, $rw, $lama, $baru]) {
            DB::table('norma_konversi')->where('dimensi_id', $dim[$kode] ?? 0)->where('kelompok_segmen', $seg)
                ->where('skor_mentah_min', $rw)->where('skor_hasil', $lama)->update(['skor_hasil' => $baru]);
        }

        $baris = fn (int $dimensiId, string $seg, int $tahap, float $min, float $max, float $hasil) => [
            'alat_tes_id' => $id, 'dimensi_id' => $dimensiId, 'kelompok_segmen' => $seg, 'tahap' => $tahap,
            'skor_mentah_min' => $min, 'skor_mentah_max' => $max, 'skor_hasil' => $hasil,
            'created_at' => now(), 'updated_at' => now(),
        ];

        // WA usia 51–60: RW 19–20 kosong di patokan → nilai tertinggi yang ada (RW 18 = 135)
        if (isset($dim['WA'])) {
            foreach ([19, 20] as $rw) {
                $ada = DB::table('norma_konversi')->where('dimensi_id', $dim['WA'])->where('kelompok_segmen', 'USIA_51_60')
                    ->where('skor_mentah_min', $rw)->exists();
                if (! $ada) {
                    DB::table('norma_konversi')->insert($baris($dim['WA'], 'USIA_51_60', 1, $rw, $rw, 135));
                }
            }
        }

        // Tahap 2: SW total → IQ (sheet "Norma IQ")
        if (isset($dim['IQ'])) {
            DB::table('norma_konversi')->where('dimensi_id', $dim['IQ'])->where('tahap', 2)->delete();
            DB::table('norma_konversi')->insert(collect(ISTSeeder::NORMA_SW_KE_IQ)
                ->map(fn ($iq, $sw) => $baris($dim['IQ'], 'default', 2, $sw, $sw, $iq))->values()->all());
        }
    }

    private function kraepelin(): void
    {
        $id = DB::table('alat_tes')->where('kode', 'KRP')->value('id');
        $kecepatan = DB::table('dimensi_alat_tes')->where('alat_tes_id', $id)->where('kode_dimensi', 'KECEPATAN')->value('id');
        if (! $kecepatan) {
            return;
        }

        // Kecepatan berupa desimal → tutup celah antar pita (mis. 8.5 dulu tidak dapat kategori)
        foreach (['Rendah' => 8.99, 'Kurang' => 13.99, 'Cukup' => 18.99, 'Baik' => 23.99] as $label => $max) {
            DB::table('level_dimensi')->where('dimensi_id', $kecepatan)->where('label', $label)->update(['skor_max' => $max]);
        }
    }

    public function down(): void
    {
        // Koreksi data satu arah; nilai lama memang salah.
    }
};

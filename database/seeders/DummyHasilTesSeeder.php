<?php

namespace Database\Seeders;

use App\Models\AlatTes;
use App\Models\GridInputPeserta;
use App\Models\HasilKolomGrid;
use App\Models\JawabanPeserta;
use App\Models\OpsiJawaban;
use App\Models\PesertaSesiTes;
use App\Models\Soal;
use App\Services\ScoringEngineService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dummy dummy jawaban dan scoring untuk peserta yang sudah terdaftar di peserta_sesi_tes.
 *
 * Alur:
 *  1. Ambil PesertaSesiTes WHERE status_pengerjaan='Selesai'
 *  2. Tiap baris punya 1 alat tes (IST/EPPS/PAP/KRP) via sesi_tes_id → alat_tes
 *  3. Isi jawaban dummy sesuai soal alat tes
 *  4. Panggil ScoringEngineService → hasil_skor_peserta otomatis muncul
 *
 * User=5 (Bagas Pramudya) di-skip karena status='Belum Mengerjakan'.
 */
class DummyHasilTesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('DummyHasilTesSeeder — generate jawaban + scoring untuk peserta selesai ...');

        DB::table('jawaban_peserta')->truncate();
        DB::table('grid_input_peserta')->truncate();
        DB::table('hasil_kolom_grid')->truncate();
        DB::table('hasil_skor_peserta')->truncate();

        $scoring      = app(ScoringEngineService::class);
        $pesertaList  = PesertaSesiTes::with('user', 'sesiTes.alatTes')
            ->where('status_pengerjaan', 'Selesai')
            ->orderBy('id')
            ->get();

        $this->command->info("Peserta selesai: {$pesertaList->count()}");

        $totalJawaban = 0;

        foreach ($pesertaList as $ps) {
            $userId  = $ps->user_id;
            $sesiId  = $ps->sesi_tes_id;  // ← FK ke sesi_tes.id (bukan psesi.id!)
            $user    = $ps->user;
            $alatTes = $ps->sesiTes?->alatTes?->first();

            if (!$alatTes) {
                $this->command->error("  [{$ps->id}] user={$userId} — no alat_tes found");
                continue;
            }

            try {
                [$count, $summary] = $this->generateForPeserta($ps, $alatTes, $user, $scoring, $userId, $sesiId);
                $totalJawaban += $count;
                $this->command->info("  [psesi={$ps->id}] {$user->name} — {$summary}");
            } catch (\Throwable $e) {
                $this->command->error("  [psesi={$ps->id}] {$user->name}: " . $e->getMessage());
            }
        }

        $this->command->info("Selesai. Total jawaban: {$totalJawaban}");
    }

    /**
     * @return array{int, string}
     */
    private function generateForPeserta(
        PesertaSesiTes $ps,
        AlatTes $alatTes,
        $user,
        ScoringEngineService $scoring,
        int $userId,
        int $sesiId
    ): array {
        $alatKode = $alatTes->kode;
        $kelompok = $this->getKelompok($user);
        $segmen   = $this->getSegmen($user);

        match ($alatKode) {
            'IST' => [
                [$count, $summary] = $this->generateIST($userId, $sesiId, $kelompok),
                $scoring->scoreIST($userId, $sesiId, $alatTes->id, $kelompok),
            ],
            'EPPS' => [
                [$count, $summary] = $this->generateEPPS($userId, $sesiId),
                $scoring->scoreForcedChoice($userId, $sesiId, $alatTes->id, $segmen),
                $scoring->scoreConsistency($userId, $sesiId, $alatTes->id, $segmen),
            ],
            'PAP' => [
                [$count, $summary] = $this->generatePAP($userId, $sesiId),
                $scoring->scoreForcedChoice($userId, $sesiId, $alatTes->id, $segmen),
                $scoring->scoreRollup($userId, $sesiId, $alatTes->id),
            ],
            'KRP' => [
                [$count, $summary] = $this->generateKRP($userId, $sesiId),
                $scoring->scoreGrid($userId, $sesiId, $alatTes->id),
            ],
            default => [$count, $summary] = [0, $alatKode],
        };

        return [$count, "{$alatKode}({$count}jawab)"];
    }

    private function getKelompok($u): string
    {
        $pk = $u->profilKandidat ?? $u->profilKaryawan;
        if (!$pk || !$pk->tanggal_lahir) return 'USIA_21_25';
        $umur = now()->diffInYears($pk->tanggal_lahir);
        return match (true) {
            $umur <= 25  => 'USIA_21_25',
            $umur <= 30  => 'USIA_26_30',
            $umur <= 35  => 'USIA_31_35',
            $umur <= 40  => 'USIA_36_40',
            $umur <= 45  => 'USIA_41_45',
            default      => 'USIA_51_60',
        };
    }

    private function getSegmen($u): string
    {
        $gender = $u->jenis_kelamin ?? 'L';
        return $gender === 'L' ? '3' : '4';
    }

    // ---------------------------------------------------------------
    // IST
    // ---------------------------------------------------------------

    /**
     * @return array{int, string}
     */
    private function generateIST(int $userId, int $sesiId, string $kelompok): array
    {
        $soalList = Soal::where('alat_tes_id', AlatTes::where('kode', 'IST')->value('id'))
            ->orderBy('nomor')
            ->get(['id', 'nomor', 'kunci_jawaban', 'tipe_format']);

        $subtesRange = [
            'SE' => [1, 20],
            'WA' => [21, 40],
            'AN' => [41, 60],
            'GE' => [61, 76],
            'RA' => [77, 96],
            'ZR' => [97, 116],
            'FA' => [117, 136],
            'WU' => [137, 156],
            'ME' => [172, 176],
        ];

        $totalBenar = 0;
        $count      = 0;

        foreach ($subtesRange as $subtes => [$min, $max]) {
            $soalSubtes = $soalList->filter(fn ($s) => $s->nomor >= $min && $s->nomor <= $max);
            if ($soalSubtes->isEmpty()) continue;

            $accuracy = 0.30 + mt_rand(0, 55) / 100.0;
            $benarSub = 0;

            foreach ($soalSubtes as $soal) {
                $opsiList = OpsiJawaban::where('soal_id', $soal->id)->orderBy('urutan')->get(['id', 'urutan']);

                $isBenar = mt_rand() / mt_getrandmax() < $accuracy;

                if ($soal->tipe_format === 'isian_teks' || $soal->tipe_format === 'isian_angka') {
                    JawabanPeserta::create([
                        'user_id'      => $userId,
                        'sesi_tes_id'  => $sesiId,
                        'soal_id'      => $soal->id,
                        'jawaban_teks' => $isBenar ? strtolower(trim($soal->kunci_jawaban ?? '')) : 'x',
                        'waktu_jawab'  => now()->subMinutes(mt_rand(1, 180)),
                    ]);
                } else {
                    $kunciHuruf = strtolower(trim($soal->kunci_jawaban ?? ''));
                    $opsiHuruf  = $opsiList->map(fn ($o) => chr(96 + $o->urutan))->values();
                    $opsiIdx    = $isBenar
                        ? $opsiHuruf->search($kunciHuruf)
                        : mt_rand(0, $opsiHuruf->count() - 1);
                    JawabanPeserta::create([
                        'user_id'         => $userId,
                        'sesi_tes_id'     => $sesiId,
                        'soal_id'         => $soal->id,
                        'opsi_dipilih_id' => $opsiList->offsetGet($opsiIdx)?->id,
                        'waktu_jawab'     => now()->subMinutes(mt_rand(1, 180)),
                    ]);
                }
                if ($isBenar) $benarSub++;
                $count++;
            }
            $totalBenar += $benarSub;
        }

        return [$count, "IST raw={$totalBenar}"];
    }

    // ---------------------------------------------------------------
    // EPPS
    // ---------------------------------------------------------------

    /**
     * @return array{int, string}
     */
    private function generateEPPS(int $userId, int $sesiId): array
    {
        $eppsId = AlatTes::where('kode', 'EPPS')->value('id');
        $soalList = Soal::where('alat_tes_id', $eppsId)
            ->orderBy('nomor')
            ->get(['id', 'nomor']);

        $count = 0;
        foreach ($soalList as $soal) {
            $opsiList = OpsiJawaban::where('soal_id', $soal->id)->orderBy('urutan')->get(['id', 'urutan']);
            if ($opsiList->count() < 2) continue;
            JawabanPeserta::create([
                'user_id'         => $userId,
                'sesi_tes_id'     => $sesiId,
                'soal_id'         => $soal->id,
                'opsi_dipilih_id' => $opsiList->random()->id,
                'waktu_jawab'     => now()->subMinutes(mt_rand(1, 30)),
            ]);
            $count++;
        }

        return [$count, "EPPS {$count}jawab"];
    }

    // ---------------------------------------------------------------
    // PAP
    // ---------------------------------------------------------------

    /**
     * @return array{int, string}
     */
    private function generatePAP(int $userId, int $sesiId): array
    {
        $papId = AlatTes::where('kode', 'PAP')->value('id');
        $soalList = Soal::where('alat_tes_id', $papId)
            ->orderBy('nomor')
            ->get(['id', 'nomor']);

        $count = 0;
        foreach ($soalList as $soal) {
            $opsiList = OpsiJawaban::where('soal_id', $soal->id)->orderBy('urutan')->get(['id', 'urutan']);
            if ($opsiList->count() < 2) continue;
            JawabanPeserta::create([
                'user_id'         => $userId,
                'sesi_tes_id'     => $sesiId,
                'soal_id'         => $soal->id,
                'opsi_dipilih_id' => $opsiList->random()->id,
                'waktu_jawab'     => now()->subMinutes(mt_rand(1, 20)),
            ]);
            $count++;
        }

        return [$count, "PAP {$count}jawab"];
    }

    // ---------------------------------------------------------------
    // KRP
    // ---------------------------------------------------------------

    /**
     * @return array{int, string}
     */
    private function generateKRP(int $userId, int $sesiId): array
    {
        $krpId  = AlatTes::where('kode', 'KRP')->value('id');
        $soalList = Soal::where('alat_tes_id', $krpId)
            ->orderBy('nomor')
            ->get(['id', 'nomor', 'teks_soal']);

        $numCols    = 4;
        $rowsPerCol = 13;
        $soalArr    = $soalList->toArray();
        $count      = 0;

        for ($col = 0; $col < $numCols; $col++) {
            $benarCount = 0;
            $salahCount = 0;
            for ($baris = 1; $baris <= $rowsPerCol; $baris++) {
                $soalIdx = $col * $rowsPerCol + ($baris - 1);
                if ($soalIdx >= count($soalArr)) continue;
                $soal = $soalArr[$soalIdx];

                $angka = array_map('intval', explode(',', trim($soal['teks_soal'])));
                if (count($angka) < 2) continue;

                $idx = ($baris - 1) * 2;
                if ($idx + 1 >= count($angka)) break;

                $jawabBenar = ($angka[$idx] + $angka[$idx + 1]) % 10;
                $isBenar    = mt_rand(1, 100) <= (70 + mt_rand(0, 20));
                $jawabPeserta = $isBenar ? $jawabBenar : mt_rand(0, 9);

                GridInputPeserta::updateOrCreate(
                    ['user_id' => $userId, 'sesi_tes_id' => $sesiId, 'alat_tes_id' => $krpId, 'kolom_ke' => $col + 1, 'baris_ke' => $baris],
                    [
                        'jawaban_peserta' => $jawabPeserta,
                        'jawaban_benar'   => $jawabBenar,
                        'is_benar'        => $isBenar,
                        'waktu_input'     => now()->subMinutes(mt_rand(1, 30) * $col + 1),
                    ]
                );
                $count++;

                if ($isBenar) $benarCount++; else $salahCount++;
            }

            HasilKolomGrid::updateOrCreate(
                ['user_id' => $userId, 'sesi_tes_id' => $sesiId, 'alat_tes_id' => $krpId, 'kolom_ke' => $col + 1],
                [
                    'jumlah_benar'      => $benarCount,
                    'jumlah_salah'      => $salahCount,
                    'jumlah_kelewat'    => 0,
                    'waktu_pakai_detik' => mt_rand(60, 180) * $col + 1,
                ]
            );
        }

        return [$count, "KRP {$count}jawab"];
    }
}

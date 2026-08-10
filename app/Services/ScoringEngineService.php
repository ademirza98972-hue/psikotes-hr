<?php

namespace App\Services;

use App\Models\BobotOpsiDimensi;
use App\Models\DimensiAlatTes;
use App\Models\DimensiTurunanKomponen;
use App\Models\HasilKolomGrid;
use App\Models\HasilSkorPeserta;
use App\Models\JawabanPeserta;
use App\Models\NormaKonversi;
use App\Models\Soal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScoringEngineService
{
    /**
     * Hitung skor kognitif untuk user pada sesi tes tertentu.
     */
    public function scoreKognitif(int $userId, int $sesiTesId, int $alatTesId): HasilSkorPeserta
    {
        $dimensiIQ = DB::table('dimensi_alat_tes')
            ->where('alat_tes_id', $alatTesId)
            ->where('kode_dimensi', 'IQ')
            ->value('id');

        $jawaban = JawabanPeserta::where('user_id', $userId)
            ->where('sesi_tes_id', $sesiTesId)
            ->whereHas('soal', fn ($q) => $q->where('alat_tes_id', $alatTesId))
            ->with('opsiDipilih.bobotOpsiDimensi')
            ->get();

        $skorMentah = $jawaban->filter(function (JawabanPeserta $j) {
            if (! $j->opsiDipilih) {
                return false;
            }

            return $j->opsiDipilih->bobotOpsiDimensi->contains(fn ($b) => $b->bobot > 0);
        })->count();

        $skorAkhir = NormaKonversi::where('alat_tes_id', $alatTesId)
            ->where('tahap', 1)
            ->where('kelompok_segmen', 'default')
            ->where('skor_mentah_min', '<=', $skorMentah)
            ->where('skor_mentah_max', '>=', $skorMentah)
            ->value('skor_hasil');

        $skorAkhir ??= 0.00;

        return HasilSkorPeserta::updateOrCreate(
            [
                'user_id'     => $userId,
                'sesi_tes_id' => $sesiTesId,
                'alat_tes_id' => $alatTesId,
                'dimensi_id'  => $dimensiIQ,
            ],
            [
                'skor_mentah' => $skorMentah,
                'skor_akhir'  => $skorAkhir,
                'level_id'    => $this->cariLevelId($dimensiIQ, (float) $skorAkhir),
            ]
        );
    }

    public function scoreForcedChoice(int $userId, int $sesiTesId, int $alatTesId, string $kelompokSegmen, bool $pakaiNorma = true): Collection
    {
        $jawaban = JawabanPeserta::where('user_id', $userId)
            ->where('sesi_tes_id', $sesiTesId)
            ->whereHas('soal', fn ($q) => $q->where('alat_tes_id', $alatTesId))
            ->whereNotNull('opsi_dipilih_id')
            ->with('opsiDipilih.bobotOpsiDimensi')
            ->get();

        $tally = [];
        foreach ($jawaban as $j) {
            if (!$j->opsiDipilih || $j->opsiDipilih->bobotOpsiDimensi->isEmpty()) {
                continue;
            }
            foreach ($j->opsiDipilih->bobotOpsiDimensi as $bobot) {
                if ($bobot->bobot > 0) {
                    $dimensiId = $bobot->dimensi_id;
                    if (!isset($tally[$dimensiId])) {
                        $tally[$dimensiId] = 0;
                    }
                    $tally[$dimensiId] += $bobot->bobot;
                }
            }
        }

        $results = collect();
        foreach ($tally as $dimensiId => $skorMentah) {
            $skorAkhir = $pakaiNorma
                ? NormaKonversi::where('alat_tes_id', $alatTesId)
                    ->where('dimensi_id', $dimensiId)
                    ->where('tahap', 1)
                    ->where('kelompok_segmen', $kelompokSegmen)
                    ->where('skor_mentah_min', '<=', $skorMentah)
                    ->where('skor_mentah_max', '>=', $skorMentah)
                    ->value('skor_hasil')
                : $skorMentah;

            $skorAkhir = $skorAkhir ?? 0.00;

            $results->push(
                HasilSkorPeserta::updateOrCreate(
                    [
                        'user_id'     => $userId,
                        'sesi_tes_id' => $sesiTesId,
                        'alat_tes_id' => $alatTesId,
                        'dimensi_id'  => $dimensiId,
                    ],
                    [
                        'skor_mentah' => $skorMentah,
                        'skor_akhir'  => $skorAkhir,
                        'level_id'    => $this->cariLevelId($dimensiId, (float) $skorAkhir),
                    ]
                )
            );
        }
        return $results;
    }

    public function scoreConsistency(int $userId, int $sesiTesId, int $alatTesId, string $kelompokSegmen): HasilSkorPeserta
    {
        $dimensiCon = DimensiAlatTes::where('alat_tes_id', $alatTesId)
            ->where('kode_dimensi', 'con')
            ->first();

        if (!$dimensiCon) {
            throw new \InvalidArgumentException("Dimensi 'con' tidak ditemukan untuk alat tes {$alatTesId}.");
        }

        $jawaban = JawabanPeserta::where('user_id', $userId)
            ->where('sesi_tes_id', $sesiTesId)
            ->whereHas('soal', function (\Illuminate\Database\Eloquent\Builder $q) use ($alatTesId) {
                $q->where('alat_tes_id', $alatTesId)
                  ->whereNotNull('duplikat_dari_soal_id');
            })
            ->with('soal', 'opsiDipilih')
            ->get();

        $skorMentah = 0;

        foreach ($jawaban as $j) {
            $soalAsli = Soal::where('id', $j->soal->duplikat_dari_soal_id)->first();
            if (!$soalAsli) {
                continue;
            }

            $jawabanAsli = JawabanPeserta::where('user_id', $userId)
                ->where('sesi_tes_id', $sesiTesId)
                ->where('soal_id', $soalAsli->id)
                ->with('opsiDipilih')
                ->first();

            if (!$jawabanAsli) {
                continue;
            }

            if ($jawabanAsli->opsiDipilih->urutan === $j->opsiDipilih->urutan) {
                $skorMentah++;
            }
        }

        $skorAkhir = NormaKonversi::where('alat_tes_id', $alatTesId)
            ->where('dimensi_id', $dimensiCon->id)
            ->where('tahap', 1)
            ->where('kelompok_segmen', $kelompokSegmen)
            ->where('skor_mentah_min', '<=', $skorMentah)
            ->where('skor_mentah_max', '>=', $skorMentah)
            ->value('skor_hasil');

        $skorAkhir = $skorAkhir ?? 0.00;

        return HasilSkorPeserta::updateOrCreate(
            [
                'user_id'     => $userId,
                'sesi_tes_id' => $sesiTesId,
                'alat_tes_id' => $alatTesId,
                'dimensi_id'  => $dimensiCon->id,
            ],
            [
                'skor_mentah' => $skorMentah,
                'skor_akhir'  => $skorAkhir,
                'level_id'    => $this->cariLevelId($dimensiCon->id, (float) $skorAkhir),
            ]
        );
    }

    /**
     * Hitung skor dimensi turunan (rollup) dari rata-rata berbobot skor mentah dimensi komponennya.
     */
    public function scoreRollup(int $userId, int $sesiTesId, int $alatTesId): Collection
    {
        $turunanDimensi = DimensiAlatTes::where('alat_tes_id', $alatTesId)
            ->whereHas('komponenPenyusun')
            ->get();

        $results = collect();

        foreach ($turunanDimensi as $turunan) {
            $komponen = DimensiTurunanKomponen::with('dimensiKomponen')
                ->where('dimensi_turunan_id', $turunan->id)
                ->get();

            if ($komponen->isEmpty()) {
                continue;
            }

            $totalBobot = $komponen->sum('bobot');
            $skorMentah = 0.00;

            foreach ($komponen as $item) {
                $skor = HasilSkorPeserta::where('user_id', $userId)
                    ->where('sesi_tes_id', $sesiTesId)
                    ->where('alat_tes_id', $alatTesId)
                    ->where('dimensi_id', $item->dimensi_komponen_id)
                    ->value('skor_mentah');

                $skor = $skor ?? 0.00;
                $skorMentah += (float) $skor * (float) $item->bobot;
            }

            $skorMentah = $totalBobot > 0 ? round($skorMentah / $totalBobot, 2) : 0.00;

            $results->push(
                HasilSkorPeserta::updateOrCreate(
                    [
                        'user_id'     => $userId,
                        'sesi_tes_id' => $sesiTesId,
                        'alat_tes_id' => $alatTesId,
                        'dimensi_id'  => $turunan->id,
                    ],
                    [
                        'skor_mentah' => $skorMentah,
                        'skor_akhir'  => $skorMentah,
                        'level_id'    => $this->cariLevelId($turunan->id, (float) $skorMentah),
                    ]
                )
            );
        }

        return $results;
    }

    public function scoreGrid(int $userId, int $sesiTesId, int $alatTesId): Collection
    {
        $kodes = ['KECEPATAN', 'KETELITIAN', 'KETAHANAN', 'KEAJEGAN'];
        $dimensiByKode = DimensiAlatTes::where('alat_tes_id', $alatTesId)
            ->whereIn('kode_dimensi', $kodes)
            ->pluck('id', 'kode_dimensi');

        if ($dimensiByKode->count() !== 4) {
            throw new \InvalidArgumentException(
                "Alat tes {$alatTesId} belum memiliki 4 dimensi (KECEPATAN/KETELITIAN/KETAHANAN/KEAJEGAN). "
                . "Dimensi yang ditemukan: " . $dimensiByKode->keys()->join(', ')
            );
        }

        $rows = HasilKolomGrid::where('user_id', $userId)
            ->where('sesi_tes_id', $sesiTesId)
            ->where('alat_tes_id', $alatTesId)
            ->orderBy('kolom_ke')
            ->get();

        $n = $rows->count();
        if ($n === 0) {
            return collect();
        }

        $sumBenar = 0;
        $sumSalah = 0;
        $sumKelewat = 0;
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        foreach ($rows as $row) {
            $x = (float) $row->kolom_ke;
            $y = (float) $row->jumlah_benar;
            $sumBenar += $row->jumlah_benar;
            $sumSalah += $row->jumlah_salah;
            $sumKelewat += $row->jumlah_kelewat;
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        $kecepatan = $n > 0 ? round($sumBenar / $n, 2) : 0.00;
        $ketelitian = (int) ($sumSalah + $sumKelewat);

        $denominator = $n * $sumX2 - $sumX * $sumX;
        if ($denominator == 0) {
            $b = 0.00;
        } else {
            $b = ($n * $sumXY - $sumX * $sumY) / $denominator;
        }
        $ketaahanan = round($b * $n, 2);

        $keajegan = 0.00;
        if ($n > 0) {
            $totalDeviasi = 0.00;
            foreach ($rows as $row) {
                $totalDeviasi += abs((float) $row->jumlah_benar - $kecepatan);
            }
            $keajegan = round($totalDeviasi / $n, 2);
        }

        $results = collect([
            [
                'kode_dimensi' => 'KECEPATAN',
                'skor_mentah'  => $kecepatan,
                'skor_akhir'   => $kecepatan,
            ],
            [
                'kode_dimensi' => 'KETELITIAN',
                'skor_mentah'  => $ketelitian,
                'skor_akhir'   => $ketelitian,
            ],
            [
                'kode_dimensi' => 'KETAHANAN',
                'skor_mentah'  => $ketaahanan,
                'skor_akhir'   => $ketaahanan,
            ],
            [
                'kode_dimensi' => 'KEAJEGAN',
                'skor_mentah'  => $keajegan,
                'skor_akhir'   => $keajegan,
            ],
        ]);

        return $results->map(function ($item) use ($userId, $sesiTesId, $alatTesId, $dimensiByKode) {
            $dimensiId = $dimensiByKode[$item['kode_dimensi']];
            return HasilSkorPeserta::updateOrCreate(
                [
                    'user_id'     => $userId,
                    'sesi_tes_id' => $sesiTesId,
                    'alat_tes_id' => $alatTesId,
                    'dimensi_id'  => $dimensiId,
                ],
                [
                    'skor_mentah' => $item['skor_mentah'],
                    'skor_akhir'  => $item['skor_akhir'],
                    'level_id'    => $this->cariLevelId($dimensiId, (float) $item['skor_akhir']),
                ]
            );
        });
    }

    private function cariLevelId(int $dimensiId, float $skorAkhir): ?int
    {
        return \App\Models\LevelDimensi::where('dimensi_id', $dimensiId)
            ->where('skor_min', '<=', $skorAkhir)
            ->where('skor_max', '>=', $skorAkhir)
            ->orderBy('urutan')
            ->value('id');
    }

    public function dispatch(
        int $userId,
        int $sesiTesId,
        int $alatTesId,
        string $kelompokSegmen = 'default'
    ): mixed {
        $alatTes = \App\Models\AlatTes::findOrFail($alatTesId);

        return match($alatTes->pola_skoring) {
            'kognitif'             => $this->scoreKognitif($userId, $sesiTesId, $alatTesId),
            'forced_choice'        => $this->scoreForcedChoice($userId, $sesiTesId, $alatTesId, $kelompokSegmen),
            'forced_choice_rollup' => $this->scoreForcedChoiceRollup($userId, $sesiTesId, $alatTesId, $kelompokSegmen),
            'grid'                 => $this->scoreGrid($userId, $sesiTesId, $alatTesId),
            default                => throw new \InvalidArgumentException(
                "pola_skoring '{$alatTes->pola_skoring}' tidak dikenal untuk alat tes '{$alatTes->kode}'."
            ),
        };
    }

    private function scoreForcedChoiceRollup(
        int $userId,
        int $sesiTesId,
        int $alatTesId,
        string $kelompokSegmen
    ): mixed {
        $this->scoreForcedChoice($userId, $sesiTesId, $alatTesId, $kelompokSegmen);
        return $this->scoreRollup($userId, $sesiTesId, $alatTesId);
    }
}

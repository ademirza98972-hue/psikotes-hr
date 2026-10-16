<?php

namespace App\Services;

use App\Models\BobotOpsiDimensi;
use App\Models\DimensiAlatTes;
use App\Models\DimensiTurunanKomponen;
use App\Models\HasilKolomGrid;
use App\Models\HasilSkorPeserta;
use App\Models\JawabanPeserta;
use App\Models\NormaKonversi;
use App\Models\OpsiJawaban;
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

    /**
     * Hitung skor IST per subtes (SE, WA, AN, GE, RA, ZR, FA, WU, ME) dan IQ Total.
     */
    public function scoreIST(int $userId, int $sesiTesId, int $alatTesId, string $kelompokSegmen = 'USIA_21_25'): void
    {
        $dimensiMap = DB::table('dimensi_alat_tes')
            ->where('alat_tes_id', $alatTesId)
            ->pluck('id', 'kode_dimensi');

        $subtesRange = [
            'SE' => [1, 20],
            'WA' => [21, 40],
            'AN' => [41, 60],
            'GE' => [61, 76],
            'RA' => [77, 96],
            'ZR' => [97, 116],
            'FA' => [117, 136],
            'WU' => [137, 156],
            'ME' => [157, 176],
        ];

        $jawaban = JawabanPeserta::where('user_id', $userId)
            ->where('sesi_tes_id', $sesiTesId)
            ->whereHas('soal', fn($q) => $q->where('alat_tes_id', $alatTesId))
            ->with('soal')
            ->get()
            ->keyBy('soal_id');

        $soalList = Soal::where('alat_tes_id', $alatTesId)
            ->get()
            ->keyBy('id');

        $skorMentahTotal = 0;
        foreach ($subtesRange as $kode => [$nomorMin, $nomorMax]) {
            if (!isset($dimensiMap[$kode])) {
                continue;
            }

            $soalSubtes = $soalList->filter(fn($s) => $s->nomor >= $nomorMin && $s->nomor <= $nomorMax);
            $benar = 0;

            if ($kode === 'GE') {
                // GE: kunci_jawaban is JSON {"2":["word"],"1":["word"]}, score 0/1/2 per item
                $rawGE = 0;
                foreach ($soalSubtes as $soal) {
                    $jaw = $jawaban->get($soal->id);
                    if (!$jaw) continue;
                    $kunciRaw = $soal->kunci_jawaban ?? '';
                    if (!$kunciRaw) continue;
                    $kunci = json_decode($kunciRaw, true);
                    if (!is_array($kunci)) continue;
                    $jawPeserta = strtolower(trim($jaw->jawaban_teks ?? ''));
                    $score2 = array_map('strtolower', $kunci['2'] ?? []);
                    $score1 = array_map('strtolower', $kunci['1'] ?? []);
                    if (in_array($jawPeserta, $score2)) {
                        $rawGE += 2;
                    } elseif (in_array($jawPeserta, $score1)) {
                        $rawGE += 1;
                    }
                }
                $benar = $this->convertGeRawToEquivalent($rawGE);
            } else {
                foreach ($soalSubtes as $soal) {
                    $jaw = $jawaban->get($soal->id);
                    if (!$jaw) continue;
                    $kunciJawaban = strtolower(trim($soal->kunci_jawaban ?? ''));
                    if ($kunciJawaban === '') continue;

                    if ($soal->tipe_format === 'pilihan_ganda' || $soal->tipe_format === 'pilihan_gambar') {
                        if ($jaw->opsi_dipilih_id) {
                            $opsiDipilih = OpsiJawaban::find($jaw->opsi_dipilih_id);
                            $hurufDipilih = chr(96 + ($opsiDipilih?->urutan ?? 0));
                            if ($hurufDipilih === $kunciJawaban) {
                                $benar++;
                            }
                        }
                    } elseif (in_array($soal->tipe_format, ['isian_teks', 'isian_angka'])) {
                        $jawabanPeserta = strtolower(trim($jaw->jawaban_teks ?? ''));
                        $kunciArr = json_decode($soal->kunci_jawaban ?? '', true);
                        if (is_array($kunciArr)) {
                            $semua = array_map('strtolower', array_merge($kunciArr['2'] ?? [], $kunciArr['1'] ?? []));
                            if (in_array($jawabanPeserta, $semua)) {
                                $benar++;
                            }
                        } elseif ($jawabanPeserta === $kunciJawaban) {
                            $benar++;
                        }
                    }
                }
            }

            $skorAkhirSubtes = NormaKonversi::where('alat_tes_id', $alatTesId)
                ->where('dimensi_id', $dimensiMap[$kode])
                ->where('kelompok_segmen', $kelompokSegmen)
                ->where('skor_mentah_min', '<=', $benar)
                ->where('skor_mentah_max', '>=', $benar)
                ->value('skor_hasil') ?? 0;

            HasilSkorPeserta::updateOrCreate(
                [
                    'user_id'     => $userId,
                    'sesi_tes_id' => $sesiTesId,
                    'alat_tes_id' => $alatTesId,
                    'dimensi_id'  => $dimensiMap[$kode],
                ],
                [
                    'skor_mentah' => $benar,
                    'skor_akhir'  => $skorAkhirSubtes,
                    'level_id'    => $this->cariLevelId($dimensiMap[$kode], (float) $skorAkhirSubtes),
                ]
            );

            $skorMentahTotal += $benar;
        }

        // IQ Total (patokan Sofware IST.xlsx): jumlah RW 9 subtes (GE pakai nilai konversi)
        // → tahap 1: tabel JUMLAH per usia (RW → SW) → tahap 2: sheet "Norma IQ" (SW → IQ).
        if (isset($dimensiMap['IQ'])) {
            $swTotal = $this->normaTerdekat($alatTesId, $dimensiMap['IQ'], $kelompokSegmen, 1, $skorMentahTotal);
            $iqTotal = $swTotal === null ? 0
                : ($this->normaTerdekat($alatTesId, $dimensiMap['IQ'], 'default', 2, $swTotal) ?? $swTotal);

            HasilSkorPeserta::updateOrCreate(
                [
                    'user_id'     => $userId,
                    'sesi_tes_id' => $sesiTesId,
                    'alat_tes_id' => $alatTesId,
                    'dimensi_id'  => $dimensiMap['IQ'],
                ],
                [
                    'skor_mentah' => $skorMentahTotal,
                    'skor_akhir'  => $iqTotal,
                    'level_id'    => null,
                ]
            );
        }
    }

    /**
     * Lookup norma; skor di luar rentang tabel memakai baris terdekat (batas bawah/atas tabel),
     * bukan 0. Contoh: SW total 67 tidak ada di "Norma IQ" (73–140) → pakai IQ untuk SW 73.
     */
    private function normaTerdekat(int $alatTesId, int $dimensiId, string $segmen, int $tahap, float $skor): ?float
    {
        $q = fn () => NormaKonversi::where('alat_tes_id', $alatTesId)
            ->where('dimensi_id', $dimensiId)
            ->where('kelompok_segmen', $segmen)
            ->where('tahap', $tahap);

        $hasil = $q()->where('skor_mentah_min', '<=', $skor)->where('skor_mentah_max', '>=', $skor)->value('skor_hasil')
            ?? match (true) {
                $skor < (float) $q()->min('skor_mentah_min') => $q()->orderBy('skor_mentah_min')->value('skor_hasil'),
                $skor > (float) $q()->max('skor_mentah_max') => $q()->orderByDesc('skor_mentah_max')->value('skor_hasil'),
                default => null,
            };

        return $hasil === null ? null : (float) $hasil;
    }

    private function convertGeRawToEquivalent(int $rawGE): int
    {
        return match(true) {
            $rawGE >= 31 => 20,
            $rawGE === 30 => 19,
            $rawGE === 29 => 18,
            $rawGE === 28 => 17,
            $rawGE === 27 => 16,
            $rawGE >= 25  => 15,
            $rawGE >= 23  => 14,
            $rawGE >= 21  => 13,
            $rawGE >= 19  => 12,
            $rawGE >= 17  => 11,
            $rawGE >= 15  => 10,
            $rawGE >= 13  => 9,
            $rawGE >= 11  => 8,
            $rawGE >= 9   => 7,
            $rawGE >= 7   => 6,
            $rawGE >= 5   => 5,
            $rawGE === 4  => 4,
            $rawGE === 3  => 3,
            $rawGE === 2  => 2,
            default       => 1,
        };
    }

    public function scoreForcedChoice(int $userId, int $sesiTesId, int $alatTesId, string $kelompokSegmen, bool $pakaiNorma = true): Collection
    {
        $jawaban = JawabanPeserta::where('user_id', $userId)
            ->where('sesi_tes_id', $sesiTesId)
            ->whereHas('soal', fn ($q) => $q->where('alat_tes_id', $alatTesId))
            ->whereNotNull('opsi_dipilih_id')
            ->with('opsiDipilih.bobotOpsiDimensi')
            ->get();

        // Semua dimensi yang punya bobot di alat tes ini mulai dari 0, supaya
        // dimensi yang tidak pernah dipilih tetap tersimpan (skor 0) — bukan hilang.
        $tally = DB::table('bobot_opsi_dimensi as b')
            ->join('opsi_jawaban as o', 'o.id', '=', 'b.opsi_jawaban_id')
            ->join('soal as s', 's.id', '=', 'o.soal_id')
            ->where('s.alat_tes_id', $alatTesId)
            ->whereNull('s.deleted_at')
            ->where('b.bobot', '>', 0)
            ->distinct()
            ->pluck('b.dimensi_id')
            ->mapWithKeys(fn ($id) => [$id => 0])
            ->all();

        foreach ($jawaban as $j) {
            if (!$j->opsiDipilih || $j->opsiDipilih->bobotOpsiDimensi->isEmpty()) {
                continue;
            }
            foreach ($j->opsiDipilih->bobotOpsiDimensi as $bobot) {
                if ($bobot->bobot > 0) {
                    $dimensiId = $bobot->dimensi_id;
                    $tally[$dimensiId] = ($tally[$dimensiId] ?? 0) + $bobot->bobot;
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
            $soalAsli = Soal::find($j->soal->duplikat_dari_soal_id);
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

            if ($jawabanAsli->opsiDipilih && $j->opsiDipilih
                && $jawabanAsli->opsiDipilih->urutan === $j->opsiDipilih->urutan) {
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
     * 
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

        if ($rows->isEmpty()) {
            return collect();
        }

        // Patokan Kraepelin.xls: x = 1..N kolom, y = jumlah benar per kolom; kolom yang tidak
        // sempat dikerjakan (tidak ada barisnya) bernilai y = 0, pembagi tetap N (=50).
        $n = max(Soal::where('alat_tes_id', $alatTesId)->count(), (int) $rows->max('kolom_ke'));
        $yPerKolom = $rows->pluck('jumlah_benar', 'kolom_ke');

        $sumSalah = $rows->sum('jumlah_salah');
        $sumKelewat = $rows->sum('jumlah_kelewat');
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        for ($x = 1; $x <= $n; $x++) {
            $y = (float) ($yPerKolom[$x] ?? 0);
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        // Panker (kecepatan) = Σy / N
        $panker = $sumY / $n;
        $kecepatan = round($panker, 2);
        // Tianker (ketelitian) = jumlah salah + jumlah terlampaui
        $ketelitian = (int) ($sumSalah + $sumKelewat);

        $denominator = $n * $sumX2 - $sumX * $sumX;
        if ($denominator == 0) {
            $b = 0.00;
        } else {
            $b = ($n * $sumXY - $sumX * $sumY) / $denominator;
        }
        // Hanker (ketahanan) = y(50) − y(0) = 50·b dari regresi linier y = a + bx
        $ketaahanan = round($b * 50, 2);

        // Janker (keajegan) = Σ|y − Panker| / N
        $totalDeviasi = 0.00;
        for ($x = 1; $x <= $n; $x++) {
            $totalDeviasi += abs((float) ($yPerKolom[$x] ?? 0) - $panker);
        }
        $keajegan = round($totalDeviasi / $n, 2);

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

    /**
     * Kelompok norma peserta untuk suatu alat tes.
     * Forced choice (EPPS): 3 = General Adult (Men), 4 = General Adult (Women).
     * IST: kelompok usia sheet "USIA xx - yy" (Sofware IST.xlsx); usia dihitung pada tanggal tes.
     */
    public function kelompokSegmen(\App\Models\User $user, \App\Models\AlatTes $alatTes, ?\DateTimeInterface $tanggalTes = null): string
    {
        if (strtolower(str_replace(' ', '_', $alatTes->format_dasar ?? '')) === 'forced_choice') {
            $jenisKelamin = $user->dataKaryawan?->jenis_kelamin ?? $user->profilKandidat?->jenis_kelamin;

            return $jenisKelamin === 'P' ? '4' : '3';
        }

        if ($alatTes->kode === 'IST') {
            $tanggalLahir = $user->dataKaryawan?->tanggal_lahir ?? $user->profilKandidat?->tanggal_lahir;
            $usia = $tanggalLahir
                ? (int) \Carbon\Carbon::parse($tanggalLahir)->diffInYears($tanggalTes ?? now())
                : 25;

            // ponytail: patokan tidak punya norma <21 dan 46–50 → pakai kelompok terdekat yang tersedia
            return match (true) {
                $usia <= 25 => 'USIA_21_25',
                $usia <= 30 => 'USIA_26_30',
                $usia <= 35 => 'USIA_31_35',
                $usia <= 40 => 'USIA_36_40',
                $usia <= 45 => 'USIA_41_45',
                default     => 'USIA_51_60',
            };
        }

        return 'default';
    }

    /**
     * Rekap satu kolom Kraepelin dari input per baris. Jawaban "0" sah (mis. 4+6 → 0);
     * terlampaui = baris kosong yang dilompati sebelum baris terakhir yang diisi.
     */
    public function rekapKolomGrid(int $userId, int $sesiTesId, int $alatTesId, int $kolomKe): void
    {
        $stat = \App\Models\GridInputPeserta::where('user_id', $userId)
            ->where('sesi_tes_id', $sesiTesId)
            ->where('alat_tes_id', $alatTesId)
            ->where('kolom_ke', $kolomKe)
            ->selectRaw('
                SUM(is_benar = 1) as jumlah_benar,
                SUM(is_benar = 0) as jumlah_salah,
                MAX(baris_ke) - COUNT(*) as jumlah_kelewat,
                MIN(waktu_input) as waktu_min
            ')->first();

        $kolom = HasilKolomGrid::firstOrNew([
            'user_id'     => $userId,
            'sesi_tes_id' => $sesiTesId,
            'alat_tes_id' => $alatTesId,
            'kolom_ke'    => $kolomKe,
        ]);
        $kolom->fill([
            'jumlah_benar'   => (int) ($stat->jumlah_benar ?? 0),
            'jumlah_salah'   => (int) ($stat->jumlah_salah ?? 0),
            'jumlah_kelewat' => (int) ($stat->jumlah_kelewat ?? 0),
        ]);
        if (! $kolom->exists) {
            $kolom->waktu_pakai_detik = (int) max(0, now()->diffInSeconds($stat->waktu_min ?? now()));
        }
        $kolom->save();
    }

    public function dispatch(
        int $userId,
        int $sesiTesId,
        int $alatTesId,
        string $kelompokSegmen = 'default'
    ): mixed {
        $alatTes = \App\Models\AlatTes::findOrFail($alatTesId);

        return match($alatTes->pola_skoring) {
            'kognitif'             => $alatTes->kode === 'IST'
                ? $this->scoreIST($userId, $sesiTesId, $alatTesId, $kelompokSegmen)
                : $this->scoreKognitif($userId, $sesiTesId, $alatTesId),
            'ist'                  => $this->scoreIST($userId, $sesiTesId, $alatTesId, $kelompokSegmen),
            // EPPS: skor konsistensi (con) dihitung dari pasangan soal ulangan (duplikat_dari_soal_id)
            'forced_choice'        => tap(
                $this->scoreForcedChoice($userId, $sesiTesId, $alatTesId, $kelompokSegmen),
                fn () => DimensiAlatTes::where('alat_tes_id', $alatTesId)->where('kode_dimensi', 'con')->exists()
                    && $this->scoreConsistency($userId, $sesiTesId, $alatTesId, $kelompokSegmen)
            ),
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
        $this->scoreForcedChoice($userId, $sesiTesId, $alatTesId, $kelompokSegmen, pakaiNorma: false);
        return $this->scoreRollup($userId, $sesiTesId, $alatTesId);
    }
}

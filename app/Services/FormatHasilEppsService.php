<?php

namespace App\Services;

use App\Models\AlatTes;
use App\Models\HasilSkorPeserta;
use App\Models\LevelDimensi;
use Illuminate\Support\Collection;

class FormatHasilEppsService
{
    /**
     * Format hasil skor EPPS untuk tampilan.
     *
     * Mengembalikan array/collection dengan 16 baris (15 kebutuhan + con):
     * [
     *   ['dimensi' => 'Achievement', 'skor_mentah' => 5, 'skor_persentil' => 72.5, 'kategori' => 'Tinggi'],
     *   ...
     * ]
     *
     * Mengembalikan Collection kosong bila EPPS tidak ditemukan atau belum ada skor.
     */
    public function formatHasilEppsUntukTampilan(int $userId, int $sesiTesId): Collection
    {
        $alatTesEpps = AlatTes::where('kode', 'EPPS')->first();

        if (! $alatTesEpps) {
            return collect();
        }

        $rows = HasilSkorPeserta::query()
            ->selectRaw('
                hasil_skor_peserta.dimensi_id AS dimensi_id,
                hasil_skor_peserta.skor_mentah AS skor_mentah,
                hasil_skor_peserta.skor_akhir AS skor_akhir,
                dimensi_alat_tes.kode_dimensi AS kode_dimensi,
                dimensi_alat_tes.nama_dimensi AS nama_dimensi
            ')
            ->join('dimensi_alat_tes', 'dimensi_alat_tes.id', '=', 'hasil_skor_peserta.dimensi_id')
            ->where('hasil_skor_peserta.user_id', $userId)
            ->where('hasil_skor_peserta.sesi_tes_id', $sesiTesId)
            ->where('hasil_skor_peserta.alat_tes_id', $alatTesEpps->id)
            ->orderBy('dimensi_alat_tes.urutan')
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $dimensiIds = $rows->pluck('dimensi_id')->unique();

        $levelByDimensi = LevelDimensi::query()
            ->where('alat_tes_id', $alatTesEpps->id)
            ->whereIn('dimensi_id', $dimensiIds)
            ->get()
            ->groupBy('dimensi_id');

        return $rows->map(function ($skor) use ($levelByDimensi) {
            $kategori = $this->cariKategoriLevel(
                $levelByDimensi->get($skor->dimensi_id, collect()),
                (float) $skor->skor_akhir
            );

            return [
                'dimensi'        => $skor->kode_dimensi . ' - ' . $skor->nama_dimensi,
                'skor_mentah'    => $skor->skor_mentah,
                'skor_persentil' => $skor->skor_akhir,
                'kategori'       => $kategori,
            ];
        });
    }

    /**
     * Cari label level_dimensi yang rentang [skor_min, skor_max] memuat $skorPersentil.
     * Mengembalikan null bila tidak ada level yang cocok.
     */
    private function cariKategoriLevel(Collection $levels, float $skorPersentil): ?string
    {
        foreach ($levels as $level) {
            if ((float) $level->skor_min <= $skorPersentil && (float) $level->skor_max >= $skorPersentil) {
                return $level->label;
            }
        }

        return null;
    }
}

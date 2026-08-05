<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BankSoalController extends Controller
{
    /**
     * Daftar Alat Tes — disalin dari AlatTesController agar
     * dua halaman tidak bergantung satu sama lain untuk sementara.
     * Nanti sumber tunggalnya adalah model AlatTes.
     */
    protected const DUMMY_ALAT_TES = [
        ['id' => 3, 'nama' => 'EPPS (Edwards Personal Preference Schedule)', 'format_dasar' => 'Forced Choice'],
    ];

    /**
     * Soal dummy dikunci per alat_tes_id, struktur datanya beda per format.
     */
    protected const DUMMY_SOAL = [
        // EPPS — Forced Choice
        3 => [
            ['id' => 301, 'pernyataan_a' => 'Saya senang membantu orang lain memecahkan masalah pribadinya.', 'dimensi_a' => 'Nurturance', 'pernyataan_b' => 'Saya suka memenangkan setiap kompetisi yang saya ikuti.', 'dimensi_b' => 'Achievement'],
            ['id' => 302, 'pernyataan_a' => 'Saya lebih suka bekerja sendiri daripada berkelompok.',             'dimensi_a' => 'Autonomy',    'pernyataan_b' => 'Saya merasa paling hidup saat bersama banyak orang.', 'dimensi_b' => 'Affiliation'],
            ['id' => 303, 'pernyataan_a' => 'Saya teratur dan rapi dalam segala hal.',                            'dimensi_a' => 'Order',       'pernyataan_b' => 'Saya spontan dan fleksibel terhadap perubahan.', 'dimensi_b' => 'Change'],
            ['id' => 304, 'pernyataan_a' => 'Saya memimpin dengan memberi contoh, bukan perintah.',               'dimensi_a' => 'Dominance',   'pernyataan_b' => 'Saya memimpin dengan membangun konsensus.', 'dimensi_b' => 'Affiliation'],
            ['id' => 305, 'pernyataan_a' => 'Saya cepat mengambil keputusan penting.',                             'dimensi_a' => 'Dominance',   'pernyataan_b' => 'Saya mengumpulkan banyak data dulu sebelum bertindak.', 'dimensi_b' => 'Endurance'],
        ],
    ];

    public function index(): View
    {
        $alatTesSemua = self::DUMMY_ALAT_TES;

        // Kelompokkan semua soal per alat tes untuk tampilan tanpa filter
        $kelompokSoalSemua = [];
        foreach (self::DUMMY_SOAL as $alatId => $soalList) {
            $kelompokSoalSemua[$alatId] = [
                'alat'      => null,
                'soal'      => $soalList,
                'jumlahSoal' => count($soalList),
            ];
            foreach ($alatTesSemua as $alat) {
                if ($alat['id'] === $alatId) {
                    $kelompokSoalSemua[$alatId]['alat'] = $alat;
                    break;
                }
            }
        }

        $alatTesTerpilih = null;
        $daftarSoal = [];

        $idDipilih = (int) request('alat_tes_id', 0);
        if ($idDipilih > 0) {
            foreach ($alatTesSemua as $alat) {
                if ($alat['id'] === $idDipilih) {
                    $alatTesTerpilih = $alat;
                    $daftarSoal = self::DUMMY_SOAL[$idDipilih] ?? [];
                    break;
                }
            }
        }

        return view('admin.bank-soal.index', [
            'alatTesSemua'   => $alatTesSemua,
            'kelompokSoalSemua' => $kelompokSoalSemua,
            'alatTesTerpilih' => $alatTesTerpilih,
            'daftarSoal'     => $daftarSoal,
            'totalSemuaSoal' => array_sum(array_map('count', self::DUMMY_SOAL)),
        ]);
    }

    public function tambah(int $alatTesId): View
    {
        $alatTes = null;
        foreach (self::DUMMY_ALAT_TES as $alat) {
            if ($alat['id'] === $alatTesId) {
                $alatTes = $alat;
                break;
            }
        }

        abort_if($alatTes === null, 404, 'Alat Tes tidak ditemukan.');

        return view('admin.bank-soal.tambah', [
            'alatTes' => $alatTes,
        ]);
    }
}

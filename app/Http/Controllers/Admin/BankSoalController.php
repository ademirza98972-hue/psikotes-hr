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
        ['id' => 1, 'nama' => 'DISC',                          'format_dasar' => 'Skala Likert'],
        ['id' => 2, 'nama' => 'IST (Intelligence Structure Test)', 'format_dasar' => 'Pilihan Ganda'],
        ['id' => 3, 'nama' => 'EPPS (Edwards Personal Preference Schedule)', 'format_dasar' => 'Forced Choice'],
        ['id' => 4, 'nama' => 'MMPI-2',                        'format_dasar' => 'Skala Likert'],
    ];

    /**
     * Soal dummy dikunci per alat_tes_id, struktur datanya beda per format.
     */
    protected const DUMMY_SOAL = [
        // DISC — Skala Likert
        1 => [
            ['id' => 101, 'pernyataan' => 'Saya mudah bergaul dengan orang baru.',                              'dimensi' => 'Influence'],
            ['id' => 102, 'pernyataan' => 'Saya cenderung menganalisis masalah sebelum mengambil keputusan.',   'dimensi' => 'Compliance'],
            ['id' => 103, 'pernyataan' => 'Saya lebih memilih bekerja dalam kelompok yang aktif.',              'dimensi' => 'Steadiness'],
            ['id' => 104, 'pernyataan' => 'Saya nyaman memimpin dan mengarahkan orang lain.',                   'dimensi' => 'Dominance'],
            ['id' => 105, 'pernyataan' => 'Saya sulit berubah jika rencana sudah saya tentukan.',               'dimensi' => 'Steadiness'],
        ],
        // IST — Pilihan Ganda
        2 => [
            ['id' => 201, 'teks_soal' => 'Manakah yang melanjutkan deret: 2, 4, 8, 16, ...?',                   'opsi' => ['20', '24', '32', '64'], 'kunci' => 'C'],
            ['id' => 202, 'teks_soal' => 'Lawan kata dari "OPTIMIS" adalah...',                                   'opsi' => ['Pesimis', 'Realistis', 'Sinis', 'Apatis'], 'kunci' => 'A'],
            ['id' => 203, 'teks_soal' => 'Jika 5 mesin butuh 5 menit untuk membuat 5 widget, 100 mesin butuh berapa lama untuk membuat 100 widget?', 'opsi' => ['1 menit', '5 menit', '100 menit', '20 menit'], 'kunci' => 'B'],
            ['id' => 204, 'teks_soal' => 'Manakah angka yang tidak termasuk: 2, 3, 5, 7, 11, 13, 15?',           'opsi' => ['11', '13', '15', '7'], 'kunci' => 'C'],
        ],
        // EPPS — Forced Choice
        3 => [
            ['id' => 301, 'pernyataan_a' => 'Saya senang membantu orang lain memecahkan masalah pribadinya.', 'dimensi_a' => 'Nurturance', 'pernyataan_b' => 'Saya suka memenangkan setiap kompetisi yang saya ikuti.', 'dimensi_b' => 'Achievement'],
            ['id' => 302, 'pernyataan_a' => 'Saya lebih suka bekerja sendiri daripada berkelompok.',             'dimensi_a' => 'Autonomy',    'pernyataan_b' => 'Saya merasa paling hidup saat bersama banyak orang.', 'dimensi_b' => 'Affiliation'],
            ['id' => 303, 'pernyataan_a' => 'Saya teratur dan rapi dalam segala hal.',                            'dimensi_a' => 'Order',       'pernyataan_b' => 'Saya spontan dan fleksibel terhadap perubahan.', 'dimensi_b' => 'Change'],
            ['id' => 304, 'pernyataan_a' => 'Saya memimpin dengan memberi contoh, bukan perintah.',               'dimensi_a' => 'Dominance',   'pernyataan_b' => 'Saya memimpin dengan membangun konsensus.', 'dimensi_b' => 'Affiliation'],
            ['id' => 305, 'pernyataan_a' => 'Saya cepat mengambil keputusan penting.',                             'dimensi_a' => 'Dominance',   'pernyataan_b' => 'Saya mengumpulkan banyak data dulu sebelum bertindak.', 'dimensi_b' => 'Endurance'],
        ],
        // MMPI-2 — Skala Likert (sensitif)
        4 => [
            ['id' => 401, 'pernyataan' => 'Saya kadang merasa tidak ada yang benar-benar memahami saya.',       'dimensi' => 'D'],
            ['id' => 402, 'pernyataan' => 'Saya sesekali mendengar hal-hal yang tidak bisa orang lain dengar.', 'dimensi' => 'Pa'],
            ['id' => 403, 'pernyataan' => 'Saya merasa bersalah tanpa alasan yang jelas.',                         'dimensi' => 'Pt'],
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

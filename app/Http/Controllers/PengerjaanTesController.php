<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PengerjaanTesController extends Controller
{
    /**
     * Data dummy soal untuk alat tes - EPPS (Forced Choice)
     */
    private function getSoalDummy(): array
    {
        return [
            'EPPS' => [
                'nama_lengkap' => 'Edwards Personal Preference Schedule',
                'format_dasar' => 'Pilihan Ganda',
                'jumlah_soal'  => 5,
                'soal'         => [
                    [
                        'nomor'       => 1,
                        'statement_a' => 'Saya senang menjadi pusat perhatian dalam kelompok.',
                        'statement_b' => 'Saya lebih nyaman mengamati dan mendengarkan orang lain.',
                        'type'        => 'forced_choice',
                    ],
                    [
                        'nomor'       => 2,
                        'statement_a' => 'Saya berusaha agar orang lain merasa nyaman dengan cara saya.',
                        'statement_b' => 'Saya lebih jujur dan langsung menyatakan pendapat saya.',
                        'type'        => 'forced_choice',
                    ],
                    [
                        'nomor'       => 3,
                        'statement_a' => 'Saya enjoying tugas-tugas rutin yang jelas aturannya.',
                        'statement_b' => 'Saya lebih suka hal-hal baru dan tidak pasti.',
                        'type'        => 'forced_choice',
                    ],
                    [
                        'nomor'       => 4,
                        'statement_a' => 'Saya lebih memilih bekerja sendiri daripada dalam tim.',
                        'statement_b' => 'Saya aktif berpartisipasi dalam kegiatan kelompok.',
                        'type'        => 'forced_choice',
                    ],
                    [
                        'nomor'       => 5,
                        'statement_a' => 'Saya merasa perlu sering mengulang pekerjaan agar sempurna.',
                        'statement_b' => 'Saya tidak terlalu peduli pada detail kecil asalkan hasilnya bagus.',
                        'type'        => 'forced_choice',
                    ],
                ],
            ],
        ];
    }

    private function getSesiDummy(): array
    {
        return [
            ['id' => 1, 'nama_sesi' => 'Tes Rekrutmen Q3 2025',         'daftar_alat_tes_ditugaskan' => []],
            ['id' => 2, 'nama_sesi' => 'Evaluasi Kompetensi Internal', 'daftar_alat_tes_ditugaskan' => ['EPPS']],
            ['id' => 3, 'nama_sesi' => 'Assessment Awal Karyawan',     'daftar_alat_tes_ditugaskan' => ['EPPS']],
        ];
    }

    private function getSesiById(int $sesiId)
    {
        foreach ($this->getSesiDummy() as $s) {
            if ((int) $s['id'] === (int) $sesiId) {
                return $s;
            }
        }
        return null;
    }

    private function sessionKey(int $sesiId, string $name): string
    {
        return "pengerjaan_tes.sesi_{$sesiId}.{$name}";
    }

    public function kerjakan(Request $request, int $sesiId)
    {
        $sesi = $this->getSesiById($sesiId);
        if (!$sesi) {
            return redirect()->route('peserta.dashboard')->with('error', 'Sesi tes tidak ditemukan.');
        }

        $soalDummy     = $this->getSoalDummy();
        $daftarAlatTes = $sesi['daftar_alat_tes_ditugaskan'];

        // Flatten soal dari semua alat tes (hanya yang punya dummy soal)
        $daftarSoalFlat = [];
        foreach ($daftarAlatTes as $kodeAlat) {
            if (!isset($soalDummy[$kodeAlat])) {
                continue;
            }
            foreach ($soalDummy[$kodeAlat]['soal'] as $soal) {
                $daftarSoalFlat[] = [
                    'kode_alat_tes' => $kodeAlat,
                    'soal'          => $soal,
                ];
            }
        }

        // Inisialisasi step ke 0 untuk sesi baru (atau ambil dari session)
        $currentStep = (int) Session::get($this->sessionKey($sesiId, 'current_step'), 0);

        // Tangani query ?prev=1 (kembali ke soal sebelumnya)
        if ($request->query('prev') === '1' && $currentStep > 0) {
            $currentStep = $currentStep - 1;
            Session::put($this->sessionKey($sesiId, 'current_step'), $currentStep);
        }

        // Tangani query ?next=1 (loncat ke soal berikutnya tanpa menyimpan)
        if ($request->query('next') === '1' && $currentStep + 1 < count($daftarSoalFlat)) {
            $currentStep = $currentStep + 1;
            Session::put($this->sessionKey($sesiId, 'current_step'), $currentStep);
        }

        // Jika step melebihi total soal, berarti sudah selesai
        if ($currentStep >= count($daftarSoalFlat)) {
            return redirect()->route('peserta.tes.selesai', $sesiId);
        }

        $currentItem = $daftarSoalFlat[$currentStep];
        $kodeAlatTes = $currentItem['kode_alat_tes'];
        $soal        = $currentItem['soal'];

        // Hitung posisi soal dalam alat tes ini
        $soalNomorAlatIni   = $soal['nomor'];
        $alatTesIndex       = 0;
        foreach ($daftarAlatTes as $idx => $kode) {
            if (isset($soalDummy[$kode]) && $kode === $kodeAlatTes) {
                $alatTesIndex = $idx;
                break;
            }
        }

        // Ambil semua alat tes yang punya soal (untuk hitung total alat tes dengan soal)
        $alatTesDenganSoal = [];
        foreach ($daftarAlatTes as $kode) {
            if (isset($soalDummy[$kode])) {
                $alatTesDenganSoal[] = $kode;
            }
        }

        // Jawaban sebelumnya untuk soal ini
        $answers = Session::get($this->sessionKey($sesiId, 'answers'), []);
        $savedAnswer = $answers[$currentStep] ?? null;

        // Untuk progress bar: hitung progres di dalam alat tes ini
        $soalAlatTesIni = [];
        foreach ($daftarSoalFlat as $idx => $item) {
            if ($item['kode_alat_tes'] === $kodeAlatTes) {
                $soalAlatTesIni[] = $idx;
            }
        }
        $totalSoalAlatTes = count($soalAlatTesIni);

        // Variable untuk view
        $sesiIdView       = $sesiId;
        $nama_sesi        = $sesi['nama_sesi'];
        $kode_alat_tes    = $kodeAlatTes;
        $nama_alat_tes    = $soalDummy[$kodeAlatTes]['nama_lengkap'];
        $format_dasar     = $soalDummy[$kodeAlatTes]['format_dasar'];
        $alat_tes_index   = $alatTesIndex;
        $total_alat_tes   = count($alatTesDenganSoal);
        $soal_nomor       = $soalNomorAlatIni;
        $soal_total       = $totalSoalAlatTes;
        $soal_posisi_global = $currentStep;
        $soal_total_global = count($daftarSoalFlat);
        $soal_data        = $soal;
        $saved_answer     = $savedAnswer;
        $is_first_soal    = $currentStep === 0;
        $is_last_soal     = $currentStep === count($daftarSoalFlat) - 1;

        return view('peserta.pengerjaan-soal', [
            'sesiId'          => $sesiIdView,
            'nama_sesi'       => $nama_sesi,
            'kode_alat_tes'   => $kode_alat_tes,
            'nama_alat_tes'   => $nama_alat_tes,
            'format_dasar'    => $format_dasar,
            'alat_tes_index'  => $alat_tes_index,
            'total_alat_tes'  => $total_alat_tes,
            'soal_nomor'      => $soal_nomor,
            'soal_total'      => $soal_total,
            'soal_posisi_global' => $soal_posisi_global,
            'soal_total_global' => $soal_total_global,
            'soal_data'       => $soal_data,
            'saved_answer'    => $saved_answer,
            'is_first_soal'   => $is_first_soal,
            'is_last_soal'    => $is_last_soal,
        ]);
    }

    public function jawab(Request $request, int $sesiId)
    {
        $sesi = $this->getSesiById($sesiId);
        if (!$sesi) {
            return redirect()->route('peserta.dashboard')->with('error', 'Sesi tes tidak ditemukan.');
        }

        $soalDummy     = $this->getSoalDummy();
        $daftarAlatTes = $sesi['daftar_alat_tes_ditugaskan'];

        // Flatten semua soal untuk validasi
        $daftarSoalFlat = [];
        foreach ($daftarAlatTes as $kodeAlat) {
            if (!isset($soalDummy[$kodeAlat])) {
                continue;
            }
            foreach ($soalDummy[$kodeAlat]['soal'] as $soal) {
                $daftarSoalFlat[] = [
                    'kode_alat_tes' => $kodeAlat,
                    'soal'          => $soal,
                ];
            }
        }

        $currentStep = (int) Session::get($this->sessionKey($sesiId, 'current_step'), 0);

        // Validasi: pastikan step masih dalam range
        if ($currentStep >= count($daftarSoalFlat)) {
            return redirect()->route('peserta.tes.selesai', $sesiId);
        }

        $choice = $request->input('choice');
        if ($choice === null || $choice === '') {
            return redirect()->route('peserta.tes.kerjakan', $sesiId)->with('error', 'Silakan pilih jawaban terlebih dahulu.');
        }

        // Simpan jawaban
        $answers = Session::get($this->sessionKey($sesiId, 'answers'), []);
        $answers[$currentStep] = $choice;
        Session::put($this->sessionKey($sesiId, 'answers'), $answers);

        // Pindah ke soal berikutnya
        $nextStep = $currentStep + 1;
        if ($nextStep >= count($daftarSoalFlat)) {
            // Semua soal sudah dijawab, arahkan ke halaman selesai
            return redirect()->route('peserta.tes.selesai', $sesiId);
        }

        Session::put($this->sessionKey($sesiId, 'current_step'), $nextStep);

        return redirect()->route('peserta.tes.kerjakan', $sesiId);
    }

    public function selesai(int $sesiId)
    {
        $sesi = $this->getSesiById($sesiId);
        $namaSesi = $sesi['nama_sesi'] ?? null;

        return view('peserta.tes-selesai', [
            'sesiId'   => $sesiId,
            'namaSesi' => $namaSesi,
        ]);
    }
}

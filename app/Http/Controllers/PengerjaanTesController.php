<?php

namespace App\Http\Controllers;

use App\Models\PesertaSesiTes;
use App\Models\SesiTes;
use App\Models\Soal;
use App\Models\JawabanPeserta;
use App\Services\ScoringEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PengerjaanTesController extends Controller
{
    /**
     * Ambil semua sesi_tes di mana user login terdaftar sebagai peserta,
     * lengkap dengan daftar kode alat tes yang ditugaskan.
     */
    private function getSesiDummy(): array
    {
        $userId = auth()->id();
        if (!$userId) {
            return [];
        }

        $sesi = SesiTes::query()
            ->whereHas('pesertaSesiTes', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with(['alatTes' => function ($q) {
                $q->select('alat_tes.id', 'alat_tes.kode');
            }])
            ->orderBy('tanggal_mulai')
            ->get();

        return $sesi->map(function (SesiTes $s) {
            return [
                'id'                          => $s->id,
                'nama_sesi'                   => $s->nama_sesi,
                'daftar_alat_tes_ditugaskan' => $s->alatTes
                    ->pluck('kode')
                    ->all(),
            ];
        })->all();
    }

    private function getSesiById(int $sesiId)
    {
        $userId = auth()->id();
        if (!$userId) {
            return null;
        }

        $sesi = SesiTes::query()
            ->where('id', $sesiId)
            ->whereHas('pesertaSesiTes', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with(['alatTes' => function ($q) {
                $q->select('alat_tes.id', 'alat_tes.kode');
            }])
            ->first();

        if (!$sesi) {
            return null;
        }

        return [
            'id'                          => $sesi->id,
            'nama_sesi'                   => $sesi->nama_sesi,
            'daftar_alat_tes_ditugaskan' => $sesi->alatTes
                ->pluck('kode')
                ->all(),
        ];
    }

    /**
     * Ambil semua soal + opsi_jawaban untuk alat tes yang ditugaskan
     * ke sesi-sesi milik user login. Output keyed by kode_alat_tes.
     */
    private function getSoalDummy(): array
    {
        $userId = auth()->id();
        if (!$userId) {
            return [];
        }

        $sesi = SesiTes::query()
            ->whereHas('pesertaSesiTes', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with(['alatTes' => function ($q) {
                $q->with(['soal.opsiJawaban']);
            }])
            ->get();

        $result = [];
        foreach ($sesi as $s) {
            foreach ($s->alatTes as $alat) {
                $kode = $alat->kode;
                if (isset($result[$kode])) {
                    continue;
                }
                $result[$kode] = [
                    'nama_lengkap' => $alat->nama,
                    'format_dasar' => $alat->format_dasar,
                    'jumlah_soal'  => $alat->soal->count(),
                    'soal'         => $alat->soal
                        ->sortBy('nomor')
                        ->values()
                        ->map(function (Soal $soal) {
                            return [
                                'id'          => $soal->id,
                                'nomor'       => $soal->nomor,
                                'teks_soal'   => $soal->teks_soal,
                                'tipe_format' => $soal->tipe_format,
                                'opsi'        => $soal->opsiJawaban
                                    ->sortBy('urutan')
                                    ->values()
                                    ->map(function ($opsi) {
                                        return [
                                            'id'     => $opsi->id,
                                            'teks'   => $opsi->teks_opsi,
                                            'urutan' => $opsi->urutan,
                                        ];
                                    })->all(),
                            ];
                        })->all(),
                ];
            }
        }

        return $result;
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

        // Jawaban sebelumnya untuk soal ini — ambil dari DB (opsi_dipilih_id)
        $savedAnswer = JawabanPeserta::where('user_id', auth()->id())
            ->where('sesi_tes_id', $sesiId)
            ->where('soal_id', $soal['id'])
            ->value('opsi_dipilih_id');

        // Variable untuk view
        $sesiIdView           = $sesiId;
        $nama_sesi            = $sesi['nama_sesi'];
        $kode_alat_tes        = $kodeAlatTes;
        $nama_alat_tes        = $soalDummy[$kodeAlatTes]['nama_lengkap'];
        $format_dasar         = $soalDummy[$kodeAlatTes]['format_dasar'];
        $alat_tes_index       = $alatTesIndex;
        $total_alat_tes       = count($alatTesDenganSoal);
        $soal_nomor           = $soalNomorAlatIni;
        $soal_total           = count($soalDummy[$kodeAlatTes]['soal']);
        $soal_posisi_global   = $currentStep;
        $soal_total_global    = count($daftarSoalFlat);
        $soal_data            = $soal;
        $saved_answer         = $savedAnswer;
        $is_first_soal        = $currentStep === 0;
        $is_last_soal         = $currentStep === count($daftarSoalFlat) - 1;

        return view('peserta.pengerjaan-soal', [
            'sesiId'            => $sesiIdView,
            'nama_sesi'         => $nama_sesi,
            'kode_alat_tes'     => $kode_alat_tes,
            'nama_alat_tes'     => $nama_alat_tes,
            'format_dasar'      => $format_dasar,
            'alat_tes_index'    => $alat_tes_index,
            'total_alat_tes'    => $total_alat_tes,
            'soal_nomor'        => $soal_nomor,
            'soal_total'        => $soal_total,
            'soal_posisi_global' => $soal_posisi_global,
            'soal_total_global' => $soal_total_global,
            'soal_data'         => $soal_data,
            'saved_answer'      => $saved_answer,
            'is_first_soal'     => $is_first_soal,
            'is_last_soal'      => $is_last_soal,
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

        $opsiId = $request->input('opsi_id');
        if ($opsiId === null || $opsiId === '') {
            return redirect()->route('peserta.tes.kerjakan', $sesiId)->with('error', 'Silakan pilih jawaban terlebih dahulu.');
        }

        $soal = $daftarSoalFlat[$currentStep]['soal'];
        $soalId = $soal['id'];

        // Simpan ke database (idempoten — updateOrCreate)
        JawabanPeserta::updateOrCreate(
            [
                'user_id'     => auth()->id(),
                'sesi_tes_id' => $sesiId,
                'soal_id'     => $soalId,
            ],
            [
                'opsi_dipilih_id' => (int) $opsiId,
                'waktu_jawab'     => now(),
            ]
        );

        // Pindah ke soal berikutnya (session hanya untuk navigasi)
        $nextStep = $currentStep + 1;
        if ($nextStep >= count($daftarSoalFlat)) {
            return redirect()->route('peserta.tes.selesai', $sesiId);
        }

        Session::put($this->sessionKey($sesiId, 'current_step'), $nextStep);

        return redirect()->route('peserta.tes.kerjakan', $sesiId);
    }

    public function selesai(int $sesiId)
    {
        $sesi = $this->getSesiById($sesiId);
        if (!$sesi) {
            return redirect()->route('peserta.dashboard')->with('error', 'Sesi tes tidak ditemukan.');
        }

        $soalDummy     = $this->getSoalDummy();
        $daftarAlatTes = $sesi['daftar_alat_tes_ditugaskan'];
        $userId        = auth()->id();

        // Muat alat_tes_id untuk setiap kode alat tes yang ditugaskan
        $alatTesByKode = SesiTes::where('id', $sesiId)
            ->with('alatTes:id,kode')
            ->first()
            ->alatTes
            ->keyBy('kode')
            ->map(fn ($a) => $a->id);

        // Panggil scoring untuk setiap alat tes yang pakai forced_choice
        $scoringService = app(ScoringEngineService::class);
        $skorHasil      = [];

        foreach ($daftarAlatTes as $kodeAlat) {
            if (!isset($soalDummy[$kodeAlat])) {
                continue;
            }

            $alatTesId = $alatTesByKode[$kodeAlat] ?? null;
            if (!$alatTesId) {
                continue;
            }

            $formatDasar = strtolower(str_replace(' ', '_', $soalDummy[$kodeAlat]['format_dasar'] ?? ''));
            if ($formatDasar === 'forced_choice') {
                // TODO: kelompok_segmen EPPS (1-4) perlu dikonfirmasi ke psikolog
                // (kemungkinan berdasarkan tingkat pendidikan peserta).
                // Sementara pakai '2' yang punya data norma paling lengkap.
                $kelompokSegmen = '2';
                $skorHasil[$kodeAlat] = $scoringService->scoreForcedChoice(
                    $userId,
                    $sesiId,
                    $alatTesId,
                    $kelompokSegmen
                );
            }
        }

        PesertaSesiTes::where('user_id', auth()->id())
            ->where('sesi_tes_id', $sesiId)
            ->update([
                'status_pengerjaan' => 'Selesai',
                'tanggal_pengerjaan' => now(),
            ]);

        return view('peserta.tes-selesai', [
            'sesiId'  => $sesiId,
            'namaSesi' => $sesi['nama_sesi'],
            'skorHasil' => $skorHasil,
        ]);
    }
}

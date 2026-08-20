<?php

namespace App\Http\Controllers;

use App\Models\GridInputPeserta;
use App\Models\HasilKolomGrid;
use App\Models\PesertaAlatTes;
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
    public function __construct(private ScoringEngineService $scoringService) {}

    /**
     * Ambil semua sesi_tes di mana user login terdaftar sebagai peserta,
     * lengkap dengan daftar kode alat tes yang ditugaskan.
     */
    private function getSesiPeserta(): array
    {
        $userId = auth()->id();
        if (!$userId) return [];

        $pesertaSesiList = PesertaSesiTes::where('user_id', $userId)
            ->with(['sesiTes', 'alatTes'])
            ->get();

        return $pesertaSesiList->map(function (PesertaSesiTes $ps) {
            return [
                'id'                         => $ps->sesi_tes_id,
                'nama_sesi'                  => $ps->sesiTes?->nama_sesi ?? '—',
                'peserta_sesi_tes_id'        => $ps->id,
                'daftar_alat_tes_ditugaskan' => $ps->alatTes->pluck('kode')->all(),
            ];
        })->all();
    }

    private function getSesiById(int $sesiId): ?array
    {
        $userId = auth()->id();
        if (!$userId) return null;

        $pesertaSesi = PesertaSesiTes::where('sesi_tes_id', $sesiId)
            ->where('user_id', $userId)
            ->first();

        if (!$pesertaSesi) return null;

        $sesi = SesiTes::find($sesiId);
        if (!$sesi) return null;

        $alatTesPeserta = PesertaAlatTes::where('peserta_sesi_tes_id', $pesertaSesi->id)
            ->with('alatTes:id,kode,nama,format_dasar')
            ->get()
            ->pluck('alatTes')
            ->filter()
            ->values();

        return [
            'id'                         => $sesi->id,
            'nama_sesi'                  => $sesi->nama_sesi,
            'peserta_sesi_tes_id'        => $pesertaSesi->id,
            'daftar_alat_tes_ditugaskan' => $alatTesPeserta->pluck('kode')->all(),
        ];
    }

    /**
     * Ambil semua soal + opsi_jawaban untuk alat tes yang ditugaskan
     * ke sesi-sesi milik user login. Output keyed by kode_alat_tes.
     */
    private function getSoalPeserta(): array
    {
        $userId = auth()->id();
        if (!$userId) return [];

        $pesertaSesiList = PesertaSesiTes::where('user_id', $userId)
            ->with(['alatTes.soal.opsiJawaban'])
            ->get();

        $result = [];
        foreach ($pesertaSesiList as $ps) {
            foreach ($ps->alatTes as $alat) {
                $kode = $alat->kode;
                if (isset($result[$kode])) continue;

                $isGrid = $alat->format_dasar === 'Grid' || ($alat->pola_skoring ?? '') === 'grid';

                if ($isGrid) {
                    $result[$kode] = [
                        'nama_lengkap' => $alat->nama,
                        'format_dasar' => $alat->format_dasar,
                        'jumlah_soal'  => $alat->soal->count(),
                        'pola_skoring' => $alat->pola_skoring ?? '',
                        'soal'         => $alat->soal
                            ->sortBy('nomor')
                            ->values()
                            ->map(fn($soal) => [
                                'id'          => $soal->id,
                                'nomor'       => $soal->nomor,
                                'angka'       => array_map('intval',
                                                    explode(',', $soal->teks_soal)),
                                'tipe_format' => 'grid',
                            ])->all(),
                    ];
                } else {
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
                                        ->map(fn($opsi) => [
                                            'id'        => $opsi->id,
                                            'teks'      => $opsi->teks_opsi,
                                            'urutan'    => $opsi->urutan,
                                            'gambar'    => $opsi->gambar_opsi ?? null,
                                        ])->all(),
                                    'gambar_soal'   => $soal->gambar_soal ?? null,
                                    'kunci_jawaban' => $soal->kunci_jawaban ?? null,
                                ];
                            })->all(),
                    ];
                }
            }
        }

        return $result;
    }

    private function sessionKey(int $sesiId, string $name): string
    {
        return "pengerjaan_tes.sesi_{$sesiId}.{$name}";
    }

    private function getKodeSubtesIst(int $nomor): string
    {
        return match(true) {
            $nomor <= 20  => 'SE',
            $nomor <= 40  => 'WA',
            $nomor <= 60  => 'AN',
            $nomor <= 76  => 'GE',
            $nomor <= 96  => 'RA',
            $nomor <= 116 => 'ZR',
            $nomor <= 136 => 'FA',
            $nomor <= 156 => 'WU',
            default       => 'ME',
        };
    }

    private function getOrCreateSoalOrder(int $sesiId, string $kodeAlatTes, array $soalIds, ?string $kodeSubtes = null): array
    {
        $key = $kodeSubtes
            ? $this->sessionKey($sesiId, "soal_order_{$kodeAlatTes}_{$kodeSubtes}")
            : $this->sessionKey($sesiId, "soal_order_{$kodeAlatTes}");

        $stored = Session::get($key);
        if ($stored !== null) {
            return $stored;
        }

        $shuffled = $soalIds;
        shuffle($shuffled);
        Session::put($key, $shuffled);
        return $shuffled;
    }

    private function getOrCreateOpsiOrder(int $sesiId, int $soalId, array $opsiIds): array
    {
        $key = $this->sessionKey($sesiId, "opsi_order_{$soalId}");

        $stored = Session::get($key);
        if ($stored !== null) {
            return $stored;
        }

        $shuffled = $opsiIds;
        shuffle($shuffled);
        Session::put($key, $shuffled);
        return $shuffled;
    }

    public function kerjakan(Request $request, int $sesiId)
    {
        $sesi = $this->getSesiById($sesiId);
        if (!$sesi) {
            return redirect()->route('peserta.dashboard')->with('error', 'Sesi tes tidak ditemukan.');
        }

        $soalDummy     = $this->getSoalPeserta();
        $daftarAlatTes = $sesi['daftar_alat_tes_ditugaskan'];

        // Ambil alat_tes_id untuk setiap kode alat tes yang ditugaskan
        $pesertaSesiTesId = $sesi['peserta_sesi_tes_id'];
        $alatTesByKode = \App\Models\PesertaAlatTes::where('peserta_sesi_tes_id', $pesertaSesiTesId)
            ->with('alatTes:id,kode')
            ->get()
            ->pluck('alatTes')
            ->filter()
            ->keyBy('kode')
            ->map(fn ($a) => $a->id);

        // Hitung jumlah soal yang sudah dijawab per alat tes
        $userId = auth()->id();
        $alatTesSudahSelesai = \App\Models\JawabanPeserta::where('user_id', $userId)
            ->where('sesi_tes_id', $sesiId)
            ->whereHas('soal', function($q) use ($alatTesByKode) {
                $q->whereIn('alat_tes_id', $alatTesByKode);
            })
            ->with('soal')
            ->get()
            ->groupBy(fn($j) => $j->soal->alat_tes_id);

        // Tentukan alat tes yang sedang aktif (pertama yang belum selesai)
        $kodeAlatTesAktif = null;
        foreach ($daftarAlatTes as $kodeAlat) {
            $alatTesId = $alatTesByKode[$kodeAlat] ?? null;
            if (!$alatTesId) continue;

            $totalSoal = count($soalDummy[$kodeAlat]['soal'] ?? []);
            $totalDijawab = $alatTesSudahSelesai->get($alatTesId)?->count() ?? 0;

            if ($totalDijawab < $totalSoal) {
                $kodeAlatTesAktif = $kodeAlat;
                break;
            }
        }

        // Jika semua alat tes sudah selesai, redirect ke halaman selesai
        if (!$kodeAlatTesAktif) {
            return redirect()->route('peserta.tes.selesai', $sesiId);
        }

        // Inisialisasi step ke 0 untuk sesi baru (atau ambil dari session)
        $currentStep = (int) Session::get($this->sessionKey($sesiId, 'current_step'), 0);

        // Reset current step jika berpindah alat tes
        $kodeAlatTesSesiKey = $this->sessionKey($sesiId, 'kode_alat_tes_aktif');
        $kodeAlatTesSesiSebelumnya = Session::get($kodeAlatTesSesiKey);
        if ($kodeAlatTesSesiSebelumnya !== $kodeAlatTesAktif) {
            Session::put($kodeAlatTesSesiKey, $kodeAlatTesAktif);
            Session::put($this->sessionKey($sesiId, 'current_step'), 0);
            Session::forget($this->sessionKey($sesiId, 'timer_sesi_' . $kodeAlatTesSesiSebelumnya));
            Session::forget($this->sessionKey($sesiId, 'timer_sesi_' . $kodeAlatTesAktif));
            $currentStep = 0;
        }

        // Flatten hanya soal dari alat tes aktif
        $daftarSoalFlat = [];
        foreach ($soalDummy[$kodeAlatTesAktif]['soal'] as $soal) {
            $daftarSoalFlat[] = ['kode_alat_tes' => $kodeAlatTesAktif, 'soal' => $soal];
        }

        // Randomisasi urutan soal berdasarkan alat tes
        if ($kodeAlatTesAktif === 'IST') {
            $subtesUrutan = ['SE','WA','AN','GE','RA','ZR','FA','WU','ME'];
            $soalPerSubtes = [];
            foreach ($daftarSoalFlat as $item) {
                $nomor = $item['soal']['nomor'];
                $subtes = match(true) {
                    $nomor <= 20  => 'SE',
                    $nomor <= 40  => 'WA',
                    $nomor <= 60  => 'AN',
                    $nomor <= 76  => 'GE',
                    $nomor <= 96  => 'RA',
                    $nomor <= 116 => 'ZR',
                    $nomor <= 136 => 'FA',
                    $nomor <= 156 => 'WU',
                    default        => 'ME',
                };
                $soalPerSubtes[$subtes][] = $item;
            }
            $daftarSoalFlat = [];
            foreach ($subtesUrutan as $subtes) {
                if (empty($soalPerSubtes[$subtes])) continue;
                $ids = array_map(fn($i) => $i['soal']['id'], $soalPerSubtes[$subtes]);
                $orderedIds = $this->getOrCreateSoalOrder($sesiId, $kodeAlatTesAktif, $ids, $subtes);
                $idToItem = [];
                foreach ($soalPerSubtes[$subtes] as $item) {
                    $idToItem[$item['soal']['id']] = $item;
                }
                foreach ($orderedIds as $id) {
                    if (isset($idToItem[$id])) {
                        $daftarSoalFlat[] = $idToItem[$id];
                    }
                }
            }
        } elseif (in_array($kodeAlatTesAktif, ['EPPS', 'PAP'])) {
            $ids = array_map(fn($item) => $item['soal']['id'], $daftarSoalFlat);
            $orderedIds = $this->getOrCreateSoalOrder($sesiId, $kodeAlatTesAktif, $ids);
            $idToItem = [];
            foreach ($daftarSoalFlat as $item) {
                $idToItem[$item['soal']['id']] = $item;
            }
            $daftarSoalFlat = [];
            foreach ($orderedIds as $id) {
                if (isset($idToItem[$id])) {
                    $daftarSoalFlat[] = $idToItem[$id];
                }
            }
        }

        // Tangani query ?prev=1 (kembali ke soal sebelumnya)
        if ($request->query('prev') === '1' && $currentStep > 0) {
            $alatTesCheck = \App\Models\AlatTes::where('kode', $kodeAlatTesAktif)->first();
            $blockedPerSoal = $alatTesCheck?->batas_waktu_per_soal_aktif;
            $blockedIst = $kodeAlatTesAktif === 'IST'
                && $this->getKodeSubtesIst($daftarSoalFlat[$currentStep]['soal']['nomor'] ?? 0)
                   !== $this->getKodeSubtesIst($daftarSoalFlat[$currentStep - 1]['soal']['nomor'] ?? 0);
            if ($blockedPerSoal || $blockedIst) {
                $pesan = $blockedIst
                    ? 'Tidak dapat kembali ke subtes sebelumnya. Selesaikan subtes saat ini sesuai urutan.'
                    : 'Tidak dapat kembali ke soal sebelumnya karena timer per soal sedang aktif.';
                return redirect()->route('peserta.tes.kerjakan', $sesiId)->with('error', $pesan);
            }
            $currentStep = $currentStep - 1;
            Session::put($this->sessionKey($sesiId, 'current_step'), $currentStep);
        }

        // Tangani query ?next=1 (loncat ke soal berikutnya tanpa menyimpan)
        if ($request->query('next') === '1' && $currentStep + 1 < count($daftarSoalFlat)) {
            $currentStep = $currentStep + 1;
            Session::put($this->sessionKey($sesiId, 'current_step'), $currentStep);
        }

        // Tangani query ?step=N (lompat ke nomor soal tertentu)
        if ($request->query('step') !== null) {
            $targetStep = (int) $request->query('step');
            if ($targetStep >= 0 && $targetStep < count($daftarSoalFlat)) {
                $kodeTarget = $daftarSoalFlat[$targetStep]['kode_alat_tes'] ?? '';
                $alatTesTarget = \App\Models\AlatTes::where('kode', $kodeTarget)->first();
                $adaTimerPerSoal = $alatTesTarget?->batas_waktu_per_soal_aktif ?? false;
                $blockedIst = $kodeAlatTesAktif === 'IST'
                    && $this->getKodeSubtesIst($daftarSoalFlat[$currentStep]['soal']['nomor'] ?? 0)
                       !== $this->getKodeSubtesIst($daftarSoalFlat[$targetStep]['soal']['nomor'] ?? 0);
                if ($adaTimerPerSoal || $blockedIst) {
                    $pesan = $blockedIst
                        ? 'Tidak dapat pindah ke subtes lain. Selesaikan subtes saat ini terlebih dahulu sesuai urutan.'
                        : 'Tidak dapat berpindah soal karena timer per soal sedang aktif.';
                    return redirect()->route('peserta.tes.kerjakan', $sesiId)->with('error', $pesan);
                }
                $currentStep = $targetStep;
                Session::put($this->sessionKey($sesiId, 'current_step'), $currentStep);
            }
        }

        // Jika step melebihi total soal, berarti sudah selesai
        if ($currentStep >= count($daftarSoalFlat)) {
            return redirect()->route('peserta.tes.selesai', $sesiId);
        }

        $currentItem = $daftarSoalFlat[$currentStep];
        $kodeAlatTes = $currentItem['kode_alat_tes'];
        $soal        = $currentItem['soal'];

        // Randomisasi posisi opsi jawaban
        if (isset($soal['opsi']) && is_array($soal['opsi']) && count($soal['opsi']) > 1) {
            $opsiIds = array_map(fn($o) => $o['id'], $soal['opsi']);
            $orderedOpsiIds = $this->getOrCreateOpsiOrder($sesiId, $soal['id'], $opsiIds);
            $idToOpsi = [];
            foreach ($soal['opsi'] as $o) {
                $idToOpsi[$o['id']] = $o;
            }
            $soal['opsi'] = [];
            foreach ($orderedOpsiIds as $oid) {
                if (isset($idToOpsi[$oid])) {
                    $soal['opsi'][] = $idToOpsi[$oid];
                }
            }
        }

        if (($soal['tipe_format'] ?? '') === 'grid') {
            return redirect()->route('peserta.tes.kraepelin', $sesiId);
        }

        // EPPS: panduan sekali di awal
        if ($kodeAlatTes === 'EPPS' && !Session::get($this->sessionKey($sesiId, 'epps_panduan_selesai'))) {
            return redirect()->route('peserta.tes.panduan-epps', $sesiId);
        }

        // PAPI Kostick: panduan sekali di awal
        if ($kodeAlatTes === 'PAP' && !Session::get($this->sessionKey($sesiId, 'papi_panduan_selesai'))) {
            return redirect()->route('peserta.tes.panduan-papi', $sesiId);
        }

        // IST: panduan per subtes + hafalan ME
        if ($kodeAlatTes === 'IST') {
            $nomorSoal = $soal['nomor'] ?? 0;
            $kodeSub = match(true) {
                $nomorSoal <= 20  => 'SE',
                $nomorSoal <= 40  => 'WA',
                $nomorSoal <= 60  => 'AN',
                $nomorSoal <= 76  => 'GE',
                $nomorSoal <= 96  => 'RA',
                $nomorSoal <= 116 => 'ZR',
                $nomorSoal <= 136 => 'FA',
                $nomorSoal <= 156 => 'WU',
                default           => 'ME',
            };
            if ($kodeSub === 'ME') {
                if (!Session::get($this->sessionKey($sesiId, 'me_hafalan_selesai'))) {
                    return redirect()->route('peserta.tes.hafalan-me', $sesiId);
                }
            } else {
                if (!Session::get($this->sessionKey($sesiId, "ist_panduan_{$kodeSub}_selesai"))) {
                    return redirect()->route('peserta.tes.panduan-ist', [$sesiId, $kodeSub]);
                }
            }
        }

        // IST per-subtes timer
        $kodeSubtesSaat = $kodeAlatTes;
        $alatTesSaat = \App\Models\AlatTes::where('kode', $kodeSubtesSaat)->first()
            ?? \App\Models\AlatTes::find($alatTesByKode->get($kodeSubtesSaat) ?? 0);
        $isIST = ($alatTesSaat?->kode === 'IST' || str_contains($alatTesSaat?->nama ?? '', 'IST'));

        // Per-soal timer (berlaku untuk semua alat tes yang mengaktifkannya)
        $detikPerSoal = ($alatTesSaat?->batas_waktu_per_soal_aktif && $alatTesSaat?->batas_waktu_per_soal_detik)
            ? (int) $alatTesSaat->batas_waktu_per_soal_detik
            : null;

        $sisaDetikPerSoal = $detikPerSoal;
        if ($detikPerSoal) {
            $timerPerSoalKey = $this->sessionKey($sesiId, 'timer_per_soal_' . $soal['id']);
            $waktuMulaiPerSoal = Session::get($timerPerSoalKey);
            if (!$waktuMulaiPerSoal) {
                Session::put($timerPerSoalKey, now()->timestamp);
                $waktuMulaiPerSoal = now()->timestamp;
            }
            $sisaDetikPerSoal = max(0, $detikPerSoal - (now()->timestamp - $waktuMulaiPerSoal));
            if ($sisaDetikPerSoal <= 0) {
                Session::forget($timerPerSoalKey);
                $nextStep = $currentStep + 1;
                Session::put($this->sessionKey($sesiId, 'current_step'), $nextStep < count($daftarSoalFlat) ? $nextStep : 0);
                return redirect()->route('peserta.tes.kerjakan', $sesiId);
            }
        }

        $sisaWaktuDetik = null;
        $durasiSubtes = null;
        $kodeSubtesTimer = null;

        if ($isIST) {
            $nomorSoal = $soal['nomor'];
            $kodeSubtesTimer = match(true) {
                $nomorSoal <= 20  => 'SE',
                $nomorSoal <= 40  => 'WA',
                $nomorSoal <= 60  => 'AN',
                $nomorSoal <= 76  => 'GE',
                $nomorSoal <= 96  => 'RA',
                $nomorSoal <= 116 => 'ZR',
                $nomorSoal <= 136 => 'FA',
                $nomorSoal <= 156 => 'WU',
                default           => 'ME',
            };

            $dimensiSubtes = \App\Models\DimensiAlatTes::where('alat_tes_id', $alatTesSaat->id)
                ->where('kode_dimensi', $kodeSubtesTimer)
                ->first();
            $durasiSubtes = $dimensiSubtes?->durasi_detik ?? 420;

            $timerKey = $this->sessionKey($sesiId, "timer_subtes_{$kodeSubtesTimer}");
            $waktuMulaiSubtes = Session::get($timerKey);

            if (!$waktuMulaiSubtes) {
                Session::put($timerKey, now()->timestamp);
                $waktuMulaiSubtes = now()->timestamp;
            }

            $sisaWaktuDetik = max(0, $durasiSubtes - (now()->timestamp - $waktuMulaiSubtes));

            if ($sisaWaktuDetik <= 0) {
                $subtesUrutan = ['SE','WA','AN','GE','RA','ZR','FA','WU','ME'];
                $idxSubtesSaat = array_search($kodeSubtesTimer, $subtesUrutan);
                $kodeSubtesBerikutnya = $subtesUrutan[$idxSubtesSaat + 1] ?? null;

                if ($kodeSubtesBerikutnya) {
                    foreach ($daftarSoalFlat as $stepIdx => $item) {
                        $nomorItem = $item['soal']['nomor'];
                        $kodeItem = match(true) {
                            $nomorItem <= 20  => 'SE',
                            $nomorItem <= 40  => 'WA',
                            $nomorItem <= 60  => 'AN',
                            $nomorItem <= 76  => 'GE',
                            $nomorItem <= 96  => 'RA',
                            $nomorItem <= 116 => 'ZR',
                            $nomorItem <= 136 => 'FA',
                            $nomorItem <= 156 => 'WU',
                            default           => 'ME',
                        };
                        if ($kodeItem === $kodeSubtesBerikutnya) {
                            Session::put($this->sessionKey($sesiId, 'current_step'), $stepIdx);
                            return redirect()->route('peserta.tes.kerjakan', $sesiId);
                        }
                    }
                }
                return redirect()->route('peserta.tes.jawab', $sesiId)->withInput(['_method' => 'POST', 'auto_submit' => true]);
            }
        }

        $sisaWaktuSesiDetik = null;
        $durasiSesiDetik = null;

        if (!$isIST) {
            $alatTesAktif = \App\Models\AlatTes::where('kode', $kodeAlatTesAktif)->first();
            $durasiMenit = $alatTesAktif?->durasi_total_menit ?? 0;
            $durasiSesiDetik = $durasiMenit * 60;

            if ($durasiSesiDetik > 0) {
                $timerSesiKey = $this->sessionKey($sesiId, 'timer_sesi_' . $kodeAlatTesAktif);
                $waktuMulaiSesi = Session::get($timerSesiKey);

                if (!$waktuMulaiSesi) {
                    Session::put($timerSesiKey, now()->timestamp);
                    $waktuMulaiSesi = now()->timestamp;
                }

                $sisaWaktuSesiDetik = max(0, $durasiSesiDetik - (now()->timestamp - $waktuMulaiSesi));

                if ($sisaWaktuSesiDetik <= 0) {
                    return redirect()->route('peserta.tes.selesai', $sesiId);
                }
            }
        }

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

        $jawabanRecord = JawabanPeserta::where('user_id', auth()->id())
            ->where('sesi_tes_id', $sesiId)
            ->where('soal_id', $soal['id'])
            ->first();
        $savedAnswer = $jawabanRecord?->opsi_dipilih_id;
        $savedAnswerTeks = $jawabanRecord?->jawaban_teks;

        // Bangun daftar nomor soal untuk navigasi sidebar
        $soalIds = collect($daftarSoalFlat)->pluck('soal.id')->filter()->values();
        $sudahDijawabIds = JawabanPeserta::where('user_id', auth()->id())
            ->where('sesi_tes_id', $sesiId)
            ->whereIn('soal_id', $soalIds)
            ->where(function($q) {
                $q->whereNotNull('opsi_dipilih_id')
                  ->orWhere(function($q2) {
                      $q2->whereNotNull('jawaban_teks')->where('jawaban_teks', '!=', '');
                  });
            })
            ->pluck('soal_id')
            ->toArray();

        $daftar_nomor_soal = collect($daftarSoalFlat)->map(function($item, $step) use ($currentStep, $sudahDijawabIds) {
            return [
                'step'          => $step,
                'nomor_soal'    => $item['soal']['nomor'],
                'kode_alat_tes' => $item['kode_alat_tes'],
                'sudah_dijawab' => in_array($item['soal']['id'], $sudahDijawabIds),
                'is_current'    => $step === $currentStep,
                'tipe_format'   => $item['soal']['tipe_format'] ?? 'pilihan_ganda',
            ];
        })->all();

        // Variable untuk view
        $sesiIdView           = $sesiId;
        $nama_sesi            = $sesi['nama_sesi'];
        $kode_alat_tes        = $kodeAlatTes;
        $nama_alat_tes        = $soalDummy[$kodeAlatTes]['nama_lengkap'];
        $format_dasar         = $soalDummy[$kodeAlatTes]['format_dasar'];
        $alat_tes_index       = $alatTesIndex;
        $total_alat_tes       = count($alatTesDenganSoal);
        $soal_nomor           = $currentStep + 1;
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
            'saved_answer_teks' => $savedAnswerTeks,
            'is_first_soal'     => $is_first_soal,
            'is_last_soal'      => $is_last_soal,
            'daftar_nomor_soal' => $daftar_nomor_soal,
            'sisa_waktu_sesi_detik' => $sisaWaktuSesiDetik ?? null,
            'durasi_sesi_detik'     => $durasiSesiDetik ?? null,
            'sisa_waktu_detik'      => $sisaWaktuDetik ?? null,
            'durasi_subtes'         => $durasiSubtes ?? null,
            'kode_subtes_timer'     => $kodeSubtesTimer ?? null,
            'is_ist'                => $isIST ?? false,
            'detik_per_soal'        => $detikPerSoal ?? null,
            'sisa_detik_per_soal'   => $sisaDetikPerSoal ?? null,
        ]);
    }

    public function panduanPapi(int $sesiId)
    {
        $sesi = $this->getSesiById($sesiId);
        if (!$sesi) return redirect()->route('peserta.dashboard');
        $alatTes = \App\Models\AlatTes::where('kode', 'PAP')->first();
        return view('peserta.panduan-papi', ['sesiId' => $sesiId, 'alatTes' => $alatTes]);
    }

    public function panduanPapiMulai(int $sesiId)
    {
        Session::put($this->sessionKey($sesiId, 'papi_panduan_selesai'), true);
        return redirect()->route('peserta.tes.kerjakan', $sesiId);
    }

    public function panduanKraepelin(int $sesiId)
    {
        $sesi = $this->getSesiById($sesiId);
        if (!$sesi) return redirect()->route('peserta.dashboard');
        $alatTes = \App\Models\AlatTes::where('kode', 'KRP')->orWhere('pola_skoring', 'grid')->first();
        return view('peserta.panduan-kraepelin', ['sesiId' => $sesiId, 'alatTes' => $alatTes]);
    }

    public function panduanKraepelinMulai(int $sesiId)
    {
        Session::put($this->sessionKey($sesiId, 'kraepelin_panduan_selesai'), true);
        return redirect()->route('peserta.tes.kraepelin', $sesiId);
    }

    public function panduanEpps(int $sesiId)
    {
        $sesi = $this->getSesiById($sesiId);
        if (!$sesi) return redirect()->route('peserta.dashboard');
        $alatTes = \App\Models\AlatTes::where('kode', 'EPPS')->first();
        return view('peserta.panduan-epps', [
            'sesiId'  => $sesiId,
            'alatTes' => $alatTes,
        ]);
    }

    public function panduanEppsMulai(int $sesiId)
    {
        Session::put($this->sessionKey($sesiId, 'epps_panduan_selesai'), true);
        return redirect()->route('peserta.tes.kerjakan', $sesiId);
    }

    public function panduanIst(int $sesiId, string $kodeSubtes)
    {
        $sesi = $this->getSesiById($sesiId);
        if (!$sesi) return redirect()->route('peserta.dashboard');
        $valid = ['SE','WA','AN','GE','RA','ZR','FA','WU'];
        if (!in_array($kodeSubtes, $valid)) return redirect()->route('peserta.tes.kerjakan', $sesiId);
        return view('peserta.panduan-ist-subtes', ['sesiId' => $sesiId, 'kodeSubtes' => $kodeSubtes]);
    }

    public function panduanIstMulai(int $sesiId, string $kodeSubtes)
    {
        Session::put($this->sessionKey($sesiId, "ist_panduan_{$kodeSubtes}_selesai"), true);
        return redirect()->route('peserta.tes.kerjakan', $sesiId);
    }

    public function hafalanMe(int $sesiId)
    {
        $sesi = $this->getSesiById($sesiId);
        if (!$sesi) {
            return redirect()->route('peserta.dashboard');
        }
        return view('peserta.hafalan-me', ['sesiId' => $sesiId]);
    }

    public function hafalanMeSelesai(Request $request, int $sesiId)
    {
        Session::put($this->sessionKey($sesiId, 'me_hafalan_selesai'), true);
        return redirect()->route('peserta.tes.kerjakan', $sesiId);
    }

    public function timeoutSesi(Request $request, int $sesiId)
    {
        $sesi = $this->getSesiById($sesiId);
        if (!$sesi) {
            return redirect()->route('peserta.tes.kerjakan', $sesiId)->with('error', 'Sesi tes tidak ditemukan.');
        }

        $soalDummy     = $this->getSoalPeserta();
        $daftarAlatTes = $sesi['daftar_alat_tes_ditugaskan'];
        $userId        = auth()->id();

        $pesertaSesiTesId = $sesi['peserta_sesi_tes_id'];
        $alatTesByKode = \App\Models\PesertaAlatTes::where('peserta_sesi_tes_id', $pesertaSesiTesId)
            ->with('alatTes:id,kode')
            ->get()
            ->pluck('alatTes')
            ->filter()
            ->keyBy('kode')
            ->map(fn ($a) => $a->id);

        // Tandai alat tes aktif yang belum selesai dengan menyimpan jawaban kosong
        $aktif = Session::get($this->sessionKey($sesiId, 'kode_alat_tes_aktif'));
        if ($aktif && isset($soalDummy[$aktif])) {
            $alatTesId = $alatTesByKode[$aktif] ?? null;
            foreach ($soalDummy[$aktif]['soal'] as $soal) {
                JawabanPeserta::updateOrCreate(
                    ['user_id' => $userId, 'sesi_tes_id' => $sesiId, 'soal_id' => $soal['id']],
                    ['opsi_dipilih_id' => null, 'jawaban_teks' => null, 'waktu_jawab' => now()]
                );
            }
        }

        // Reset step dan timer agar alat tes berikutnya dimulai fresh
        Session::put($this->sessionKey($sesiId, 'current_step'), 0);
        Session::forget($this->sessionKey($sesiId, 'kode_alat_tes_aktif'));

        return redirect()->route('peserta.tes.kerjakan', $sesiId);
    }

    public function jawab(Request $request, int $sesiId)
    {
        $sesi = $this->getSesiById($sesiId);
        if (!$sesi) {
            return redirect()->route('peserta.dashboard')->with('error', 'Sesi tes tidak ditemukan.');
        }

        $soalDummy     = $this->getSoalPeserta();
        $daftarAlatTes = $sesi['daftar_alat_tes_ditugaskan'];

        // Flatten hanya soal dari alat tes aktif (sama seperti kerjakan())
        $kodeAlatTesAktif = Session::get($this->sessionKey($sesiId, 'kode_alat_tes_aktif'));
        $daftarSoalFlat = [];
        if ($kodeAlatTesAktif && isset($soalDummy[$kodeAlatTesAktif])) {
            foreach ($soalDummy[$kodeAlatTesAktif]['soal'] as $soal) {
                $daftarSoalFlat[] = [
                    'kode_alat_tes' => $kodeAlatTesAktif,
                    'soal'          => $soal,
                ];
            }
        }

        // Randomisasi urutan soal berdasarkan alat tes (sama seperti kerjakan())
        if ($kodeAlatTesAktif === 'IST') {
            $subtesUrutan = ['SE','WA','AN','GE','RA','ZR','FA','WU','ME'];
            $soalPerSubtes = [];
            foreach ($daftarSoalFlat as $item) {
                $nomor = $item['soal']['nomor'];
                $subtes = match(true) {
                    $nomor <= 20  => 'SE',
                    $nomor <= 40  => 'WA',
                    $nomor <= 60  => 'AN',
                    $nomor <= 76  => 'GE',
                    $nomor <= 96  => 'RA',
                    $nomor <= 116 => 'ZR',
                    $nomor <= 136 => 'FA',
                    $nomor <= 156 => 'WU',
                    default        => 'ME',
                };
                $soalPerSubtes[$subtes][] = $item;
            }
            $daftarSoalFlat = [];
            foreach ($subtesUrutan as $subtes) {
                if (empty($soalPerSubtes[$subtes])) continue;
                $ids = array_map(fn($i) => $i['soal']['id'], $soalPerSubtes[$subtes]);
                $orderedIds = $this->getOrCreateSoalOrder($sesiId, $kodeAlatTesAktif, $ids, $subtes);
                $idToItem = [];
                foreach ($soalPerSubtes[$subtes] as $item) {
                    $idToItem[$item['soal']['id']] = $item;
                }
                foreach ($orderedIds as $id) {
                    if (isset($idToItem[$id])) {
                        $daftarSoalFlat[] = $idToItem[$id];
                    }
                }
            }
        } elseif (in_array($kodeAlatTesAktif, ['EPPS', 'PAP'])) {
            $ids = array_map(fn($item) => $item['soal']['id'], $daftarSoalFlat);
            $orderedIds = $this->getOrCreateSoalOrder($sesiId, $kodeAlatTesAktif, $ids);
            $idToItem = [];
            foreach ($daftarSoalFlat as $item) {
                $idToItem[$item['soal']['id']] = $item;
            }
            $daftarSoalFlat = [];
            foreach ($orderedIds as $id) {
                if (isset($idToItem[$id])) {
                    $daftarSoalFlat[] = $idToItem[$id];
                }
            }
        }

        $currentStep = (int) Session::get($this->sessionKey($sesiId, 'current_step'), 0);

        // Validasi: pastikan step masih dalam range
        if ($currentStep >= count($daftarSoalFlat)) {
            return redirect()->route('peserta.tes.selesai', $sesiId);
        }

        $soal = $daftarSoalFlat[$currentStep]['soal'];
        $soalId = $soal['id'];
        $tipeFormat = $soal['tipe_format'] ?? 'pilihan_ganda';

        Session::forget($this->sessionKey($sesiId, 'timer_per_soal_' . $soalId));

        $timerExpired = $request->boolean('timer_expired');

        if (in_array($tipeFormat, ['isian_teks', 'isian_angka'])) {
            $jawabanTeks = $request->input('jawaban_teks');
            if (!$timerExpired && ($jawabanTeks === null || $jawabanTeks === '')) {
                return redirect()->route('peserta.tes.kerjakan', $sesiId)->with('error', 'Silakan isi jawaban terlebih dahulu.');
            }
            if ($jawabanTeks !== null && $jawabanTeks !== '') {
                JawabanPeserta::updateOrCreate(
                    ['user_id' => auth()->id(), 'sesi_tes_id' => $sesiId, 'soal_id' => $soalId],
                    ['jawaban_teks' => $jawabanTeks, 'opsi_dipilih_id' => null, 'waktu_jawab' => now()]
                );
            }
        } else {
            $opsiId = $request->input('opsi_id');
            if (!$timerExpired && ($opsiId === null || $opsiId === '')) {
                return redirect()->route('peserta.tes.kerjakan', $sesiId)->with('error', 'Silakan pilih jawaban terlebih dahulu.');
            }
            if ($opsiId !== null && $opsiId !== '') {
                JawabanPeserta::updateOrCreate(
                    ['user_id' => auth()->id(), 'sesi_tes_id' => $sesiId, 'soal_id' => $soalId],
                    ['opsi_dipilih_id' => (int) $opsiId, 'jawaban_teks' => null, 'waktu_jawab' => now()]
                );
            }
        }

        // Pindah ke soal berikutnya (session hanya untuk navigasi)
        $nextStep = $currentStep + 1;
        if ($nextStep >= count($daftarSoalFlat)) {
            // Reset step ke 0 agar kerjakan() bisa detect alat tes berikutnya
            Session::put($this->sessionKey($sesiId, 'current_step'), 0);
            return redirect()->route('peserta.tes.kerjakan', $sesiId);
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

        $soalDummy     = $this->getSoalPeserta();
        $daftarAlatTes = $sesi['daftar_alat_tes_ditugaskan'];
        $userId        = auth()->id();

        // Muat alat_tes_id untuk setiap kode alat tes yang ditugaskan
        $pesertaSesiTesId = $sesi['peserta_sesi_tes_id'];
        $alatTesByKode = PesertaAlatTes::where('peserta_sesi_tes_id', $pesertaSesiTesId)
            ->with('alatTes:id,kode')
            ->get()
            ->pluck('alatTes')
            ->filter()
            ->keyBy('kode')
            ->map(fn ($a) => $a->id);

        $skorHasil = [];

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
                $jenisKelamin = Auth::user()->jenis_kelamin ?? null;
                $kelompokSegmen = match ($jenisKelamin) {
                    'L' => '3',
                    'P' => '4',
                    default => '3',
                };
            } elseif ($kodeAlat === 'IST') {
                $user = Auth::user();
                $pendidikan = $user->profilKaryawan?->pendidikan_terakhir
                    ?? $user->profilKandidat?->pendidikan_terakhir
                    ?? 'SLTA';
                $kelompokSegmen = match (strtoupper($pendidikan)) {
                    'SD', 'SMP', 'SLTP' => 'SLTP',
                    'S1', 'S2', 'S3', 'D3', 'D4', 'SARJANA', 'SARJ' => 'SARJ',
                    default => 'SLTA',
                };
            } else {
                $kelompokSegmen = 'default'; // akan diupdate saat norma lengkap
            }

            $skorHasil[$kodeAlat] = $this->scoringService->dispatch(
                $userId,
                $sesiId,
                $alatTesId,
                $kelompokSegmen
            );
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

    public function kerjakanGrid(Request $request, int $sesiId)
    {
        $sesi = $this->getSesiById($sesiId);
        if (!$sesi) {
            return redirect()->route('peserta.dashboard')->with('error', 'Sesi tes tidak ditemukan.');
        }

        if (!Session::get($this->sessionKey($sesiId, 'kraepelin_panduan_selesai'))) {
            return redirect()->route('peserta.tes.panduan-kraepelin', $sesiId);
        }

        $soalData = $this->getSoalPeserta();

        $alatTesGrid = null;
        $kodeGrid = null;
        foreach ($sesi['daftar_alat_tes_ditugaskan'] as $kode) {
            if (isset($soalData[$kode]) &&
                ($soalData[$kode]['format_dasar'] === 'Grid' ||
                 ($soalData[$kode]['pola_skoring'] ?? '') === 'grid')) {
                $alatTesGrid = $soalData[$kode];
                $kodeGrid = $kode;
                break;
            }
        }

        if (!$alatTesGrid) {
            return redirect()->route('peserta.tes.kerjakan', $sesiId)
                ->with('error', 'Tidak ada soal Kraepelin di sesi ini.');
        }

        $alatTesId = \App\Models\AlatTes::where('kode', $kodeGrid)->value('id');

        // Persistent countdown: set waktu_mulai saat pertama kali masuk
        $pesertaSesi = PesertaSesiTes::where('user_id', auth()->id())
            ->where('sesi_tes_id', $sesiId)
            ->first();

        if ($pesertaSesi && !$pesertaSesi->waktu_mulai) {
            $pesertaSesi->update([
                'waktu_mulai'         => now(),
                'tanggal_pengerjaan'  => now()->toDateString(),
            ]);
            $pesertaSesi->refresh();
        }

        // Hitung sisa waktu dari durasi alat tes
        $alatTesModel = \App\Models\AlatTes::find($alatTesId);
        $durasiDetik  = ($alatTesModel?->durasi_total_menit ?? 60) * 60;
        $detikBerjalan = (int) $pesertaSesi?->waktu_mulai->diffInSeconds(now());
        $sisaDetik = (int) max(0, $durasiDetik - $detikBerjalan);

        // Kalau waktu habis, langsung selesai
        if ($sisaDetik <= 0) {
            return redirect()->route('peserta.tes.selesai', $sesiId);
        }

        $sudahDikerjakan = \App\Models\GridInputPeserta::where('user_id', auth()->id())
            ->where('sesi_tes_id', $sesiId)
            ->where('alat_tes_id', $alatTesId)
            ->select('kolom_ke')
            ->distinct()
            ->pluck('kolom_ke')
            ->toArray();

        $semuaKolom = collect($alatTesGrid['soal'])->sortBy('nomor')->values();
        $kolomAktif = $semuaKolom->firstWhere(
            fn($k) => !in_array($k['nomor'], $sudahDikerjakan)
        );

        if (!$kolomAktif) {
            return redirect()->route('peserta.tes.selesai', $sesiId);
        }

        $adaTimerPerKolom = $alatTesModel?->batas_waktu_per_soal_aktif ?? false;
        $detikPerKolom    = $alatTesModel?->batas_waktu_per_soal_detik ?? 60;

        // Persistensi timer per-kolom
        $sisaDetikPerKolom = $detikPerKolom;
        if ($adaTimerPerKolom) {
            $pesertaAlatTes = PesertaAlatTes::where('peserta_sesi_tes_id', $pesertaSesi->id)
                ->where('alat_tes_id', $alatTesId)
                ->first();

            $kolomAktifNomor = (int) $kolomAktif['nomor'];

            if (!$pesertaAlatTes) {
                // Kasus 1: record tidak ada → create baru
                $pesertaAlatTes = PesertaAlatTes::create([
                    'peserta_sesi_tes_id' => $pesertaSesi->id,
                    'alat_tes_id'         => $alatTesId,
                    'waktu_mulai_kolom'   => now(),
                    'kolom_ke'            => $kolomAktifNomor,
                ]);
                $sisaDetikPerKolom = $detikPerKolom;
            } elseif (!$pesertaAlatTes->kolom_ke) {
                // Kasus 2: kolom_ke null → inisialisasi
                $pesertaAlatTes->update([
                    'waktu_mulai_kolom' => now(),
                    'kolom_ke'          => $kolomAktifNomor,
                ]);
                $sisaDetikPerKolom = $detikPerKolom;
            } elseif ($pesertaAlatTes->kolom_ke == $kolomAktifNomor) {
                if (!$pesertaAlatTes->waktu_mulai_kolom) {
                    $pesertaAlatTes->update(['waktu_mulai_kolom' => now()]);
                    $sisaDetikPerKolom = $detikPerKolom;
                } else {
                    $sisaDetikPerKolom = (int) max(0, $detikPerKolom - $pesertaAlatTes->waktu_mulai_kolom->diffInSeconds(now()));
                    if ($sisaDetikPerKolom <= 0) {
                        // Kasus 4: waktu habis → simpan jawaban kosong, redirect ke kolom berikutnya
                        $sudahAda = \App\Models\GridInputPeserta::where('user_id', auth()->id())
                            ->where('sesi_tes_id', $sesiId)
                            ->where('alat_tes_id', $alatTesId)
                            ->where('kolom_ke', $kolomAktifNomor)
                            ->exists();

                        if (!$sudahAda) {
                            $barisPertama = $kolomAktif['angka'][0] ?? null;
                            if ($barisPertama !== null) {
                                \App\Models\GridInputPeserta::create([
                                    'user_id'          => auth()->id(),
                                    'sesi_tes_id'      => $sesiId,
                                    'alat_tes_id'      => $alatTesId,
                                    'kolom_ke'         => $kolomAktifNomor,
                                    'baris_ke'         => 1,
                                    'jawaban_peserta'  => 0,
                                    'jawaban_benar'    => 0,
                                    'is_benar'         => false,
                                    'waktu_input'      => now(),
                                ]);
                            }
                        }

                        $pesertaAlatTes->update([
                            'waktu_mulai_kolom' => now(),
                        ]);

                        return redirect()->route('peserta.tes.kraepelin', $sesiId);
                    }
                }
            } else {
                // Kasus 5: kolom_ke != nomor aktif → reset ke kolom baru
                $pesertaAlatTes->update([
                    'waktu_mulai_kolom' => now(),
                    'kolom_ke'          => $kolomAktifNomor,
                ]);
                $sisaDetikPerKolom = $detikPerKolom;
            }
        }

        return view('peserta.kraepelin', [
            'sesiId'                 => $sesiId,
            'nama_sesi'              => $sesi['nama_sesi'],
            'kodeGrid'               => $kodeGrid,
            'alatTesId'              => $alatTesId,
            'kolomAktif'             => $kolomAktif,
            'totalKolom'             => $semuaKolom->count(),
            'kolomSelesai'           => count($sudahDikerjakan),
            'adaTimerPerKolom'       => $adaTimerPerKolom,
            'detikPerKolom'          => $detikPerKolom,
            'sisaDetikPerKolom'      => $sisaDetikPerKolom,
            'sisaDetikKeseluruhan'  => $sisaDetik,
            'angka'                  => $kolomAktif['angka'],
        ]);
    }

    public function simpanKolomGrid(Request $request, int $sesiId)
    {
        $request->validate([
            'alat_tes_id' => 'required|integer|exists:alat_tes,id',
            'kolom_ke'    => 'required|integer|min:1',
            'jawaban'     => 'required|array',
            'jawaban.*'   => 'nullable|integer|min:0|max:9',
            'angka'       => 'required|array',
        ]);

        $userId = auth()->id();
        $alatTesId = $request->input('alat_tes_id');
        $kolomKe = $request->input('kolom_ke');
        $jawaban = $request->input('jawaban');
        $angka = $request->input('angka');

        foreach ($jawaban as $barisKe => $jawabanPeserta) {
            if ($jawabanPeserta === null || $jawabanPeserta === '') continue;

            $i = (int) $barisKe;
            $angkaBawah = (int) ($angka[$i] ?? 0);
            $angkaAtas  = (int) ($angka[$i + 1] ?? 0);
            $benar = ($angkaBawah + $angkaAtas) % 10;
            $isBenar = ((int) $jawabanPeserta) === $benar;

            \App\Models\GridInputPeserta::updateOrCreate(
                [
                    'user_id'     => $userId,
                    'sesi_tes_id' => $sesiId,
                    'alat_tes_id' => $alatTesId,
                    'kolom_ke'    => $kolomKe,
                    'baris_ke'    => $i + 1,
                ],
                [
                    'jawaban_peserta' => (int) $jawabanPeserta,
                    'jawaban_benar'   => $benar,
                    'is_benar'        => $isBenar,
                    'waktu_input'     => now(),
                ]
            );
        }

        HasilKolomGrid::updateOrCreate(
            [
                'user_id'     => $userId,
                'sesi_tes_id' => $sesiId,
                'alat_tes_id' => $alatTesId,
                'kolom_ke'    => $kolomKe,
            ],
            [
                'jumlah_benar'    => GridInputPeserta::where('user_id', $userId)
                    ->where('sesi_tes_id', $sesiId)
                    ->where('alat_tes_id', $alatTesId)
                    ->where('kolom_ke', $kolomKe)
                    ->where('is_benar', true)
                    ->count(),
                'jumlah_salah'    => GridInputPeserta::where('user_id', $userId)
                    ->where('sesi_tes_id', $sesiId)
                    ->where('alat_tes_id', $alatTesId)
                    ->where('kolom_ke', $kolomKe)
                    ->where('is_benar', false)
                    ->where('jawaban_peserta', '!=', 0)
                    ->count(),
                'jumlah_kelewat'  => GridInputPeserta::where('user_id', $userId)
                    ->where('sesi_tes_id', $sesiId)
                    ->where('alat_tes_id', $alatTesId)
                    ->where('kolom_ke', $kolomKe)
                    ->where(function ($q) {
                        $q->whereNull('jawaban_peserta')
                          ->orWhere('jawaban_peserta', 0);
                    })
                    ->count(),
                'waktu_pakai_detik' => (int) max(0, now()->diffInSeconds(
                    GridInputPeserta::where('user_id', $userId)
                        ->where('sesi_tes_id', $sesiId)
                        ->where('alat_tes_id', $alatTesId)
                        ->where('kolom_ke', $kolomKe)
                        ->min('waktu_input')
                        ?? now()
                )),
            ]
        );

        return redirect()->route('peserta.tes.kraepelin', $sesiId);
    }
}

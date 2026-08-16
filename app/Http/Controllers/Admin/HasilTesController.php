<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BidangLaporan;
use App\Models\DimensiAlatTes;
use App\Models\DimensiBidangLaporan;
use App\Models\HasilKolomGrid;
use App\Models\HasilSkorPeserta;
use App\Models\PesertaSesiTes;
use App\Models\SesiTes;
use App\Services\FormatHasilEppsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HasilTesController extends Controller
{
    public function index(Request $request): View
    {
        $selectedSesiId = $request->integer('sesi');

        $sesiList = SesiTes::with([
            'departemenTerkait',
            'alatTes',
            'pesertaSesiTesRecords.user.profilKaryawan.dataKaryawan',
            'pesertaSesiTesRecords.user.profilKandidat',
            'pesertaSesiTesRecords.alatTes',
        ])
            ->orderByDesc('tanggal_mulai')
            ->get();

        $today = now()->toDateString();
        foreach ($sesiList as $sesi) {
            if ($sesi->status === 'Draft') {
                $sesi->status_display = 'Draft';
            } elseif ($sesi->status === 'Selesai') {
                $sesi->status_display = 'Selesai';
            } elseif ($today > $sesi->tanggal_selesai?->toDateString()) {
                $sesi->status_display = 'Kedaluwarsa';
            } elseif ($today >= $sesi->tanggal_mulai?->toDateString()) {
                $sesi->status_display = 'Aktif';
            } else {
                $sesi->status_display = 'Belum Dimulai';
            }
        }

        $penjadwalan = $sesiList->map(fn($sesi) => [
            'id'                 => $sesi->id,
            'nama_sesi'          => $sesi->nama_sesi,
            'status'             => $sesi->status,
            'status_display'     => $sesi->status_display,
            'departemen_terkait' => $sesi->departemenTerkait?->nama_departemen ?? '—',
            'tanggal_mulai'      => $sesi->tanggal_mulai,
            'tanggal_selesai'    => $sesi->tanggal_selesai,
            'jumlah_peserta'     => $sesi->jumlah_peserta,
        ])->all();

        $hasilPeserta = [];
        foreach ($sesiList as $sesi) {
            if ($sesi->pesertaSesiTesRecords->isNotEmpty()) {
                $hasilPeserta[$sesi->id] = true;
            }
        }
        if (!$selectedSesiId && $sesiList->isNotEmpty()) {
            $selectedSesiId = $sesiList->first(fn($s) => ($hasilPeserta[$s->id] ?? false))?->id ?? $sesiList->first()->id;
        }

        $hasilTes = [];
        foreach ($sesiList as $sesi) {
            foreach ($sesi->pesertaSesiTesRecords as $peserta) {
                $user = $peserta->user;
                if (!$user) {
                    continue;
                }

                $departemen = '—';
                $posisi = '—';
                $karyawan = $user->profilKaryawan;
                if ($karyawan && $karyawan->dataKaryawan) {
                    $departemen = $karyawan->dataKaryawan->departemen?->nama_departemen ?? ($karyawan->departemen ?? '—');
                    $posisi = $karyawan->dataKaryawan->posisi?->nama_posisi ?? '—';
                } else {
                    $kandidat = $user->profilKandidat;
                    if ($kandidat) {
                        $departemen = $kandidat->departemen ?? '—';
                        $posisi = $kandidat->posisi_dilamar?->nama_posisi ?? '—';
                    }
                }

                $alatTesKode = $peserta->alatTes?->pluck('kode')->join(', ') ?: '—';

                $hasilTes[] = [
                    'sesi_id'            => $sesi->id,
                    'peserta_id'         => $user->id,
                    'nama_peserta'       => $user->name,
                    'departemen'         => $departemen,
                    'posisi'             => $posisi,
                    'jenis_peserta'      => ucfirst($user->tipe_akun ?? '—'),
                    'status_pengerjaan'  => $peserta->status_pengerjaan,
                    'tanggal_pengerjaan' => $peserta->tanggal_pengerjaan,
                    'hasil_alat_tes'     => [],
                    'alat_tes_kode'      => $alatTesKode,
                ];
            }
        }

        return view('admin.hasil-tes.index', compact('hasilTes', 'penjadwalan', 'selectedSesiId'));
    }

    public function detail(int $sesiId, int $pesertaId): View
    {
        $sesi = SesiTes::with('departemenTerkait')->findOrFail($sesiId);
        $user = \App\Models\User::findOrFail($pesertaId);

        $pesertaSesi = PesertaSesiTes::where('user_id', $pesertaId)
            ->where('sesi_tes_id', $sesiId)
            ->first();

        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();
        $bisaLihatSensitif = $currentUser->hasIzin('hasil_tes.lihat_sensitif');

        $hasilTes = [
            'nama_peserta'       => $user->name,
            'no_peserta'         => 'HT-' . $sesiId . '-' . str_pad((string) $pesertaId, 3, '0', STR_PAD_LEFT),
            'jenis_peserta'      => ucfirst($user->tipe_akun ?? '—'),
            'departemen'         => '—',
            'posisi'             => '—',
            'tanggal_pengerjaan' => $pesertaSesi?->tanggal_pengerjaan,
            'catatan_hr'         => $pesertaSesi?->catatan_hr,
            'peserta_id'         => $pesertaId,
            'hasil_alat_tes'     => [],
        ];

        $hasilSkor = HasilSkorPeserta::where('user_id', $pesertaId)
            ->where('sesi_tes_id', $sesiId)
            ->with(['dimensi.alatTes', 'alatTes'])
            ->get();

        $grouped = $hasilSkor->groupBy(fn($h) =>
            $h->alatTes?->id ?? $h->dimensi?->alatTes?->id
        );

        $hasilAlatTes = [];
        foreach ($grouped as $alatTesId => $skorList) {
            $firstRow = $skorList->first();
            $alatTes = $firstRow->alatTes
                ?? $firstRow->dimensi?->alatTes;

            if (!$alatTes) continue;

            $skorRingkas = $skorList->map(fn($h) => [
                'dimensi'          => ($h->dimensi?->kode_dimensi ?? '')
                    . ' - '
                    . ($h->dimensi?->nama_dimensi ?? '—'),
                'skor_mentah'  => $h->skor_mentah,
                'skor_skala'   => $h->skor_akhir,
                'skor_persentil' => $h->skor_akhir,
                'kategori'     => $h->level?->label ?? '—',
            ])->values()->all();

            $hasilAlatTes[] = [
                'nama_alat_tes'            => $alatTes->nama,
                'format_dasar'             => $alatTes->format_dasar,
                'durasi_pengerjaan_aktual' => '—',
                'skor_ringkas'             => $skorRingkas,
            ];

            if ($alatTes->format_dasar === 'Grid') {
                $gridRows = HasilKolomGrid::where('user_id', $pesertaId)
                    ->where('sesi_tes_id', $sesiId)
                    ->where('alat_tes_id', $alatTesId)
                    ->selectRaw('
                        COALESCE(SUM(jumlah_benar),0) as total_benar,
                        COALESCE(SUM(jumlah_salah),0) as total_salah,
                        COALESCE(SUM(jumlah_kelewat),0) as total_kelewat,
                        COUNT(kolom_ke) as total_kolom
                    ')->first();
                $hasilAlatTes[count($hasilAlatTes) - 1]['grid_ringkasan'] = $gridRows ?? null;
            }
        }

        $hasilTes['hasil_alat_tes'] = $hasilAlatTes;

        $hasilTes['hasil_alat_tes'] = $this->injectHasilEpps(
            $hasilTes['hasil_alat_tes'],
            $pesertaId,
            $sesiId
        );

        $psikogram = $this->hitungPsikogram($hasilTes['hasil_alat_tes']);

        return view('admin.hasil-tes.detail', compact(
            'hasilTes', 'sesi', 'psikogram', 'bisaLihatSensitif'
        ));
    }

    public function exportPdf(int $sesiId, int $pesertaId)
    {
        $sesi = SesiTes::with('departemenTerkait')->findOrFail($sesiId);
        $user = \App\Models\User::findOrFail($pesertaId);

        $pesertaSesi = PesertaSesiTes::where('user_id', $pesertaId)
            ->where('sesi_tes_id', $sesiId)
            ->first();

        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();
        $bisaLihatSensitif = $currentUser->hasIzin('hasil_tes.lihat_sensitif');

        $hasilTes = [
            'nama_peserta'       => $user->name,
            'no_peserta'         => 'HT-' . $sesiId . '-' . str_pad((string) $pesertaId, 3, '0', STR_PAD_LEFT),
            'jenis_peserta'      => ucfirst($user->tipe_akun ?? '—'),
            'departemen'         => '—',
            'posisi'             => '—',
            'peserta_id'         => $pesertaId,
            'tanggal_pengerjaan' => $pesertaSesi?->tanggal_pengerjaan,
            'catatan_hr'         => $pesertaSesi?->catatan_hr,
            'hasil_alat_tes'     => [],
        ];

        $hasilSkor = HasilSkorPeserta::where('user_id', $pesertaId)
            ->where('sesi_tes_id', $sesiId)
            ->with(['dimensi.alatTes', 'alatTes', 'level'])
            ->get();

        $grouped = $hasilSkor->groupBy(fn($h) =>
            $h->alatTes?->id ?? $h->dimensi?->alatTes?->id
        );

        $hasilAlatTes = [];
        foreach ($grouped as $alatTesId => $skorList) {
            $firstRow = $skorList->first();
            $alatTes = $firstRow->alatTes ?? $firstRow->dimensi?->alatTes;
            if (!$alatTes) continue;

            $skorRingkas = $skorList->map(fn($h) => [
                'dimensi'        => ($h->dimensi?->kode_dimensi ?? '') . ' - ' . ($h->dimensi?->nama_dimensi ?? '—'),
                'skor_mentah'    => $h->skor_mentah,
                'skor_skala'     => $h->skor_akhir,
                'skor_persentil' => $h->skor_akhir,
                'kategori'       => $h->level?->label ?? '—',
            ])->values()->all();

            $hasilAlatTes[] = [
                'nama_alat_tes'            => $alatTes->nama,
                'format_dasar'             => $alatTes->format_dasar,
                'durasi_pengerjaan_aktual' => '—',
                'skor_ringkas'             => $skorRingkas,
            ];

            if ($alatTes->format_dasar === 'Grid') {
                $gridRows = HasilKolomGrid::where('user_id', $pesertaId)
                    ->where('sesi_tes_id', $sesiId)
                    ->where('alat_tes_id', $alatTesId)
                    ->selectRaw('
                        COALESCE(SUM(jumlah_benar),0) as total_benar,
                        COALESCE(SUM(jumlah_salah),0) as total_salah,
                        COALESCE(SUM(jumlah_kelewat),0) as total_kelewat,
                        COUNT(kolom_ke) as total_kolom
                    ')->first();
                $hasilAlatTes[count($hasilAlatTes) - 1]['grid_ringkasan'] = $gridRows ?? null;
            }
        }

        $hasilTes['hasil_alat_tes'] = $hasilAlatTes;
        $hasilTes['hasil_alat_tes'] = $this->injectHasilEpps(
            $hasilTes['hasil_alat_tes'], $pesertaId, $sesiId
        );
        $psikogram = $this->hitungPsikogram($hasilTes['hasil_alat_tes']);

        $pdf = Pdf::loadView('admin.hasil-tes.pdf', compact(
            'hasilTes', 'sesi', 'psikogram', 'bisaLihatSensitif'
        ));
        $pdf->setPaper('A4', 'portrait');
        $dompdf = $pdf->getDomPDF();
        $dompdf->set_option('defaultFont', 'DejaVu Sans');
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('chroot', public_path());

        $namaFile = 'psikogram-' . str_replace(' ', '-', strtolower($user->name))
            . '-' . $sesiId . '.pdf';

        return $pdf->download($namaFile);
    }

    public function printView(int $sesiId, int $pesertaId)
    {
        $sesi = SesiTes::with('departemenTerkait')->findOrFail($sesiId);
        $user = \App\Models\User::findOrFail($pesertaId);

        $pesertaSesi = PesertaSesiTes::where('user_id', $pesertaId)
            ->where('sesi_tes_id', $sesiId)
            ->first();

        $currentUser = auth()->user();
        $bisaLihatSensitif = $currentUser->hasIzin('hasil_tes.lihat_sensitif');

        $hasilTes = [
            'nama_peserta'       => $user->name,
            'no_peserta'         => 'HT-' . $sesiId . '-' . str_pad((string) $pesertaId, 3, '0', STR_PAD_LEFT),
            'jenis_peserta'      => ucfirst($user->tipe_akun ?? '—'),
            'departemen'         => '—',
            'posisi'             => '—',
            'peserta_id'         => $pesertaId,
            'tanggal_pengerjaan' => $pesertaSesi?->tanggal_pengerjaan,
            'catatan_hr'         => $pesertaSesi?->catatan_hr,
            'hasil_alat_tes'     => [],
        ];

        $hasilSkor = HasilSkorPeserta::where('user_id', $pesertaId)
            ->where('sesi_tes_id', $sesiId)
            ->with(['dimensi.alatTes', 'alatTes', 'level'])
            ->get();

        $grouped = $hasilSkor->groupBy(fn($h) =>
            $h->alatTes?->id ?? $h->dimensi?->alatTes?->id
        );

        $hasilAlatTes = [];
        foreach ($grouped as $alatTesId => $skorList) {
            $firstRow = $skorList->first();
            $alatTes = $firstRow->alatTes ?? $firstRow->dimensi?->alatTes;
            if (!$alatTes) continue;

            $skorRingkas = $skorList->map(fn($h) => [
                'dimensi'        => ($h->dimensi?->kode_dimensi ?? '') . ' - ' . ($h->dimensi?->nama_dimensi ?? '—'),
                'skor_mentah'    => $h->skor_mentah,
                'skor_skala'     => $h->skor_akhir,
                'skor_persentil' => $h->skor_akhir,
                'kategori'       => $h->level?->label ?? '—',
            ])->values()->all();

            $hasilAlatTes[] = [
                'nama_alat_tes'            => $alatTes->nama,
                'format_dasar'             => $alatTes->format_dasar,
                'durasi_pengerjaan_aktual' => '—',
                'skor_ringkas'             => $skorRingkas,
            ];

            if ($alatTes->format_dasar === 'Grid') {
                $gridRows = HasilKolomGrid::where('user_id', $pesertaId)
                    ->where('sesi_tes_id', $sesiId)
                    ->where('alat_tes_id', $alatTesId)
                    ->selectRaw('
                        COALESCE(SUM(jumlah_benar),0) as total_benar,
                        COALESCE(SUM(jumlah_salah),0) as total_salah,
                        COALESCE(SUM(jumlah_kelewat),0) as total_kelewat,
                        COUNT(kolom_ke) as total_kolom
                    ')->first();
                $hasilAlatTes[count($hasilAlatTes) - 1]['grid_ringkasan'] = $gridRows ?? null;
            }
        }

        $hasilTes['hasil_alat_tes'] = $hasilAlatTes;
        $hasilTes['hasil_alat_tes'] = $this->injectHasilEpps(
            $hasilTes['hasil_alat_tes'], $pesertaId, $sesiId
        );
        $papScores = $this->buildPapScores($hasilTes['hasil_alat_tes']);
        $psikogram = $this->hitungPsikogram($hasilTes['hasil_alat_tes']);

        return view('admin.hasil-tes.print', compact(
            'hasilTes', 'sesi', 'papScores', 'psikogram', 'bisaLihatSensitif'
        ));
    }

    public function simpanCatatan(Request $request, int $sesiId, int $pesertaId): RedirectResponse
    {
        $request->validate([
            'catatan_hr' => 'nullable|string|max:5000',
        ]);

        PesertaSesiTes::where('sesi_tes_id', $sesiId)
            ->where('user_id', $pesertaId)
            ->update(['catatan_hr' => $request->input('catatan_hr')]);

        return redirect()
            ->route('admin.hasil-tes.detail', [$sesiId, $pesertaId])
            ->with('sukses', 'Catatan HR berhasil disimpan.');
    }

    protected function injectHasilEpps(array $hasilAlatPeserta, int $userId, int $sesiId): array
    {
        $adaEpps = false;
        foreach ($hasilAlatPeserta as $alat) {
            if (($alat['nama_alat_tes'] ?? null) === 'EPPS') {
                $adaEpps = true;
                break;
            }
        }

        if (!$adaEpps) {
            return $hasilAlatPeserta;
        }

        $eppsRows = (new FormatHasilEppsService())
            ->formatHasilEppsUntukTampilan($userId, $sesiId);

        if ($eppsRows->isEmpty()) {
            return $hasilAlatPeserta;
        }

        $skorRingkasBaru = $eppsRows->map(function (array $row) {
            return [
                'dimensi'     => $row['dimensi'],
                'skor_mentah' => $row['skor_mentah'],
                'skor_skala'  => $row['skor_persentil'],
                'kategori'    => $row['kategori'],
            ];
        })->all();

        $hasilBaru = [];
        foreach ($hasilAlatPeserta as $alat) {
            if (($alat['nama_alat_tes'] ?? null) === 'EPPS') {
                $alat['skor_ringkas'] = $skorRingkasBaru;
            }
            $hasilBaru[] = $alat;
        }

        return $hasilBaru;
    }

    protected function buildPapScores(array $hasilAlatPeserta): array
    {
        $scores = [];
        foreach ($hasilAlatPeserta as $alat) {
            if (!str_contains(strtoupper($alat['nama_alat_tes'] ?? ''), 'PAPIKOSTIK')) continue;
            foreach ($alat['skor_ringkas'] ?? [] as $row) {
                $kode = trim(explode(' - ', $row['dimensi'] ?? '')[0] ?? '');
                if ($kode) $scores[$kode] = $row;
            }
        }
        return $scores;
    }

    protected function hitungPsikogram(array $hasilAlatPeserta): array
    {
        $bidangMap = $this->loadBidangMap();

        $psikogram = [
            'Intelektual' => [],
            'Sikap Kerja' => [],
            'Kepribadian' => [],
            'Potensi Kerja' => [],
            'Sensitif'    => [],
        ];

        foreach ($hasilAlatPeserta as $alat) {
            $namaAlat = $alat['nama_alat_tes'] ?? '';
            if (!isset($bidangMap[$namaAlat])) {
                continue;
            }

            $skorRingkas = $alat['skor_ringkas'] ?? [];
            if (!is_array($skorRingkas) || empty($skorRingkas)) {
                continue;
            }

            foreach ($bidangMap[$namaAlat] as $konfigurasi) {
                $skorItem = $this->cariSkor($skorRingkas, $konfigurasi, $namaAlat);
                if (!$skorItem) {
                    continue;
                }

                $skorNilai = $this->ambilSkorNumeric($skorItem, $konfigurasi);
                if ($skorNilai === null) {
                    continue;
                }

                $kategori = $this->hitungBerdasarkanTipe(
                    $konfigurasi['tipe_kategori'],
                    $skorNilai,
                    $konfigurasi
                );
                if ($kategori === null) {
                    continue;
                }

                $aspek = [
                    'nama_dimensi'      => $konfigurasi['nama_dimensi'],
                    'deskripsi_aspek'   => $konfigurasi['deskripsi_aspek'],
                    'kategori_hasil'    => $kategori,
                    'sumber_alat_tes'   => $namaAlat,
                ];

                if ($konfigurasi['bidang'] === 'Sensitif' && isset($skorItem['skor_t'])) {
                    $aspek['skor_t'] = $skorItem['skor_t'];
                }

                $bidang = $konfigurasi['bidang'];
                if (array_key_exists($bidang, $psikogram)) {
                    $psikogram[$bidang][] = $aspek;
                }
            }
        }

        return $psikogram;
    }

    /**
     * Muat konfigurasi bidang psikogram dari DB: untuk setiap alat tes aktif,
     * kumpulkan semua dimensinya beserta bidang_laporan dan level_threshold-nya.
     *
     * @return array ['NamaAlat' => [['nama_dimensi'=>..., 'bidang'=>..., 'tipe_kategori'=>..., 'ambang_*'=>...], ...]]
     */
    protected function loadBidangMap(): array
    {
        $alatAktif = \App\Models\AlatTes::where('is_aktif', true)
            ->whereNull('deleted_at')
            ->get(['id', 'nama']);

        $map = [];
        foreach ($alatAktif as $alat) {
            $dimensinya = DimensiAlatTes::where('alat_tes_id', $alat->id)
                ->with(['bidangLaporan.bidangLaporan', 'levelDimensi'])
                ->orderBy('urutan')
                ->get();

            if ($dimensinya->isEmpty()) {
                continue;
            }

            $map[$alat->nama] = $dimensinya->map(function (DimensiAlatTes $dim) {
                $bidang = $dim->bidangLaporan->first()?->bidangLaporan->nama ?? '';
                $tipe = $dim->tipe_kategori ?? 'psikogram';

                $thresholds = [];
                foreach ($dim->levelDimensi as $level) {
                    $label = strtolower($level->label);
                    if ($label === 'rendah')          $thresholds['ambang_r'] = (float) $level->skor_max;
                    if ($label === 'kurang')          $thresholds['ambang_k'] = (float) $level->skor_max;
                    if ($label === 'cukup')           $thresholds['ambang_c'] = (float) $level->skor_max;
                    if ($label === 'baik')            $thresholds['ambang_b'] = (float) $level->skor_max;
                    if ($label === 'baik sekali')     $thresholds['ambang_bs'] = (float) $level->skor_max;
                }

                return array_merge([
                    'nama_dimensi'    => $dim->nama_dimensi,
                    'deskripsi_aspek' => $dim->deskripsi_aspek,
                    'bidang'          => $bidang,
                    'tipe_kategori'   => $tipe,
                ], $thresholds);
            })->toArray();
        }

        return $map;
    }

    protected function cariSkor(array $skorRingkas, array $konfigurasi, string $namaAlat): ?array
    {
        foreach ($skorRingkas as $score) {
            $match = false;

            if ($namaAlat === 'EPPS' && isset($score['dimensi'])) {
                $parts = explode(' - ', trim($score['dimensi']), 2);
                $kodeDimensi = trim($parts[0] ?? '');
                $namaDimensi = trim($parts[1] ?? '');

                if (strcasecmp($kodeDimensi, $konfigurasi['kode_dimensi'] ?? '') === 0) {
                    $match = true;
                }
                if (!$match && strcasecmp($namaDimensi, $konfigurasi['nama_dimensi']) === 0) {
                    $match = true;
                }
            }

            if ($match) {
                return $score;
            }
        }

        return null;
    }

    protected function ambilSkorNumeric(array $skorItem, array $konfigurasi): ?float
    {
        if (isset($skorItem['skor_skala']))    return (float) $skorItem['skor_skala'];
        if (isset($skorItem['skor_t']))        return (float) $skorItem['skor_t'];
        if (isset($skorItem['skor_mentah']))   return (float) $skorItem['skor_mentah'];
        foreach ($skorItem as $val) {
            if (is_numeric($val)) return (float) $val;
        }
        return null;
    }

    protected function hitungBerdasarkanTipe(string $tipeKategori, float $skor, array $konfigurasi): ?string
    {
        if ($tipeKategori === 'psikogram') {
            if ($skor <= ($konfigurasi['ambang_r'] ?? PHP_INT_MAX)) return 'R';
            if ($skor <= ($konfigurasi['ambang_k'] ?? PHP_INT_MAX)) return 'K';
            if ($skor <= ($konfigurasi['ambang_c'] ?? PHP_INT_MAX)) return 'C';
            if ($skor <= ($konfigurasi['ambang_b'] ?? PHP_INT_MAX)) return 'B';
            return 'BS';
        }

        if ($tipeKategori === 'klinis') {
            $ambangNormal          = $konfigurasi['ambang_normal']          ?? PHP_INT_MAX;
            $ambangPerluPerhatian = $konfigurasi['ambang_perlu_perhatian'] ?? PHP_INT_MAX;
            if ($skor >= $ambangPerluPerhatian) return 'Signifikan';
            if ($skor >= $ambangNormal)         return 'Perlu Perhatian';
            return 'Normal';
        }

        return null;
    }
}

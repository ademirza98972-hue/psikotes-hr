<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanCatatanHasilTesRequest;
use App\Models\DimensiAlatTes;
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
    public function __construct(private FormatHasilEppsService $eppsService) {}

    public function index(Request $request): View
    {
        $selectedSesiId = $request->integer('sesi');

        $sesiList = SesiTes::with([
            'departemen',
            'alatTes',
            'pesertaSesiTesRecords' => fn ($q) => $q
                ->with(['user.dataKaryawan.departemen', 'user.dataKaryawan.posisi', 'user.profilKandidat.posisi.departemen', 'alatTes'])
                ->withCount(['logAktivitas as log_mencurigakan_count' => fn ($q) => $q->where('mencurigakan', true)]),
        ])
            ->orderByDesc('tanggal_mulai')
            ->get();

        $penjadwalan = $sesiList->map(fn($sesi) => [
            'id'                 => $sesi->id,
            'nama_sesi'          => $sesi->nama_sesi,
            'status'             => $sesi->status,
            'status_display'     => $sesi->status_display,
            'departemen_terkait' => $sesi->departemen->pluck('nama_departemen')->join(', ') ?: '—',
            'tanggal_mulai'      => $sesi->tanggal_mulai,
            'tanggal_selesai'    => $sesi->tanggal_selesai,
            'jumlah_peserta'     => $sesi->jumlah_peserta,
        ])->all();

        if (!$selectedSesiId && $sesiList->isNotEmpty()) {
            $selectedSesiId = $sesiList->first(fn($s) => $s->pesertaSesiTesRecords->isNotEmpty())?->id
                ?? $sesiList->first()->id;
        }

        $hasilTes = [];
        foreach ($sesiList as $sesi) {
            foreach ($sesi->pesertaSesiTesRecords as $peserta) {
                $user = $peserta->user;
                if (!$user) {
                    continue;
                }

                $dk = $user->dataKaryawan;
                $departemen = $dk?->departemen?->nama_departemen ?? $user->profilKandidat?->posisi?->departemen?->nama_departemen ?? '—';
                $posisi     = $dk?->posisi?->nama_posisi ?? $user->profilKandidat?->posisi?->nama_posisi ?? '—';

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
                    'alat_tes_kode'           => $alatTesKode,
                    'log_mencurigakan_count'  => $peserta->log_mencurigakan_count ?? 0,
                ];
            }
        }

        return view('admin.hasil-tes.index', compact('hasilTes', 'penjadwalan', 'selectedSesiId'));
    }

    private function buildHasilTesData(int $sesiId, int $pesertaId): array
    {
        $sesi = SesiTes::with('departemen')->findOrFail($sesiId);
        $user = \App\Models\User::findOrFail($pesertaId);

        $pesertaSesi = PesertaSesiTes::where('user_id', $pesertaId)
            ->where('sesi_tes_id', $sesiId)
            ->first();

        $currentUser = auth()->user();
        $bisaLihatSensitif = $currentUser->hasIzin('hasil_tes.lihat_sensitif');
        // Posisi dilamar (kandidat) / posisi karyawan; departemen mengikuti posisi
        $posisi = $user->profilKandidat?->posisi ?? $user->dataKaryawan?->posisi;

        $hasilTes = [
            'nama_peserta'       => $user->name,
            'no_peserta'         => 'HT-' . $sesiId . '-' . str_pad((string) $pesertaId, 3, '0', STR_PAD_LEFT),
            'jenis_peserta'      => ucfirst($user->tipe_akun ?? '—'),
            'departemen'         => $posisi?->departemen?->nama_departemen
                                    ?? $user->dataKaryawan?->departemen?->nama_departemen ?? '—',
            'posisi'             => $posisi?->nama_posisi ?? '—',
            'tujuan_tes'         => $user->tipe_akun === 'karyawan' ? 'Asesmen Karyawan' : 'Seleksi Karyawan Baru',
            'pemeriksa'          => $currentUser->name,
            'tanggal_cetak'      => now()->toDateString(),
            'tanggal_pengerjaan' => $pesertaSesi?->tanggal_pengerjaan,
            'catatan_hr'         => $pesertaSesi?->catatan_hr,
            'peserta_id'         => $pesertaId,
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
                // Kelompok norma yang dipakai saat skoring (IST: usia pada tanggal tes)
                'kelompok_norma'           => $alatTes->kode === 'IST'
                    ? app(\App\Services\ScoringEngineService::class)->kelompokSegmen(
                        $user, $alatTes, $pesertaSesi?->tanggal_pengerjaan ? \Carbon\Carbon::parse($pesertaSesi->tanggal_pengerjaan) : null)
                    : null,
            ];

            if ($alatTes->format_dasar === 'Grid') {
                $lastIndex = count($hasilAlatTes) - 1;
                $gridRows = HasilKolomGrid::where('user_id', $pesertaId)
                    ->where('sesi_tes_id', $sesiId)
                    ->where('alat_tes_id', $alatTesId)
                    ->selectRaw('
                        COALESCE(SUM(jumlah_benar),0) as total_benar,
                        COALESCE(SUM(jumlah_salah),0) as total_salah,
                        COALESCE(SUM(jumlah_kelewat),0) as total_kelewat,
                        COUNT(kolom_ke) as total_kolom
                    ')->first();
                $gridExtra = $this->buildGridExtra($pesertaId, $sesiId, $alatTesId);
                $hasilAlatTes[$lastIndex]['grid_ringkasan'] = $gridRows ?? null;
                $hasilAlatTes[$lastIndex]['grid_chart']       = $gridExtra['chart'];
                $hasilAlatTes[$lastIndex]['grid_hanker']      = $gridExtra['hanker'];
                $hasilAlatTes[$lastIndex]['grid_titikTertinggi'] = $gridExtra['titikTertinggi'];
                $hasilAlatTes[$lastIndex]['grid_titikTerendah']  = $gridExtra['titikTerendah'];
                $hasilAlatTes[$lastIndex]['grid_range']       = $gridExtra['range'];
            }
        }

        $hasilTes['hasil_alat_tes'] = $this->injectHasilEpps($hasilAlatTes, $pesertaId, $sesiId);
        $psikogram = $this->hitungPsikogram($hasilTes['hasil_alat_tes']);
        $hasilTes['ringkasan'] = $this->susunRingkasan($hasilTes['hasil_alat_tes']);

        $logMencurigakan = $pesertaSesi
            ? \App\Models\LogAktivitasTes::where('peserta_sesi_tes_id', $pesertaSesi->id)
                ->where('mencurigakan', true)
                ->orderBy('created_at')
                ->get()
            : collect();

        return compact('sesi', 'user', 'hasilTes', 'psikogram', 'bisaLihatSensitif', 'logMencurigakan');
    }

    public function detail(int $sesiId, int $pesertaId): View
    {
        extract($this->buildHasilTesData($sesiId, $pesertaId));

        return view('admin.hasil-tes.detail', compact(
            'hasilTes', 'sesi', 'psikogram', 'bisaLihatSensitif', 'logMencurigakan'
        ));
    }

    public function exportPdf(int $sesiId, int $pesertaId): \Symfony\Component\HttpFoundation\Response
    {
        extract($this->buildHasilTesData($sesiId, $pesertaId));

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

    public function printView(int $sesiId, int $pesertaId): View
    {
        extract($this->buildHasilTesData($sesiId, $pesertaId));

        return view('admin.hasil-tes.print', compact(
            'hasilTes', 'sesi', 'psikogram', 'bisaLihatSensitif'
        ));
    }

    /**
     * Data grafik & rekap Kraepelin, sama dengan rumus Kraepelin.xls / ScoringEngineService::scoreGrid:
     * x = 1..N kolom (N = jumlah kolom soal), kolom tanpa jawaban bernilai y = 0.
     */
    private function buildGridExtra(int $userId, int $sesiId, int $alatTesId): array
    {
        $perKolom = HasilKolomGrid::where('user_id', $userId)
            ->where('sesi_tes_id', $sesiId)
            ->where('alat_tes_id', $alatTesId)
            ->pluck('jumlah_benar', 'kolom_ke');

        $n = max(\App\Models\Soal::where('alat_tes_id', $alatTesId)->count(), (int) $perKolom->keys()->max());
        $y = collect(range(1, max(1, $n)))->mapWithKeys(fn ($x) => [$x => (int) ($perKolom[$x] ?? 0)]);

        $chartData = $y->map(fn ($v, $x) => ['kolom' => $x, 'benar' => $v])->values()->all();
        $titikTertinggi = ['kolom' => $y->search($y->max()), 'benar' => $y->max()];
        $titikTerendah  = ['kolom' => $y->search($y->min()), 'benar' => $y->min()];

        $sumX = $y->keys()->sum();
        $sumY = $y->sum();
        $sumXY = $y->map(fn ($v, $x) => $x * $v)->sum();
        $sumX2 = $y->keys()->map(fn ($x) => $x * $x)->sum();
        $denom = $n * $sumX2 - $sumX * $sumX;
        $b = $denom != 0 ? ($n * $sumXY - $sumX * $sumY) / $denom : 0;
        $a = $sumY / $n - $b * $sumX / $n;
        $delta = 50 * $b;

        return [
            'chart'          => $chartData,
            'hanker'         => [
                'a' => $a, 'b' => $b,
                'y0' => round($a, 2), 'y50' => round($a + 50 * $b, 2), 'delta' => round($delta, 2),
                // Kraepelin.xls: y(50) − y(0) < 0 → menurun, selain itu meningkat
                'trend' => $delta < 0 ? 'menurun' : 'meningkat',
            ],
            'range'          => $y->max() - $y->min(),
            'titikTertinggi' => $titikTertinggi,
            'titikTerendah'  => $titikTerendah,
        ];
    }

    public function simpanCatatan(SimpanCatatanHasilTesRequest $request, int $sesiId, int $pesertaId): RedirectResponse
    {

        PesertaSesiTes::where('sesi_tes_id', $sesiId)
            ->where('user_id', $pesertaId)
            ->update(['catatan_hr' => $request->input('catatan_hr')]);

        return redirect()
            ->route('admin.hasil-tes.detail', [$sesiId, $pesertaId])
            ->with($this::FLASH_SUCCESS, 'Catatan HR berhasil disimpan.');
    }

    protected function injectHasilEpps(array $hasilAlatPeserta, int $userId, int $sesiId): array
    {
        if (!collect($hasilAlatPeserta)->contains('nama_alat_tes', 'EPPS')) {
            return $hasilAlatPeserta;
        }

        $eppsRows = $this->eppsService
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

        return collect($hasilAlatPeserta)->map(fn($alat) =>
            ($alat['nama_alat_tes'] ?? null) === 'EPPS'
                ? array_merge($alat, ['skor_ringkas' => $skorRingkasBaru])
                : $alat
        )->all();
    }

    /**
     * Ringkasan evaluasi per alat tes, disusun dari skor yang benar-benar dimiliki peserta
     * (bukan teks baku). Kesimpulan akhir tetap ditulis HR/psikolog.
     */
    protected function susunRingkasan(array $hasilAlatPeserta): array
    {
        $kalimat = [];
        $daftar = fn (array $x) => count($x) > 1
            ? implode(', ', array_slice($x, 0, -1)) . ' dan ' . end($x)
            : ($x[0] ?? '');

        foreach ($hasilAlatPeserta as $alat) {
            $nama = $alat['nama_alat_tes'] ?? '';
            $skor = collect($alat['skor_ringkas'] ?? [])
                ->keyBy(fn ($s) => trim(explode(' - ', $s['dimensi'] ?? '')[0]));
            if ($skor->isEmpty()) {
                continue;
            }

            if (str_contains(strtoupper($nama), 'EPPS')) {
                // Kontrol EPPS.xls: total skor 15 kebutuhan harus 210
                $total = (int) $skor->except('con')->sum(fn ($s) => (float) $s['skor_mentah']);
                if ($total !== 210) {
                    $kalimat[] = "EPPS: hanya {$total} dari 210 soal terskor — hasil belum dapat ditafsirkan, periksa kelengkapan pengerjaan.";
                    continue;
                }
                $label = fn ($kode) => FormatHasilEppsService::NAMA_KEBUTUHAN[$kode][0] ?? $kode;
                $tinggi = $skor->filter(fn ($s, $k) => $k !== 'con' && in_array($s['kategori'], ['Tinggi', 'Sangat Tinggi']))
                    ->sortByDesc('skor_skala')->keys()->map($label)->all();
                $rendah = $skor->filter(fn ($s, $k) => $k !== 'con' && in_array($s['kategori'], ['Rendah', 'Sangat Rendah']))
                    ->sortBy('skor_skala')->keys()->map($label)->all();

                $teks = 'EPPS: ' . ($tinggi ? 'kebutuhan menonjol pada ' . $daftar($tinggi) . '. ' : 'tidak ada kebutuhan yang menonjol. ')
                    . ($rendah ? 'Kebutuhan rendah pada ' . $daftar($rendah) . '. ' : '')
                    . (count($tinggi) + count($rendah) < 15 ? 'Kebutuhan lainnya berada pada taraf sedang.' : '');
                if ($con = $skor->get('con')) {
                    $teks .= ' Konsistensi jawaban ' . (int) $con['skor_mentah'] . ' dari 15 (' . ($con['kategori'] ?? '-') . ')'
                        . (in_array($con['kategori'], ['Rendah', 'Sangat Rendah']) ? ' — hasil EPPS perlu ditafsirkan dengan hati-hati.' : '.');
                }
                $kalimat[] = $teks;
            } elseif (str_contains(strtoupper($nama), 'IST')) {
                $aspek = ['SE' => 'penalaran praktis (SE)', 'WA' => 'pemahaman kata (WA)', 'AN' => 'fleksibilitas berpikir (AN)',
                    'GE' => 'abstraksi verbal (GE)', 'RA' => 'berhitung praktis (RA)', 'ZR' => 'berpikir induktif-numerik (ZR)',
                    'FA' => 'visualisasi bentuk (FA)', 'WU' => 'kemampuan ruang (WU)', 'ME' => 'daya ingat (ME)'];
                $kat = fn ($kode) => kategori_subtes_ist((float) ($skor->get($kode)['skor_skala'] ?? 0));
                $kuat = collect($aspek)->filter(fn ($n, $k) => in_array($kat($k), ['T', 'ST']))->values()->all();
                $lemah = collect($aspek)->filter(fn ($n, $k) => in_array($kat($k), ['SR', 'R']))->values()->all();
                $iq = (float) ($skor->get('IQ')['skor_skala'] ?? 0);

                $kalimat[] = 'IST: IQ ' . number_format($iq, 0) . ' (' . klasifikasi_iq($iq) . ').'
                    . ($kuat ? ' Aspek kuat: ' . $daftar($kuat) . '.' : '')
                    . ($lemah ? ' Aspek yang perlu dikembangkan: ' . $daftar($lemah) . '.' : '');
            } elseif (($alat['format_dasar'] ?? '') === 'Grid') {
                $angka = fn ($kode) => str_replace('.', ',', rtrim(rtrim(number_format((float) ($skor[$kode]['skor_skala'] ?? 0), 2, '.', ''), '0'), '.'));
                $kat = fn ($kode) => strtolower($skor[$kode]['kategori'] ?? '-');
                $kosong = collect($alat['grid_chart'] ?? [])->where('benar', 0)->count();
                $kalimat[] = 'Kraepelin: kecepatan kerja ' . $kat('KECEPATAN') . ' (rata-rata ' . $angka('KECEPATAN') . ' benar per kolom), '
                    . 'ketelitian ' . $kat('KETELITIAN') . ' (' . $angka('KETELITIAN') . ' salah/terlampaui), '
                    . 'keajegan ' . $kat('KEAJEGAN') . ' (simpangan ' . $angka('KEAJEGAN') . '), '
                    . 'ketahanan ' . $kat('KETAHANAN') . ' (tren ' . (($alat['grid_hanker']['delta'] ?? 0) < 0 ? 'menurun' : 'meningkat') . ' ' . $angka('KETAHANAN') . ').'
                    . ($kosong ? " Terdapat {$kosong} kolom tanpa jawaban benar — perhatikan saat menafsirkan." : '');
            } elseif (str_contains(strtoupper($nama), 'PAPIKOSTIK')) {
                $kelompok = ['ARAH_KERJA', 'KEPEMIMPINAN', 'AKTIVITAS', 'PERGAULAN', 'GAYA_KERJA', 'SIFAT', 'KETAATAN'];
                $top = $skor->except($kelompok)->sortByDesc(fn ($s) => (float) $s['skor_mentah'])->take(3)
                    ->map(fn ($s) => trim(explode(' - ', $s['dimensi'])[1] ?? $s['dimensi']) . ' (' . (int) $s['skor_mentah'] . ')')
                    ->values()->all();
                // Kontrol Papi Kostick.xls: total 20 faktor harus 90 (45 atas + 45 bawah)
                $total = (int) $skor->except($kelompok)->sum(fn ($s) => (float) $s['skor_mentah']);
                $kalimat[] = $total === 90
                    ? 'PAPI Kostick: kecenderungan paling kuat pada ' . $daftar($top) . '.'
                    : "PAPI Kostick: hanya {$total} dari 90 soal terskor — hasil belum dapat ditafsirkan, periksa kelengkapan pengerjaan.";
            } elseif ($iq = $skor->get('IQ')) {
                $nilai = (float) ($iq['skor_skala'] ?? 0);
                $kalimat[] = $nama . ': IQ ' . number_format($nilai, 0) . ' (' . klasifikasi_iq($nilai) . ').';
            }
        }

        return $kalimat;
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
            ->with(['dimensiAlatTes' => fn($q) => $q->with(['bidangLaporan.bidangLaporan', 'levelDimensi'])->orderBy('urutan')])
            ->get(['id', 'nama']);

        $map = [];
        foreach ($alatAktif as $alat) {
            $dimensinya = $alat->dimensiAlatTes;

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

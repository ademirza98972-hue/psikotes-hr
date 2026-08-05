<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FormatHasilEppsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HasilTesController extends Controller
{

    protected const ALAT_TES = [
        ['id' => 3, 'nama' => 'EPPS',  'format_dasar' => 'Forced Choice'],
    ];

    protected const PENJADWALAN = [
        [
            'id' => 1,
            'nama_sesi' => 'Rekrutmen Staff Finance Batch 1',
            'departemen_terkait' => 'Finance',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-08-07',
            'status' => 'Aktif',
            'daftar_alat_tes' => ['EPPS'],
            'jumlah_peserta' => 15,
        ],
        [
            'id' => 2,
            'nama_sesi' => 'Assessment Tahunan Karyawan Divisi Produksi',
            'departemen_terkait' => 'Produksi',
            'tanggal_mulai' => '2026-07-15',
            'tanggal_selesai' => '2026-07-30',
            'status' => 'Selesai',
            'daftar_alat_tes' => ['EPPS'],
            'jumlah_peserta' => 42,
        ],
        [
            'id' => 3,
            'nama_sesi' => 'Rekrutmen Staff HR',
            'departemen_terkait' => 'HR',
            'tanggal_mulai' => '2026-09-10',
            'tanggal_selesai' => '2026-09-12',
            'status' => 'Draft',
            'daftar_alat_tes' => ['EPPS'],
            'jumlah_peserta' => 8,
        ],
    ];

    protected const KARYAWAN = [
        ['name' => 'Budi Santoso',   'departemen' => 'Produksi','posisi' => 'Supervisor Produksi', 'foto_url' => null],
        ['name' => 'Dewi Lestari',   'departemen' => 'IT',      'posisi' => 'Analyst IT', 'foto_url' => null],
        ['name' => 'Eko Wibowo',     'departemen' => 'Marketing','posisi' => 'Staff Marketing', 'foto_url' => null],
        ['name' => 'Hesti Rahmawati','departemen' => 'HR',      'posisi' => 'HR Specialist', 'foto_url' => null],
        ['name' => 'Fitri Handayani','departemen' => 'Operasional','posisi' => 'Admin Operasional', 'foto_url' => null],
    ];

    protected const KANDIDAT = [
        ['name' => 'Rio Saputra',       'departemen' => 'Produksi', 'posisi' => 'Calon Supervisor', 'foto_url' => null],
        ['name' => 'Nurul Aini',        'departemen' => 'HR',       'posisi' => 'Calon HR Specialist', 'foto_url' => null],
        ['name' => 'Bagas Maulana',     'departemen' => 'IT',       'posisi' => 'Calon IT Analyst', 'foto_url' => null],
        ['name' => 'Citra Kirana',      'departemen' => 'Marketing','posisi' => 'Calon Staff Marketing', 'foto_url' => null],
        ['name' => 'Erna Wulandari',    'departemen' => 'Produksi', 'posisi' => 'Calon Operator', 'foto_url' => null],
        ['name' => 'Fajar Nugroho',     'departemen' => 'HR',       'posisi' => 'Calon Rekruter', 'foto_url' => null],
        ['name' => 'Gita Permata',      'departemen' => 'IT',       'posisi' => 'Calon Analis', 'foto_url' => null],
        ['name' => 'Indah Cahyani',     'departemen' => 'Finance',  'posisi' => 'Calon Staff', 'foto_url' => null],
    ];

    // Data dummy hasil tes per peserta per sesi
    protected const HASIL_TES = [
        // Sesi 1: Rekrutmen Staff Finance Batch 1 (EPPS)
        [
            'sesi_id' => 1,
            'peserta_id' => 104,
            'nama_peserta' => 'Indah Cahyani',
            'jenis_peserta' => 'Kandidat',
            'departemen' => 'Finance',
            'posisi' => 'Calon Staff',
            'status_pengerjaan' => 'Belum Mengerjakan',
            'tanggal_pengerjaan' => null,
            'hasil_alat_tes' => []
        ],

        // Sesi 2: Assessment Tahunan Produksi (EPPS)
        [
            'sesi_id' => 2,
            'peserta_id' => 201,
            'nama_peserta' => 'Budi Santoso',
            'jenis_peserta' => 'Karyawan',
            'departemen' => 'Produksi',
            'posisi' => 'Supervisor Produksi',
            'status_pengerjaan' => 'Selesai',
            'tanggal_pengerjaan' => '2026-07-28',
            'hasil_alat_tes' => [
                [
                    'nama_alat_tes' => 'EPPS',
                    'format_dasar' => 'Forced Choice',
                    'durasi_pengerjaan_aktual' => '65 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        ['dimensi' => 'Achievement',     'skor_mentah' => 78, 'skor_skala' => 8, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'Dominance',       'skor_mentah' => 72, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'Affiliation',     'skor_mentah' => 45, 'skor_skala' => 5, 'kategori' => 'Sedang'],
                        ['dimensi' => 'Exhibition',      'skor_mentah' => 58, 'skor_skala' => 6, 'kategori' => 'Sedang'],
                    ]
                ],
            ]
        ],
        [
            'sesi_id' => 2,
            'peserta_id' => 202,
            'nama_peserta' => 'Rio Saputra',
            'jenis_peserta' => 'Kandidat',
            'departemen' => 'Produksi',
            'posisi' => 'Calon Supervisor',
            'status_pengerjaan' => 'Selesai',
            'tanggal_pengerjaan' => '2026-07-29',
            'hasil_alat_tes' => [
                [
                    'nama_alat_tes' => 'EPPS',
                    'format_dasar' => 'Forced Choice',
                    'durasi_pengerjaan_aktual' => '60 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        ['dimensi' => 'Achievement',     'skor_mentah' => 65, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'Dominance',       'skor_mentah' => 68, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'Affiliation',     'skor_mentah' => 52, 'skor_skala' => 6, 'kategori' => 'Sedang'],
                        ['dimensi' => 'Exhibition',      'skor_mentah' => 45, 'skor_skala' => 5, 'kategori' => 'Sedang'],
                    ]
                ]
            ]
        ],
        [
            'sesi_id' => 2,
            'peserta_id' => 203,
            'nama_peserta' => 'Erna Wulandari',
            'jenis_peserta' => 'Kandidat',
            'departemen' => 'Produksi',
            'posisi' => 'Calon Operator',
            'status_pengerjaan' => 'Selesai',
            'tanggal_pengerjaan' => '2026-07-27',
            'hasil_alat_tes' => [
                [
                    'nama_alat_tes' => 'EPPS',
                    'format_dasar' => 'Forced Choice',
                    'durasi_pengerjaan_aktual' => '55 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        ['dimensi' => 'Achievement',     'skor_mentah' => 54, 'skor_skala' => 6, 'kategori' => 'Sedang'],
                        ['dimensi' => 'Dominance',       'skor_mentah' => 38, 'skor_skala' => 4, 'kategori' => 'Rendah'],
                        ['dimensi' => 'Affiliation',     'skor_mentah' => 71, 'skor_skala' => 8, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'Exhibition',      'skor_mentah' => 39, 'skor_skala' => 4, 'kategori' => 'Rendah'],
                    ]
                ]
            ]
        ],
        // Additional participants for Session 2 (more test data)
        [
            'sesi_id' => 2,
            'peserta_id' => 204,
            'nama_peserta' => 'Agus Suryanto',
            'jenis_peserta' => 'Karyawan',
            'departemen' => 'Produksi',
            'posisi' => 'Operator Mesin',
            'status_pengerjaan' => 'Selesai',
            'tanggal_pengerjaan' => '2026-07-26',
            'hasil_alat_tes' => [
                [
                    'nama_alat_tes' => 'EPPS',
                    'format_dasar' => 'Forced Choice',
                    'durasi_pengerjaan_aktual' => '52 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        ['dimensi' => 'Achievement',     'skor_mentah' => 61, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'Dominance',       'skor_mentah' => 58, 'skor_skala' => 6, 'kategori' => 'Sedang'],
                        ['dimensi' => 'Affiliation',     'skor_mentah' => 65, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'Exhibition',      'skor_mentah' => 42, 'skor_skala' => 5, 'kategori' => 'Sedang'],
                    ]
                ]
            ]
        ],
        [
            'sesi_id' => 2,
            'peserta_id' => 205,
            'nama_peserta' => 'Siti Rahmah',
            'jenis_peserta' => 'Karyawan',
            'departemen' => 'Produksi',
            'posisi' => 'Quality Control',
            'status_pengerjaan' => 'Selesai',
            'tanggal_pengerjaan' => '2026-07-25',
            'hasil_alat_tes' => [
                [
                    'nama_alat_tes' => 'EPPS',
                    'format_dasar' => 'Forced Choice',
                    'durasi_pengerjaan_aktual' => '58 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        ['dimensi' => 'Achievement',     'skor_mentah' => 69, 'skor_skala' => 8, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'Dominance',       'skor_mentah' => 51, 'skor_skala' => 6, 'kategori' => 'Sedang'],
                        ['dimensi' => 'Affiliation',     'skor_mentah' => 72, 'skor_skala' => 8, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'Exhibition',      'skor_mentah' => 48, 'skor_skala' => 5, 'kategori' => 'Sedang'],
                    ]
                ]
            ]
        ],
        [
            'sesi_id' => 2,
            'peserta_id' => 206,
            'nama_peserta' => 'Tono Wijaya',
            'jenis_peserta' => 'Karyawan',
            'departemen' => 'Produksi',
            'posisi' => 'Asisten Supervisor',
            'status_pengerjaan' => 'Sedang Berjalan',
            'tanggal_pengerjaan' => '2026-07-30',
            'hasil_alat_tes' => [
                [
                    'nama_alat_tes' => 'EPPS',
                    'format_dasar' => 'Forced Choice',
                    'durasi_pengerjaan_aktual' => null,
                    'is_sensitif' => false,
                    'skor_ringkas' => null // belum mulai
                ]
            ]
        ],
        [
            'sesi_id' => 2,
            'peserta_id' => 207,
            'nama_peserta' => 'Rima Kusuma',
            'jenis_peserta' => 'Karyawan',
            'departemen' => 'Produksi',
            'posisi' => 'Operator Line',
            'status_pengerjaan' => 'Belum Mengerjakan',
            'tanggal_pengerjaan' => null,
            'hasil_alat_tes' => []
        ],

        // Sesi 3: Rekrutmen HR (EPPS saja)
        [
            'sesi_id' => 3,
            'peserta_id' => 301,
            'nama_peserta' => 'Nurul Aini',
            'jenis_peserta' => 'Kandidat',
            'departemen' => 'HR',
            'posisi' => 'Calon HR Specialist',
            'status_pengerjaan' => 'Belum Mengerjakan',
            'tanggal_pengerjaan' => null,
            'hasil_alat_tes' => []
        ],
        [
            'sesi_id' => 3,
            'peserta_id' => 302,
            'nama_peserta' => 'Hesti Rahmawati',
            'jenis_peserta' => 'Karyawan',
            'departemen' => 'HR',
            'posisi' => 'HR Specialist',
            'status_pengerjaan' => 'Selesai',
            'tanggal_pengerjaan' => '2026-09-05',
            'hasil_alat_tes' => [
                [
                    'nama_alat_tes' => 'EPPS',
                    'format_dasar' => 'Forced Choice',
                    'durasi_pengerjaan_aktual' => '58 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        ['dimensi' => 'Achievement',     'skor_mentah' => 71, 'skor_skala' => 8, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'Dominance',       'skor_mentah' => 52, 'skor_skala' => 6, 'kategori' => 'Sedang'],
                        ['dimensi' => 'Affiliation',     'skor_mentah' => 88, 'skor_skala' => 9, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'Exhibition',      'skor_mentah' => 65, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                    ]
                ]
            ]
        ],
        [
            'sesi_id' => 3,
            'peserta_id' => 303,
            'nama_peserta' => 'Fajar Nugroho',
            'jenis_peserta' => 'Kandidat',
            'departemen' => 'HR',
            'posisi' => 'Calon Rekruter',
            'status_pengerjaan' => 'Sedang Berjalan',
            'tanggal_pengerjaan' => '2026-09-07',
            'hasil_alat_tes' => [
                [
                    'nama_alat_tes' => 'EPPS',
                    'format_dasar' => 'Forced Choice',
                    'durasi_pengerjaan_aktual' => '35 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => null // sebagian
                ]
            ]
        ],
    ];

    public function index(): View
    {
        return view('admin.hasil-tes.index', [
            'penjadwalan' => self::PENJADWALAN,
            'hasilTes' => self::HASIL_TES,
            'karyawan' => self::KARYAWAN,
            'kandidat' => self::KANDIDAT,
        ]);
    }

    /**
     * Detail hasil tes untuk satu peserta pada satu sesi.
     *
     * Catatan routing: {pesertaId} di URL diperlakukan sebagai user_id
     * (FK ke tabel users). Saat data masih dummy, nilai peserta_id di
     * HASIL_TES belum tentu ada di tabel users, sehingga panggilan ke
     * FormatHasilEppsService akan mengembalikan koleksi kosong dan
     * halaman otomatis fallback ke skor dummy HASIL_TES.
     */
    public function detail(int $sesiId, int $pesertaId): View
    {
        $sesi = collect(self::PENJADWALAN)->where('id', $sesiId)->first();
        $hasilTes = collect(self::HASIL_TES)->where('sesi_id', $sesiId)->where('peserta_id', $pesertaId)->first();

        if (!$sesi || !$hasilTes) {
            abort(404);
        }

        // Simpan skor_ringkas dummy EPPS sebelum disuntik (dipakai untuk psikogram
        // sementara — konversi skor_skala dari DB belum tersedia).
        $dummyEppsSkorRingkas = null;
        foreach ($hasilTes['hasil_alat_tes'] as $alat) {
            if (($alat['nama_alat_tes'] ?? null) === 'EPPS' && !empty($alat['skor_ringkas'])) {
                $dummyEppsSkorRingkas = $alat['skor_ringkas'];
                break;
            }
        }

        // Suntik data EPPS nyata dari database bila tersedia.
        // PesertaId dari URL = user_id untuk pencarian skor di DB.
        if (!empty($hasilTes['hasil_alat_tes'])) {
            $hasilTes['hasil_alat_tes'] = $this->injectHasilEpps(
                $hasilTes['hasil_alat_tes'],
                $pesertaId,
                $sesiId
            );
        }

        // Cek apakah ada alat tes sensitif dan user punya izin lihat sensitif
        $bisaLihatSensitif = false;
        foreach ($hasilTes['hasil_alat_tes'] as $alat) {
            if (!empty($alat['is_sensitif']) && !empty($alat['skor_ringkas'])) {
                /** @var \App\Models\User $user */
$user = auth()->user();
$bisaLihatSensitif = $user->hasIzin('hasil_tes.lihat_sensitif');
                break;
            }
        }

        // Psikogram sementara tidak pakai skor EPPS nyata: skor_skala (1-10) yang
        // dibutuhkan hitungPsikogram belum tersedia dari FormatHasilEppsService.
        // Pemakaian skor dummy menjamin bidang psikogram tetap kosong atau
        // sesuai konfigurasi, bukan menampilkan nilai yang salah.
        $psikogramAlatTes = $hasilTes['hasil_alat_tes'];
        foreach ($psikogramAlatTes as &$alat) {
            if (($alat['nama_alat_tes'] ?? null) === 'EPPS') {
                $alat['skor_ringkas'] = $dummyEppsSkorRingkas ?? [];
            }
        }
        unset($alat);
        $psikogram = $this->hitungPsikogram($psikogramAlatTes);

        return view('admin.hasil-tes.detail', [
            'sesi' => $sesi,
            'hasilTes' => $hasilTes,
            'bisaLihatSensitif' => $bisaLihatSensitif,
            'psikogram' => $psikogram,
        ]);
    }

    /**
     * Suntik hasil EPPS nyata dari FormatHasilEppsService ke dalam array
     * hasil_alat_tes. Untuk entri dengan nama_alat_tes === 'EPPS', bila
     * service mengembalikan data, ganti bentuk 'skor_ringkas' dengan
     * data tersebut. Bila kosong (belum ada skor di DB), biarkan
     * skor_ringkas dummy tetap dipakai agar halaman tidak error.
     *
     * @param array $hasilAlatPeserta Array hasil_alat_tes milik satu peserta
     * @param int   $userId          ID user (FK ke tabel users)
     * @param int   $sesiId          ID sesi tes (FK ke sesi_tes)
     * @return array Array baru (tidak memodifikasi input)
     */
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
            return $hasilAlatPeserta; // bukan EPPS, jangan sentuh
        }

        $eppsRows = (new FormatHasilEppsService())
            ->formatHasilEppsUntukTampilan($userId, $sesiId);

        if ($eppsRows->isEmpty()) {
            return $hasilAlatPeserta; // belum ada skor di DB, fallback ke dummy
        }

        // Bentuk skor_ringkas versi service: setiap row punya
        // dimensi (format 'Kode - Nama'), skor_mentah, skor_persentil, kategori.
        // Tambah 'skor_skala' agar view & hitungPsikogram tetap kompatibel:
        // skor_skala = skor_persentil (bila dikonversi ke skala 1-10 di
        // kemudian hari, cukup ubah di sini).
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

    /**
     * Hit psikogram secara otomatis dari konfigurasi dimensi di AlatTesController
     *
     * @param array $hasilAlatPeserta Array hasil_alat_tes milik satu peserta
     * @return array Array asosiatif dengan kunci bidang_psikogram (Intelektual, Sikap Kerja, Kepribadian, Potensi Kerja, Sensitif)
     */
    protected function hitungPsikogram(array $hasilAlatPeserta): array
    {
        // Muat konfigurasi alat tes dari AlatTesController
        $allKonfigurasi = AlatTesController::DUMMY_ALAT_TES;

        // Bangun peta konversi: nama alat tes => array ['nama' => ..., 'dimensi' => [...]]
        $konfigByNama = [];
        foreach ($allKonfigurasi as $alat) {
            $konfigByNama[$alat['nama']] = [
                'dimensi' => $alat['dimensi'] ?? [],
            ];
        }

        // Hasil psikogram yang akan dikembalikan, urutan sesuai ketentuan
        $psikogram = [
            'Intelektual' => [],
            'Sikap Kerja' => [],
            'Kepribadian' => [],
            'Potensi Kerja' => [],
            'Sensitif' => [],
        ];

        foreach ($hasilAlatPeserta as $alat) {
            $namaAlat = $alat['nama_alat_tes'] ?? '';
            if (!isset($konfigByNama[$namaAlat])) {
                continue; // alat tes tidak ditemukan konfigurasinya, skip
            }

            $dimensiKonfig = $konfigByNama[$namaAlat]['dimensi'];
            $skorRingkas = $alat['skor_ringkas'] ?? [];

            if (!is_array($skorRingkas) || empty($skorRingkas)) {
                continue; // tidak ada skor, skip
            }

            foreach ($dimensiKonfig as $dim) {
                $kodeDim = $dim['kode'] ?? '';
                $namaDim = $dim['nama_dimensi'] ?? '';
                $bidang = $dim['bidang_psikogram'] ?? '';
                $deskripsi = $dim['deskripsi_aspek'] ?? '';
                $tipeKategori = $dim['tipe_kategori'] ?? 'psikogram';

                // Cari skor yang sesuai dari $skorRingkas
                $skorItem = null;

                // Matching berdasarkan cara yang berbeda tergantung jenis alat tes
                foreach ($skorRingkas as $score) {
                    $match = false;

                    if ($namaAlat === 'EPPS') {
                        // Format: dimensi seperti 'Ach - Achievement'
                        if (isset($score['dimensi'])) {
                            $dimensi = $score['dimensi'];
                            $parts = explode(' - ', trim($dimensi), 2);
                            $kodeDimensi = trim($parts[0] ?? '');
                            $namaDimensi   = trim($parts[1] ?? '');

                            // Primary: case-insensitive kode match
                            // (DB menyimpan lowercase 'ach', dummy memakai capitalized 'Ach')
                            if (strcasecmp($kodeDimensi, $kodeDim) === 0) {
                                $match = true;
                            }
                            // Fallback: case-insensitive nama match
                            // (menangani selisih ejaan seperti DB 'exh' vs dummy 'Exp')
                            if (! $match
                                && isset($dim['nama_dimensi'])
                                && strcasecmp($namaDimensi, $dim['nama_dimensi']) === 0
                            ) {
                                $match = true;
                            }
                        }
                    }

                    if ($match) {
                        $skorItem = $score;
                        break; // cukup temukan yang pertama sesuai
                    }
                }

                if (!$skorItem) {
                    continue; // skor tidak ditemukan,跳过 dimensi ini
                }

                // Tentukan skor numeric yang akan digunakan untuk kategori
                $skorNilai = $this->ambilSkorNumeric($skorItem, $dim, $namaAlat);
                if ($skorNilai === null) {
                    continue; // tidak skor numeric ditemukan, skip
                }

                // Hitung kategori
                $kategori = $this->hitungBerdasarkanTipe($tipeKategori, $skorNilai, $dim);
                if ($kategori === null) {
                    continue;
                }

                // Siapkan data aspek
                $aspek = [
                    'nama_dimensi' => $namaDim,
                    'deskripsi_aspek' => $deskripsi,
                    'kategori_hasil' => $kategori,
                    'sumber_alat_tes' => $namaAlat,
                ];

                // Untuk bidang Sensitif, tambahkan skor T jika tersedia
                if ($bidang === 'Sensitif' && isset($skorItem['skor_t'])) {
                    $aspek['skor_t'] = $skorItem['skor_t'];
                }

                // Masukkan ke dalam bidang psikogram yang sesuai
                if (array_key_exists($bidang, $psikogram)) {
                    $psikogram[$bidang][] = $aspek;
                }
            }
        }

        return $psikogram;
    }

    /**
     * Ambil nilai skor numeric dari array skor berdasarkan jenis alat tes
     */
    protected function ambilSkorNumeric(array $skorItem, array $dim, string $namaAlat): ?float
    {
        // Prioritas: skor_skala > skor_t > skor_mentah
        if (isset($skorItem['skor_skala'])) {
            return (float) $skorItem['skor_skala'];
        }
        if (isset($skorItem['skor_t'])) {
            return (float) $skorItem['skor_t'];
        }
        if (isset($skorItem['skor_mentah'])) {
            return (float) $skorItem['skor_mentah'];
        }
        // Coba ambil nilai dari kolom lain yang mungkin disebut sebagai skor (misal langsung nilai numeric)
        // Sebagai fallback, coba iterate semua value numeric
        foreach ($skorItem as $val) {
            if (is_numeric($val)) {
                return (float) $val;
            }
        }
        return null;
    }

    /**
     * Hitung kategori berdasarkan tipe kategori dan ambang batas
     */
    protected function hitungBerdasarkanTipe(string $tipeKategori, float $skor, array $dim): ?string
    {
        if ($tipeKategori === 'psikogram') {
            $ambangR = $dim['ambang_r'] ?? PHP_INT_MAX;
            $ambangK = $dim['ambang_k'] ?? PHP_INT_MAX;
            $ambangC = $dim['ambang_c'] ?? PHP_INT_MAX;
            $ambangB = $dim['ambang_b'] ?? PHP_INT_MAX;

            if ($skor <= $ambangR) return 'R';
            if ($skor <= $ambangK) return 'K';
            if ($skor <= $ambangC) return 'C';
            if ($skor <= $ambangB) return 'B';
            return 'BS';
        }

        if ($tipeKategori === 'klinis') {
            $ambangNormal = $dim['ambang_normal'] ?? PHP_INT_MAX;
            $ambangPerluPerhatian = $dim['ambang_perlu_perhatian'] ?? PHP_INT_MAX;

            if ($skor >= $ambangPerluPerhatian) return 'Signifikan';
            if ($skor >= $ambangNormal) return 'Perlu Perhatian';
            return 'Normal';
        }

        return null;
    }
}
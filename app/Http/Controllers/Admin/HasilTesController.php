<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HasilTesController extends Controller
{
    protected const ALAT_TES = [
        ['id' => 1, 'nama' => 'DISC', 'format_dasar' => 'Skala Likert'],
        ['id' => 2, 'nama' => 'IST',   'format_dasar' => 'Pilihan Ganda'],
        ['id' => 3, 'nama' => 'EPPS',  'format_dasar' => 'Forced Choice'],
        ['id' => 4, 'nama' => 'MMPI-2','format_dasar' => 'Skala Likert'],
    ];

    protected const PENJADWALAN = [
        [
            'id' => 1,
            'nama_sesi' => 'Rekrutmen Staff Finance Batch 1',
            'departemen_terkait' => 'Finance',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-08-07',
            'status' => 'Aktif',
            'daftar_alat_tes' => ['IST', 'DISC'],
            'jumlah_peserta' => 15,
        ],
        [
            'id' => 2,
            'nama_sesi' => 'Assessment Tahunan Karyawan Divisi Produksi',
            'departemen_terkait' => 'Produksi',
            'tanggal_mulai' => '2026-07-15',
            'tanggal_selesai' => '2026-07-30',
            'status' => 'Selesai',
            'daftar_alat_tes' => ['DISC', 'EPPS', 'MMPI-2'],
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
        ['name' => 'Andi Pratama',   'departemen' => 'Finance', 'posisi' => 'Staff Akuntansi', 'foto_url' => null],
        ['name' => 'Budi Santoso',   'departemen' => 'Produksi','posisi' => 'Supervisor Produksi', 'foto_url' => null],
        ['name' => 'Dewi Lestari',   'departemen' => 'IT',      'posisi' => 'Analyst IT', 'foto_url' => null],
        ['name' => 'Eko Wibowo',     'departemen' => 'Marketing','posisi' => 'Staff Marketing', 'foto_url' => null],
        ['name' => 'Galih Mahendra', 'departemen' => 'Finance', 'posisi' => 'Staff Keuangan', 'foto_url' => null],
        ['name' => 'Hesti Rahmawati','departemen' => 'HR',      'posisi' => 'HR Specialist', 'foto_url' => null],
        ['name' => 'Fitri Handayani','departemen' => 'Operasional','posisi' => 'Admin Operasional', 'foto_url' => null],
    ];

    protected const KANDIDAT = [
        ['name' => 'Rio Saputra',       'departemen' => 'Produksi', 'posisi' => 'Calon Supervisor', 'foto_url' => null],
        ['name' => 'Nurul Aini',        'departemen' => 'HR',       'posisi' => 'Calon HR Specialist', 'foto_url' => null],
        ['name' => 'Bagas Maulana',     'departemen' => 'IT',       'posisi' => 'Calon IT Analyst', 'foto_url' => null],
        ['name' => 'Citra Kirana',      'departemen' => 'Marketing','posisi' => 'Calon Staff Marketing', 'foto_url' => null],
        ['name' => 'Dimas Setiawan',    'departemen' => 'Finance',  'posisi' => 'Calon Akuntan', 'foto_url' => null],
        ['name' => 'Erna Wulandari',    'departemen' => 'Produksi', 'posisi' => 'Calon Operator', 'foto_url' => null],
        ['name' => 'Fajar Nugroho',     'departemen' => 'HR',       'posisi' => 'Calon Rekruter', 'foto_url' => null],
        ['name' => 'Gita Permata',      'departemen' => 'IT',       'posisi' => 'Calon Analis', 'foto_url' => null],
        ['name' => 'Indah Cahyani',     'departemen' => 'Finance',  'posisi' => 'Calon Staff', 'foto_url' => null],
    ];

    // Data dummy hasil tes per peserta per sesi
    protected const HASIL_TES = [
        // Sesi 1: Rekrutmen Staff Finance Batch 1 (IST, DISC)
        [
            'sesi_id' => 1,
            'peserta_id' => 101,
            'nama_peserta' => 'Andi Pratama',
            'jenis_peserta' => 'Karyawan',
            'departemen' => 'Finance',
            'posisi' => 'Staff Akuntansi',
            'status_pengerjaan' => 'Selesai',
            'tanggal_pengerjaan' => '2026-08-05',
            'hasil_alat_tes' => [
                [
                    'nama_alat_tes' => 'IST',
                    'format_dasar' => 'Pilihan Ganda',
                    'durasi_pengerjaan_aktual' => '45 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'total_skor' => 78,
                        'kategori' => 'Tinggi',
                        'deskripsi_kategori' => 'Kesesuaian kerja sangat baik, potensi pengembangan tinggi'
                    ]
                ],
                [
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '32 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'D' => 65, 'I' => 42, 'S' => 78, 'C' => 55
                    ]
                ]
            ]
        ],
        [
            'sesi_id' => 1,
            'peserta_id' => 102,
            'nama_peserta' => 'Dimas Setiawan',
            'jenis_peserta' => 'Kandidat',
            'departemen' => 'Finance',
            'posisi' => 'Calon Akuntan',
            'status_pengerjaan' => 'Sedang Berjalan',
            'tanggal_pengerjaan' => '2026-08-06',
            'hasil_alat_tes' => [
                [
                    'nama_alat_tes' => 'IST',
                    'format_dasar' => 'Pilihan Ganda',
                    'durasi_pengerjaan_aktual' => '28 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => null // belum selesai
                ],
                [
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '25 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'D' => 52, 'I' => 68, 'S' => 45, 'C' => 61
                    ]
                ]
            ]
        ],
        [
            'sesi_id' => 1,
            'peserta_id' => 103,
            'nama_peserta' => 'Galih Mahendra',
            'jenis_peserta' => 'Karyawan',
            'departemen' => 'Finance',
            'posisi' => 'Staff Keuangan',
            'status_pengerjaan' => 'Selesai',
            'tanggal_pengerjaan' => '2026-08-04',
            'hasil_alat_tes' => [
                [
                    'nama_alat_tes' => 'IST',
                    'format_dasar' => 'Pilihan Ganda',
                    'durasi_pengerjaan_aktual' => '52 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'total_skor' => 64,
                        'kategori' => 'Sedang',
                        'deskripsi_kategori' => 'Kesesuaian kerja sedang, memerlukan pelatihan lanjutan'
                    ]
                ],
                [
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '35 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'D' => 58, 'I' => 38, 'S' => 72, 'C' => 49
                    ]
                ]
            ]
        ],
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

        // Sesi 2: Assessment Tahunan Produksi (DISC, EPPS, MMPI-2)
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
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '40 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'D' => 85, 'I' => 55, 'S' => 38, 'C' => 62
                    ]
                ],
                [
                    'nama_alat_tes' => 'EPPS',
                    'format_dasar' => 'Forced Choice',
                    'durasi_pengerjaan_aktual' => '65 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'Achievement' => 78,
                        'Dominance' => 72,
                        'Affiliation' => 45,
                        'Exhibition' => 58
                    ]
                ],
                [
                    'nama_alat_tes' => 'MMPI-2',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '55 menit',
                    'is_sensitif' => true, // sensitif
                    'skor_ringkas' => [
                        'Depresi' => 68,
                        'Paranoia' => 52,
                        'Psychasthenia' => 44
                    ]
                ]
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
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '38 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'D' => 71, 'I' => 48, 'S' => 61, 'C' => 53
                    ]
                ],
                [
                    'nama_alat_tes' => 'EPPS',
                    'format_dasar' => 'Forced Choice',
                    'durasi_pengerjaan_aktual' => '60 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'Achievement' => 65,
                        'Dominance' => 68,
                        'Affiliation' => 52,
                        'Exhibition' => 45
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
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '33 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'D' => 42, 'I' => 35, 'S' => 78, 'C' => 65
                    ]
                ],
                [
                    'nama_alat_tes' => 'EPPS',
                    'format_dasar' => 'Forced Choice',
                    'durasi_pengerjaan_aktual' => '55 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'Achievement' => 54,
                        'Dominance' => 38,
                        'Affiliation' => 71,
                        'Exhibition' => 39
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
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '36 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'D' => 72, 'I' => 45, 'S' => 68, 'C' => 59
                    ]
                ],
                [
                    'nama_alat_tes' => 'EPPS',
                    'format_dasar' => 'Forced Choice',
                    'durasi_pengerjaan_aktual' => '52 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'Achievement' => 61,
                        'Dominance' => 58,
                        'Affiliation' => 65,
                        'Exhibition' => 42
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
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '41 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'D' => 68, 'I' => 52, 'S' => 73, 'C' => 64
                    ]
                ],
                [
                    'nama_alat_tes' => 'EPPS',
                    'format_dasar' => 'Forced Choice',
                    'durasi_pengerjaan_aktual' => '58 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        'Achievement' => 69,
                        'Dominance' => 51,
                        'Affiliation' => 72,
                        'Exhibition' => 48
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
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '39 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => null // belum selesai
                ],
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
                        'Achievement' => 71,
                        'Dominance' => 52,
                        'Affiliation' => 88,
                        'Exhibition' => 65
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

    public function detail($sesiId, $pesertaId): View
    {
        $sesi = collect(self::PENJADWALAN)->where('id', $sesiId)->first();
        $hasilTes = collect(self::HASIL_TES)->where('sesi_id', $sesiId)->where('peserta_id', $pesertaId)->first();

        if (!$sesi || !$hasilTes) {
            abort(404);
        }

        // Cek apakah ada alat tes sensitif MMPI-2 dan user punya izin lihat sensitif
        $bisaLihatSensitif = false;
        foreach ($hasilTes['hasil_alat_tes'] as $alat) {
            if ($alat['nama_alat_tes'] === 'MMPI-2' && $alat['is_sensitif'] && !empty($alat['skor_ringkas'])) {
                $bisaLihatSensitif = auth()->user()->hasIzin('hasil_tes.lihat_sensitif');
                break;
            }
        }

        return view('admin.hasil-tes.detail', [
            'sesi' => $sesi,
            'hasilTes' => $hasilTes,
            'bisaLihatSensitif' => $bisaLihatSensitif,
        ]);
    }
}
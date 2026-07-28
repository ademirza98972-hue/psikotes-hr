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
                        ['nama_subtes' => 'SE - Pengertian Umum', 'skor_mentah' => 15, 'skor_skala' => 118, 'kategori' => 'Baik'],
                        ['nama_subtes' => 'WA - Kosakata',          'skor_mentah' => 17, 'skor_skala' => 124, 'kategori' => 'Sangat Baik'],
                        ['nama_subtes' => 'AN - Aritmatika',        'skor_mentah' => 12, 'skor_skala' => 105, 'kategori' => 'Baik'],
                        ['nama_subtes' => 'GE - Persamaan Kata',    'skor_mentah' => 14, 'skor_skala' => 112, 'kategori' => 'Baik'],
                    ]
                ],
                [
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '32 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        ['dimensi' => 'D - Dominance',   'skor_mentah' => 65, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'I - Influence',   'skor_mentah' => 42, 'skor_skala' => 4, 'kategori' => 'Sedang'],
                        ['dimensi' => 'S - Steadiness',  'skor_mentah' => 78, 'skor_skala' => 8, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'C - Compliance',  'skor_mentah' => 55, 'skor_skala' => 6, 'kategori' => 'Sedang'],
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
                        ['dimensi' => 'D - Dominance',   'skor_mentah' => 52, 'skor_skala' => 5, 'kategori' => 'Sedang'],
                        ['dimensi' => 'I - Influence',   'skor_mentah' => 68, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'S - Steadiness',  'skor_mentah' => 45, 'skor_skala' => 5, 'kategori' => 'Sedang'],
                        ['dimensi' => 'C - Compliance',  'skor_mentah' => 61, 'skor_skala' => 6, 'kategori' => 'Sedang'],
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
                        ['nama_subtes' => 'SE - Pengertian Umum', 'skor_mentah' => 11, 'skor_skala' => 96,  'kategori' => 'Cukup'],
                        ['nama_subtes' => 'WA - Kosakata',          'skor_mentah' => 13, 'skor_skala' => 102, 'kategori' => 'Baik'],
                        ['nama_subtes' => 'AN - Aritmatika',        'skor_mentah' => 10, 'skor_skala' => 88,  'kategori' => 'Kurang'],
                        ['nama_subtes' => 'GE - Persamaan Kata',    'skor_mentah' => 12, 'skor_skala' => 99,  'kategori' => 'Cukup'],
                    ]
                ],
                [
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '35 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        ['dimensi' => 'D - Dominance',   'skor_mentah' => 58, 'skor_skala' => 6, 'kategori' => 'Sedang'],
                        ['dimensi' => 'I - Influence',   'skor_mentah' => 38, 'skor_skala' => 4, 'kategori' => 'Sedang'],
                        ['dimensi' => 'S - Steadiness',  'skor_mentah' => 72, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'C - Compliance',  'skor_mentah' => 49, 'skor_skala' => 5, 'kategori' => 'Sedang'],
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
                        ['dimensi' => 'D - Dominance',   'skor_mentah' => 85, 'skor_skala' => 9, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'I - Influence',   'skor_mentah' => 55, 'skor_skala' => 6, 'kategori' => 'Sedang'],
                        ['dimensi' => 'S - Steadiness',  'skor_mentah' => 38, 'skor_skala' => 4, 'kategori' => 'Sedang'],
                        ['dimensi' => 'C - Compliance',  'skor_mentah' => 62, 'skor_skala' => 6, 'kategori' => 'Sedang'],
                    ]
                ],
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
                [
                    'nama_alat_tes' => 'MMPI-2',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '55 menit',
                    'is_sensitif' => true, // sensitif
                    'skor_ringkas' => [
                        ['skala_klinis' => 'Depresi',                 'skor_t' => 68, 'interpretasi' => 'Perlu Perhatian'],
                        ['skala_klinis' => 'Paranoia',                'skor_t' => 52, 'interpretasi' => 'Normal'],
                        ['skala_klinis' => 'Psychasthenia',           'skor_t' => 74, 'interpretasi' => 'Perlu Perhatian'],
                        ['skala_klinis' => 'Hipocondriasis',          'skor_t' => 61, 'interpretasi' => 'Normal'],
                        ['skala_klinis' => 'Histeria',               'skor_t' => 82, 'interpretasi' => 'Signifikan'],
                        ['skala_klinis' => 'Hiperpatenia',           'skor_t' => 55, 'interpretasi' => 'Normal'],
                        ['skala_klinis' => 'Schizophrenia',          'skor_t' => 69, 'interpretasi' => 'Perlu Perhatian'],
                        ['skala_klinis' => 'Mania',                  'skor_t' => 48, 'interpretasi' => 'Normal'],
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
                        ['dimensi' => 'D - Dominance',   'skor_mentah' => 71, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'I - Influence',   'skor_mentah' => 48, 'skor_skala' => 5, 'kategori' => 'Sedang'],
                        ['dimensi' => 'S - Steadiness',  'skor_mentah' => 61, 'skor_skala' => 6, 'kategori' => 'Sedang'],
                        ['dimensi' => 'C - Compliance',  'skor_mentah' => 53, 'skor_skala' => 6, 'kategori' => 'Sedang'],
                    ]
                ],
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
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '33 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        ['dimensi' => 'D - Dominance',   'skor_mentah' => 42, 'skor_skala' => 5, 'kategori' => 'Sedang'],
                        ['dimensi' => 'I - Influence',   'skor_mentah' => 35, 'skor_skala' => 4, 'kategori' => 'Rendah'],
                        ['dimensi' => 'S - Steadiness',  'skor_mentah' => 78, 'skor_skala' => 9, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'C - Compliance',  'skor_mentah' => 65, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                    ]
                ],
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
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '36 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        ['dimensi' => 'D - Dominance',   'skor_mentah' => 72, 'skor_skala' => 8, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'I - Influence',   'skor_mentah' => 45, 'skor_skala' => 5, 'kategori' => 'Sedang'],
                        ['dimensi' => 'S - Steadiness',  'skor_mentah' => 68, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'C - Compliance',  'skor_mentah' => 59, 'skor_skala' => 6, 'kategori' => 'Sedang'],
                    ]
                ],
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
                    'nama_alat_tes' => 'DISC',
                    'format_dasar' => 'Skala Likert',
                    'durasi_pengerjaan_aktual' => '41 menit',
                    'is_sensitif' => false,
                    'skor_ringkas' => [
                        ['dimensi' => 'D - Dominance',   'skor_mentah' => 68, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'I - Influence',   'skor_mentah' => 52, 'skor_skala' => 6, 'kategori' => 'Sedang'],
                        ['dimensi' => 'S - Steadiness',  'skor_mentah' => 73, 'skor_skala' => 8, 'kategori' => 'Tinggi'],
                        ['dimensi' => 'C - Compliance',  'skor_mentah' => 64, 'skor_skala' => 7, 'kategori' => 'Tinggi'],
                    ]
                ],
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
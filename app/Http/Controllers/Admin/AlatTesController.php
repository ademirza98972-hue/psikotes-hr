<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlatTesController extends Controller
{
    /**
     * Data dummy hardcode — akan diganti query ke tabel `alat_tes`
     * setelah backend modul Tes dibangun.
     */
    public const DUMMY_ALAT_TES = [
        [
            'id' => 3,
            'nama' => 'EPPS (Edwards Personal Preference Schedule)',
            'format_dasar' => 'Forced Choice',
            'durasi_total_menit' => 45,
            'batas_waktu_per_soal_aktif' => false,
            'batas_waktu_per_soal_detik' => null,
            'is_sensitif' => false,
            'jumlah_soal' => 225,
            'dimensi' => [
                [
                    'nama_dimensi' => 'Achievement',
                    'kode' => 'Ach',
                    'bidang_psikogram' => 'Potensi Kerja',
                    'deskripsi_aspek' => 'Mengukur kecenderungan mengejar prestasi standar tinggi dan ketekunan.',
                    'skor_min' => 1,
                    'skor_max' => 10,
                    'tipe_kategori' => 'psikogram',
                    'ambang_r' => 2,
                    'ambang_k' => 4,
                    'ambang_c' => 6,
                    'ambang_b' => 8,
                ],
                [
                    'nama_dimensi' => 'Dominance',
                    'kode' => 'Dom',
                    'bidang_psikogram' => 'Potensi Kerja',
                    'deskripsi_aspek' => 'Menilai keinginan mengarahkan orang lain dan mengambil keputusan.',
                    'skor_min' => 1,
                    'skor_max' => 10,
                    'tipe_kategori' => 'psikogram',
                    'ambang_r' => 2,
                    'ambang_k' => 4,
                    'ambang_c' => 6,
                    'ambang_b' => 8,
                ],
                [
                    'nama_dimensi' => 'Affiliation',
                    'kode' => 'Aff',
                    'bidang_psikogram' => 'Kepribadian',
                    'deskripsi_aspek' => 'Mengukur kebutuhan akan hubungan sosial dan acceptance dari orang lain.',
                    'skor_min' => 1,
                    'skor_max' => 10,
                    'tipe_kategori' => 'psikogram',
                    'ambang_r' => 2,
                    'ambang_k' => 4,
                    'ambang_c' => 6,
                    'ambang_b' => 8,
                ],
                [
                    'nama_dimensi' => 'Exhibition',
                    'kode' => 'Exp',
                    'bidang_psikogram' => 'Kepribadian',
                    'deskripsi_aspek' => 'Menilai keinginan tampil di depan orang dan menjadi perhatian publik.',
                    'skor_min' => 1,
                    'skor_max' => 10,
                    'tipe_kategori' => 'psikogram',
                    'ambang_r' => 2,
                    'ambang_k' => 4,
                    'ambang_c' => 6,
                    'ambang_b' => 8,
                ],
            ],
        ],
    ];

    public function index(): View
    {
        return view('admin.alat-tes.index', [
            'alatTes' => self::DUMMY_ALAT_TES,
        ]);
    }

    public function tambah(): View
    {
        return view('admin.alat-tes.tambah', [
            'pilihanFormat' => ['Pilihan Ganda', 'Skala Likert', 'Forced Choice'],
        ]);
    }

    public function simpan(Request $request)
    {
        return redirect()
            ->route('admin.alat-tes.index')
            ->with('sukses', 'Fitur simpan akan aktif setelah backend selesai dibangun.');
    }
}
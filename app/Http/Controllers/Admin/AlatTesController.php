<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\DimensiAlatTes;
use App\Models\LevelDimensi;
use App\Models\NormaKonversi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AlatTesController extends Controller
{
    public function index(): View
    {
        // withCount('soal') mengambil jumlah soal real dari tabel soal
        $alatTes = AlatTes::withCount('soal')
            ->orderBy('nama')
            ->get();

        $totalSoal = $alatTes->sum('soal_count');

        return view('admin.alat-tes.index', [
            'alatTes' => $alatTes,
            'totalSoal' => $totalSoal,
        ]);
    }

    public function tambah(): View
    {
        return view('admin.alat-tes.tambah', [
            'pilihanFormat' => ['Pilihan Ganda', 'Skala Likert', 'Forced Choice', 'Grid', 'Mixed'],
        ]);
    }

    public function simpan(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'kode' => 'required|string|max:20|unique:alat_tes,kode',
            'format_dasar' => 'required|string|max:50',
            'pola_skoring' => 'required|string|in:kognitif,forced_choice,forced_choice_rollup,grid',
            'jumlah_soal' => 'nullable|integer|min:0',
            'durasi_total_menit' => 'nullable|integer|min:0',
            'batas_waktu_per_soal_aktif' => 'boolean',
            'batas_waktu_per_soal_detik' => 'nullable|integer|min:1',
            'is_sensitif' => 'boolean',
        ]);

        AlatTes::create([
            'nama' => $data['nama'],
            'kode' => $data['kode'],
            'format_dasar' => $data['format_dasar'],
            'pola_skoring' => $data['pola_skoring'],
            'jumlah_soal' => $data['jumlah_soal'] ?? null,
            'durasi_total_menit' => $data['durasi_total_menit'] ?? null,
            'batas_waktu_per_soal_aktif' => $request->boolean('batas_waktu_per_soal_aktif'),
            'batas_waktu_per_soal_detik' => $data['batas_waktu_per_soal_detik'] ?? null,
            'is_sensitif' => $request->boolean('is_sensitif'),
            'is_aktif' => true,
        ]);

        return redirect()
            ->route('admin.alat-tes.index')
            ->with('sukses', "Alat tes '{$data['nama']}' berhasil disimpan.");
    }

    public function edit(int $id): View
    {
        $alatTes = AlatTes::findOrFail($id);

        return view('admin.alat-tes.edit', [
            'alatTes' => $alatTes,
            'pilihanFormat' => ['Pilihan Ganda', 'Skala Likert', 'Forced Choice', 'Grid', 'Mixed'],
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'kode' => 'required|string|max:20|unique:alat_tes,kode,'.$id,
            'format_dasar' => 'required|string|max:50',
            'pola_skoring' => 'required|string|in:kognitif,forced_choice,forced_choice_rollup,grid',
            'jumlah_soal' => 'nullable|integer|min:0',
            'durasi_total_menit' => 'nullable|integer|min:0',
            'batas_waktu_per_soal_aktif' => 'boolean',
            'batas_waktu_per_soal_detik' => 'nullable|integer|min:1',
            'is_sensitif' => 'boolean',
        ]);

        $alatTes = AlatTes::findOrFail($id);
        $alatTes->update([
            'nama' => $data['nama'],
            'kode' => $data['kode'],
            'format_dasar' => $data['format_dasar'],
            'pola_skoring' => $data['pola_skoring'],
            'jumlah_soal' => $data['jumlah_soal'] ?? null,
            'durasi_total_menit' => $data['durasi_total_menit'] ?? null,
            'batas_waktu_per_soal_aktif' => $request->boolean('batas_waktu_per_soal_aktif'),
            'batas_waktu_per_soal_detik' => $data['batas_waktu_per_soal_detik'] ?? null,
            'is_sensitif' => $request->boolean('is_sensitif'),
        ]);

        return redirect()
            ->route('admin.alat-tes.index')
            ->with('sukses', "Alat tes '{$data['nama']}' berhasil diperbarui.");
    }

    public function hapus(int $id)
    {
        $alatTes = AlatTes::findOrFail($id);
        $nama = $alatTes->nama;
        $alatTes->delete();

        return redirect()
            ->route('admin.alat-tes.index')
            ->with('sukses', "Alat tes '{$nama}' berhasil dihapus.");
    }

    public function restore(int $id)
    {
        $alatTes = AlatTes::withTrashed()->findOrFail($id);
        $nama = $alatTes->nama;
        $alatTes->restore();

        return redirect()
            ->route('admin.alat-tes.index')
            ->with('sukses', "Alat tes '{$nama}' berhasil dipulihkan.");
    }

    public function norma(int $id): View
    {
        $alatTes = AlatTes::findOrFail($id);
        $dimensi = \App\Models\DimensiAlatTes::where('alat_tes_id', $id)
            ->orderBy('urutan')->get();
        $jumlahNorma = NormaKonversi::where('alat_tes_id', $id)->count();
        $contohNorma = NormaKonversi::where('alat_tes_id', $id)
            ->with('dimensi')
            ->orderBy('dimensi_id')
            ->orderBy('skor_mentah_min')
            ->limit(10)
            ->get();

        return view('admin.alat-tes.norma', compact(
            'alatTes', 'dimensi', 'jumlahNorma', 'contohNorma'
        ));
    }

    public function uploadNorma(Request $request, int $id)
    {
        $alatTes = AlatTes::findOrFail($id);

        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:2048',
            'hapus_lama' => 'boolean',
        ]);

        $file = $request->file('file_csv');
        $baris = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_map('trim', array_shift($baris));

        // Validasi header CSV
        $headerWajib = ['dimensi_kode','kelompok_segmen','tahap',
                        'skor_mentah_min','skor_mentah_max','skor_hasil'];
        $headerKurang = array_diff($headerWajib, $header);
        if (!empty($headerKurang)) {
            return back()->withErrors([
                'file_csv' => 'Header CSV kurang: ' . implode(', ', $headerKurang)
            ]);
        }

        // Ambil semua dimensi alat tes ini untuk lookup kode → id
        $dimensiMap = \App\Models\DimensiAlatTes::where('alat_tes_id', $id)
            ->pluck('id', 'kode_dimensi');

        $rows = [];
        $errors = [];
        foreach ($baris as $i => $kolom) {
            if (count($kolom) !== count($header)) continue;
            $row = array_combine($header, $kolom);

            $dimensiId = $dimensiMap[$row['dimensi_kode']] ?? null;
            if (!$dimensiId) {
                $errors[] = "Baris " . ($i + 2) . ": kode dimensi '{$row['dimensi_kode']}' tidak ditemukan.";
                continue;
            }

            $rows[] = [
                'alat_tes_id'     => $id,
                'dimensi_id'      => $dimensiId,
                'kelompok_segmen' => trim($row['kelompok_segmen']),
                'tahap'           => (int) $row['tahap'],
                'skor_mentah_min' => (float) $row['skor_mentah_min'],
                'skor_mentah_max' => (float) $row['skor_mentah_max'],
                'skor_hasil'      => (float) $row['skor_hasil'],
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        }

        if (!empty($errors)) {
            return back()->withErrors(['file_csv' => implode(' | ', $errors)]);
        }

        DB::transaction(function () use ($id, $rows, $request) {
            if ($request->boolean('hapus_lama')) {
                NormaKonversi::where('alat_tes_id', $id)->delete();
            }
            foreach (array_chunk($rows, 500) as $chunk) {
                NormaKonversi::insert($chunk);
            }
        });

        $jumlah = count($rows);
        return redirect()
            ->route('admin.alat-tes.norma', $id)
            ->with('sukses', "{$jumlah} baris norma berhasil diimport.");
    }

    public function dimensi(int $id): View
    {
        $alatTes = AlatTes::findOrFail($id);
        $dimensi = DimensiAlatTes::where('alat_tes_id', $id)
            ->with('levelDimensi')
            ->orderBy('urutan')
            ->get();

        return view('admin.alat-tes.dimensi', compact('alatTes', 'dimensi'));
    }

    public function simpanDimensi(Request $request, int $id)
    {
        AlatTes::findOrFail($id);

        $data = $request->validate([
            'kode_dimensi'   => 'required|string|max:50',
            'nama_dimensi'   => 'required|string|max:150',
            'deskripsi_aspek'=> 'nullable|string',
            'tipe_kategori'  => 'required|in:psikogram,klinis',
            'arah_skor'      => 'required|in:tinggi_baik,rendah_baik',
            'urutan'         => 'nullable|integer|min:0',
        ]);

        DimensiAlatTes::create(array_merge($data, [
            'alat_tes_id' => $id,
            'urutan'      => $data['urutan']
                ?? (DimensiAlatTes::where('alat_tes_id', $id)->max('urutan') + 1),
        ]));

        return redirect()
            ->route('admin.alat-tes.dimensi', $id)
            ->with('sukses', "Dimensi '{$data['nama_dimensi']}' berhasil ditambahkan.");
    }

    public function updateDimensi(Request $request, int $id, int $dimensiId)
    {
        $dimensi = DimensiAlatTes::where('alat_tes_id', $id)
            ->findOrFail($dimensiId);

        $data = $request->validate([
            'kode_dimensi'   => 'required|string|max:50',
            'nama_dimensi'   => 'required|string|max:150',
            'deskripsi_aspek'=> 'nullable|string',
            'tipe_kategori'  => 'required|in:psikogram,klinis',
            'arah_skor'      => 'required|in:tinggi_baik,rendah_baik',
            'urutan'         => 'nullable|integer|min:0',
        ]);

        $dimensi->update($data);

        return redirect()
            ->route('admin.alat-tes.dimensi', $id)
            ->with('sukses', "Dimensi '{$data['nama_dimensi']}' berhasil diupdate.");
    }

    public function hapusDimensi(int $id, int $dimensiId)
    {
        $dimensi = DimensiAlatTes::where('alat_tes_id', $id)
            ->findOrFail($dimensiId);
        $nama = $dimensi->nama_dimensi;
        $dimensi->delete();

        return redirect()
            ->route('admin.alat-tes.dimensi', $id)
            ->with('sukses', "Dimensi '{$nama}' berhasil dihapus.");
    }

    public function simpanLevel(Request $request, int $id, int $dimensiId)
    {
        $dimensi = DimensiAlatTes::where('alat_tes_id', $id)
            ->findOrFail($dimensiId);

        $data = $request->validate([
            'levels'           => 'required|array|min:1',
            'levels.*.label'   => 'required|string|max:50',
            'levels.*.skor_min'=> 'required|numeric',
            'levels.*.skor_max'=> 'required|numeric',
            'levels.*.urutan'  => 'required|integer|min:0',
        ]);

        LevelDimensi::where('dimensi_id', $dimensiId)->delete();

        foreach ($data['levels'] as $level) {
            LevelDimensi::create([
                'alat_tes_id' => $id,
                'dimensi_id'  => $dimensiId,
                'label'       => $level['label'],
                'skor_min'    => $level['skor_min'],
                'skor_max'    => $level['skor_max'],
                'urutan'      => $level['urutan'],
            ]);
        }

        return redirect()
            ->route('admin.alat-tes.dimensi', $id)
            ->with('sukses', "Level dimensi '{$dimensi->nama_dimensi}' berhasil disimpan.");
    }
}
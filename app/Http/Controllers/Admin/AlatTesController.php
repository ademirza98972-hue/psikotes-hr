<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerbaruiAlatTesRequest;
use App\Http\Requests\SimpanAlatTesRequest;
use App\Http\Requests\SimpanDimensiAlatTesRequest;
use App\Http\Requests\SimpanLevelDimensiRequest;
use App\Http\Requests\UploadNormaAlatTesRequest;
use App\Models\AlatTes;
use App\Models\DimensiAlatTes;
use App\Models\LevelDimensi;
use App\Models\NormaKonversi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AlatTesController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = $this->getPerPage($request, 10);

        // Compute stats before paginating
        $totalSoal = AlatTes::withCount('soal')->get()->sum('soal_count');
        $totalAlat = AlatTes::count();
        $alatPopuler = AlatTes::withCount('soal')->orderByDesc('soal_count')->first();

        $alatTes = AlatTes::withCount('soal')
            ->orderBy('nama')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.alat-tes.index', [
            'alatTes'       => $alatTes,
            'totalSoal'     => $totalSoal,
            'totalAlat'     => $totalAlat,
            'alatPopuler'   => $alatPopuler,
            'pilihanFormat' => ['Pilihan Ganda', 'Skala Likert', 'Forced Choice', 'Grid', 'Mixed'],
        ]);
    }

    public function tambah(): View
    {
        return view('admin.alat-tes.tambah', [
            'pilihanFormat' => ['Pilihan Ganda', 'Skala Likert', 'Forced Choice', 'Grid', 'Mixed'],
        ]);
    }

    public function simpan(SimpanAlatTesRequest $request): RedirectResponse
    {
        $data = $request->validated();

        AlatTes::create([
            'nama' => $data['nama'],
            'kode' => $data['kode'],
            'format_dasar' => $data['format_dasar'],
            'pola_skoring' => $data['pola_skoring'],
            'jumlah_soal' => $data['jumlah_soal'] ?? null,
            'durasi_total_menit' => $data['durasi_total_menit'],
            'batas_waktu_per_soal_aktif' => $request->boolean('batas_waktu_per_soal_aktif'),
            'batas_waktu_per_soal_detik' => $data['batas_waktu_per_soal_detik'],
            'is_sensitif' => $request->boolean('is_sensitif'),
            'is_aktif' => true,
        ]);

        return redirect()
            ->route('admin.alat-tes.index')
            ->with($this::FLASH_SUCCESS, "Alat tes '{$data['nama']}' berhasil disimpan.");
    }

    public function edit(int $id): View
    {
        $alatTes = AlatTes::findOrFail($id);

        return view('admin.alat-tes.edit', [
            'alatTes' => $alatTes,
            'pilihanFormat' => ['Pilihan Ganda', 'Skala Likert', 'Forced Choice', 'Grid', 'Mixed'],
        ]);
    }

    public function update(PerbaruiAlatTesRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();

        $alatTes = AlatTes::findOrFail($id);
        $alatTes->update([
            'nama' => $data['nama'],
            'kode' => $data['kode'],
            'format_dasar' => $data['format_dasar'],
            'pola_skoring' => $data['pola_skoring'],
            'jumlah_soal' => $data['jumlah_soal'] ?? null,
            'durasi_total_menit' => $data['durasi_total_menit'],
            'batas_waktu_per_soal_aktif' => $request->boolean('batas_waktu_per_soal_aktif'),
            'batas_waktu_per_soal_detik' => $data['batas_waktu_per_soal_detik'],
            'is_sensitif' => $request->boolean('is_sensitif'),
        ]);

        return redirect()
            ->route('admin.alat-tes.index')
            ->with($this::FLASH_SUCCESS, "Alat tes '{$data['nama']}' berhasil diperbarui.");
    }

    public function hapus(int $id): RedirectResponse
    {
        $alatTes = AlatTes::findOrFail($id);
        $nama = $alatTes->nama;
        $alatTes->deleted_by = Auth::id();
        $alatTes->save();
        $alatTes->delete();

        return redirect()
            ->route('admin.alat-tes.index')
            ->with($this::FLASH_SUCCESS, "Alat tes '{$nama}' berhasil dihapus.");
    }

    public function restore(int $id): RedirectResponse
    {
        $alatTes = AlatTes::withTrashed()->findOrFail($id);
        $nama = $alatTes->nama;
        $alatTes->restore();

        return redirect()
            ->route('admin.alat-tes.index')
            ->with($this::FLASH_SUCCESS, "Alat tes '{$nama}' berhasil dipulihkan.");
    }

    public function norma(Request $request, int $id): View
    {
        $alatTes = AlatTes::findOrFail($id);
        $dimensi = \App\Models\DimensiAlatTes::where('alat_tes_id', $id)
            ->orderBy('urutan')->get();
        $jumlahNorma = NormaKonversi::where('alat_tes_id', $id)->count();

        $perPage = $this->getPerPage($request, 25, [25, 50, 100, 250]);
        $filterDimensi = $request->input('dimensi');

        $contohNorma = NormaKonversi::where('alat_tes_id', $id)
            ->with('dimensi')
            ->when($filterDimensi, fn($q) => $q->where('dimensi_id', $filterDimensi))
            ->orderBy('dimensi_id')
            ->orderBy('kelompok_segmen')
            ->orderBy('skor_mentah_min')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.alat-tes.norma', compact(
            'alatTes', 'dimensi', 'jumlahNorma', 'contohNorma', 'perPage', 'filterDimensi'
        ));
    }

    public function uploadNorma(UploadNormaAlatTesRequest $request, int $id): RedirectResponse
    {
        $alatTes = AlatTes::findOrFail($id);

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
            ->with($this::FLASH_SUCCESS, "{$jumlah} baris norma berhasil diimport.");
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

    public function simpanDimensi(SimpanDimensiAlatTesRequest $request, int $id): RedirectResponse
    {
        AlatTes::findOrFail($id);

        $data = $request->validated();

        DimensiAlatTes::create(array_merge($data, [
            'alat_tes_id' => $id,
            'urutan'      => $data['urutan']
                ?? (DimensiAlatTes::where('alat_tes_id', $id)->max('urutan') + 1),
        ]));

        return redirect()
            ->route('admin.alat-tes.dimensi', $id)
            ->with($this::FLASH_SUCCESS, "Dimensi '{$data['nama_dimensi']}' berhasil ditambahkan.");
    }

    public function updateDimensi(SimpanDimensiAlatTesRequest $request, int $id, int $dimensiId): RedirectResponse
    {
        $dimensi = DimensiAlatTes::where('alat_tes_id', $id)
            ->findOrFail($dimensiId);

        $data = $request->validated();

        $dimensi->update($data);

        return redirect()
            ->route('admin.alat-tes.dimensi', $id)
            ->with($this::FLASH_SUCCESS, "Dimensi '{$data['nama_dimensi']}' berhasil diupdate.");
    }

    public function hapusDimensi(int $id, int $dimensiId): RedirectResponse
    {
        $dimensi = DimensiAlatTes::where('alat_tes_id', $id)
            ->findOrFail($dimensiId);
        $nama = $dimensi->nama_dimensi;
        $dimensi->delete();

        return redirect()
            ->route('admin.alat-tes.dimensi', $id)
            ->with($this::FLASH_SUCCESS, "Dimensi '{$nama}' berhasil dihapus.");
    }

    public function simpanLevel(SimpanLevelDimensiRequest $request, int $id, int $dimensiId): RedirectResponse
    {
        $dimensi = DimensiAlatTes::where('alat_tes_id', $id)
            ->findOrFail($dimensiId);

        $data = $request->validated();

        DB::transaction(function () use ($id, $dimensiId, $data) {
            $levelIds = LevelDimensi::where('dimensi_id', $dimensiId)->pluck('id');
            if ($levelIds->isNotEmpty()) {
                DB::table('hasil_skor_peserta')->whereIn('level_id', $levelIds)->update(['level_id' => null]);
            }
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
        });

        return redirect()
            ->route('admin.alat-tes.dimensi', $id)
            ->with($this::FLASH_SUCCESS, "Level dimensi '{$dimensi->nama_dimensi}' berhasil disimpan.");
    }
}
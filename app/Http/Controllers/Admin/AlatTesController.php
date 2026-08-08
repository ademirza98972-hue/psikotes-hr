<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlatTesController extends Controller
{
    public function index(): View
    {
        // toArray() supaya view bisa memakai array_column() (tidak bekerja pada Collection Eloquent)
        $alatTes = AlatTes::withTrashed(false)
            ->orderBy('nama')
            ->get()
            ->toArray();

        return view('admin.alat-tes.index', [
            'alatTes' => $alatTes,
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
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'kode' => 'required|string|max:20|unique:alat_tes,kode',
            'format_dasar' => 'required|string|max:50',
            'jumlah_soal' => 'nullable|integer|min:0',
            'durasi_total_menit' => 'nullable|integer|min:0',
            'batas_waktu_per_soal_aktif' => 'boolean',
            'is_sensitif' => 'boolean',
        ]);

        AlatTes::create([
            'nama' => $data['nama'],
            'kode' => $data['kode'],
            'format_dasar' => $data['format_dasar'],
            'jumlah_soal' => $data['jumlah_soal'] ?? null,
            'durasi_total_menit' => $data['durasi_total_menit'] ?? null,
            'batas_waktu_per_soal_aktif' => $request->boolean('batas_waktu_per_soal_aktif'),
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
            'pilihanFormat' => ['Pilihan Ganda', 'Skala Likert', 'Forced Choice'],
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'kode' => 'required|string|max:20|unique:alat_tes,kode,'.$id,
            'format_dasar' => 'required|string|max:50',
            'jumlah_soal' => 'nullable|integer|min:0',
            'durasi_total_menit' => 'nullable|integer|min:0',
            'batas_waktu_per_soal_aktif' => 'boolean',
            'is_sensitif' => 'boolean',
        ]);

        $alatTes = AlatTes::findOrFail($id);
        $alatTes->update([
            'nama' => $data['nama'],
            'kode' => $data['kode'],
            'format_dasar' => $data['format_dasar'],
            'jumlah_soal' => $data['jumlah_soal'] ?? null,
            'durasi_total_menit' => $data['durasi_total_menit'] ?? null,
            'batas_waktu_per_soal_aktif' => $request->boolean('batas_waktu_per_soal_aktif'),
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
}
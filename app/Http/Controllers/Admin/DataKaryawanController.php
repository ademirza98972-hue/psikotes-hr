<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DataKaryawanTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\PerbaruiDataKaryawanRequest;
use App\Http\Requests\SimpanDataKaryawanRequest;
use App\Imports\DataKaryawanImport;
use App\Models\DataKaryawan;
use App\Models\Departemen;
use App\Models\Posisi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class DataKaryawanController extends Controller
{
    public function index(Request $request): View
    {
        $kataKunci = $request->input('cari');
        $filterDepartemen = $request->input('departemen');
        $filterStatus = $request->input('status');

        $validStatus = ['belum_terpakai', 'sudah_terpakai'];
        if ($filterStatus !== null && $filterStatus !== '' && ! in_array($filterStatus, $validStatus, true)) {
            $filterStatus = null;
        }

        $data = DataKaryawan::query()
            ->with(['departemen', 'posisi'])
            ->when($kataKunci, function ($query, $kataKunci) {
                $query->where(function ($q) use ($kataKunci) {
                    $q->where('nik_karyawan', 'like', '%' . $kataKunci . '%')
                        ->orWhere('nama_karyawan', 'like', '%' . $kataKunci . '%');
                });
            })
            ->when($filterDepartemen, function ($query, $filterDepartemen) {
                $query->where('departemen_id', (int) $filterDepartemen);
            })
            ->when($filterStatus, function ($query, $filterStatus) {
                $query->where('status', $filterStatus);
            })
            ->orderBy('nama_karyawan')
            ->paginate(15)
            ->withQueryString();

        $semuaDepartemen = Departemen::orderBy('nama_departemen')->get();

        return view('admin.data-karyawan.index', [
            'data' => $data,
            'kataKunci' => $kataKunci,
            'filterDepartemen' => $filterDepartemen,
            'filterStatus' => $filterStatus,
            'semuaDepartemen' => $semuaDepartemen,
        ]);
    }

    public function tambah(): View
    {
        $departemen = Departemen::orderBy('nama_departemen')->get();
        return view('admin.data-karyawan.tambah', compact('departemen'));
    }

    public function simpan(SimpanDataKaryawanRequest $request): RedirectResponse
    {
        DataKaryawan::create($request->validated());

        return redirect()
            ->route('admin.data-karyawan.index')
            ->with('sukses', 'Data karyawan berhasil ditambahkan.');
    }

    public function ubah(int $data_karyawan): View
    {
        $data = DataKaryawan::with(['departemen', 'posisi'])->findOrFail($data_karyawan);
        $departemen = Departemen::orderBy('nama_departemen')->get();
        $posisi = Posisi::query()
            ->when($data->departemen_id, fn ($q) => $q->where('departemen_id', $data->departemen_id))
            ->orderBy('nama_posisi')
            ->get();

        return view('admin.data-karyawan.ubah', [
            'data' => $data,
            'departemen' => $departemen,
            'posisi' => $posisi,
        ]);
    }

    public function perbarui(PerbaruiDataKaryawanRequest $request, int $data_karyawan): RedirectResponse
    {
        $data = DataKaryawan::findOrFail($data_karyawan);
        $data->update($request->validated());

        return redirect()
            ->route('admin.data-karyawan.index')
            ->with('sukses', 'Data karyawan berhasil diperbarui.');
    }

    public function hapus(int $data_karyawan): RedirectResponse
    {
        $data = DataKaryawan::findOrFail($data_karyawan);

        if ($data->status === 'sudah_terpakai') {
            return back()->with('error', 'NIK ini sudah dipakai untuk registrasi, tidak bisa dihapus. Hapus dulu akun user terkait jika ingin melepas NIK ini.');
        }

        $data->delete();

        return redirect()
            ->route('admin.data-karyawan.index')
            ->with('sukses', 'Data karyawan berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [
            'file_excel.required' => 'File Excel wajib dipilih.',
            'file_excel.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_excel.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $import = new DataKaryawanImport();

        try {
            Excel::import($import, $request->file('file_excel'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file Excel. Pastikan format file sesuai template.');
        }

        $pesan = "Berhasil mengimpor {$import->berhasil} data karyawan.";

        if ($import->dilewati > 0) {
            $pesan .= " {$import->dilewati} baris dilewati (kolom wajib kosong).";
        }

        if (count($import->duplikat) > 0) {
            $nikList = implode(', ', $import->duplikat);
            return redirect()
                ->route('admin.data-karyawan.index')
                ->with('sukses', $pesan)
                ->with('duplikat_nik', $import->duplikat)
                ->with('info', 'NIK berikut sudah terdaftar dan dilewati: ' . $nikList);
        }

        return redirect()
            ->route('admin.data-karyawan.index')
            ->with('sukses', $pesan);
    }

    public function downloadTemplate(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new DataKaryawanTemplateExport(), 'template-data-karyawan.xlsx');
    }
}

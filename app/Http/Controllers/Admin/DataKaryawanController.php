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
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class DataKaryawanController extends Controller
{
    private function subqueryMemilikiAkun(): string
    {
        return 'EXISTS(
            SELECT 1 FROM profil_karyawan pk
            JOIN users u ON u.id = pk.user_id AND u.deleted_at IS NULL
            WHERE pk.nik_karyawan = data_karyawan.nik_karyawan
        )';
    }

    public function index(Request $request): View
    {
        $kataKunci = $request->input('cari');
        $filterDepartemen = $request->input('departemen');
        $filterStatus = $request->input('status');

        $validStatus = ['belum_terpakai', 'sudah_terpakai'];
        if ($filterStatus !== null && $filterStatus !== '' && ! in_array($filterStatus, $validStatus, true)) {
            $filterStatus = null;
        }

        $subquery = $this->subqueryMemilikiAkun();

        $perPage = in_array((int)$request->input('per_page'), [10, 25, 50, 100]) ? (int)$request->input('per_page') : 15;

        $data = DataKaryawan::query()
            ->with(['departemen', 'posisi'])
            ->selectRaw("data_karyawan.*, ({$subquery}) as memiliki_akun")
            ->when($kataKunci, function ($query, $kataKunci) {
                $query->where(function ($q) use ($kataKunci) {
                    $q->where('nik_karyawan', 'like', '%' . $kataKunci . '%')
                        ->orWhere('nama_karyawan', 'like', '%' . $kataKunci . '%');
                });
            })
            ->when($filterDepartemen, function ($query, $filterDepartemen) {
                $query->where('departemen_id', (int) $filterDepartemen);
            })
            ->when($filterStatus === 'sudah_terpakai', fn ($q) => $q->whereRaw($subquery))
            ->when($filterStatus === 'belum_terpakai', fn ($q) => $q->whereRaw("NOT ({$subquery})"))
            ->orderBy('nama_karyawan')
            ->paginate($perPage)
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

        $memilikiAkun = DB::table('profil_karyawan')
            ->join('users', 'profil_karyawan.user_id', '=', 'users.id')
            ->whereNull('users.deleted_at')
            ->where('profil_karyawan.nik_karyawan', $data->nik_karyawan)
            ->exists();

        if ($memilikiAkun) {
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

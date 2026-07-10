<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerbaruiDataKaryawanRequest;
use App\Http\Requests\SimpanDataKaryawanRequest;
use App\Models\DataKaryawan;
use App\Models\Departemen;
use App\Models\Posisi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataKaryawanController extends Controller
{
    public function index(Request $request): View
    {
        $kataKunci = $request->input('cari');

        $data = DataKaryawan::query()
            ->with(['departemen', 'posisi'])
            ->when($kataKunci, function ($query, $kataKunci) {
                $query->where(function ($q) use ($kataKunci) {
                    $q->where('nik_karyawan', 'like', '%' . $kataKunci . '%')
                        ->orWhere('nama_karyawan', 'like', '%' . $kataKunci . '%');
                });
            })
            ->orderBy('nama_karyawan')
            ->paginate(15)
            ->withQueryString();

        return view('admin.data-karyawan.index', [
            'data' => $data,
            'kataKunci' => $kataKunci,
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
}
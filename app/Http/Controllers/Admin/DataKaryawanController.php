<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DataKaryawanTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportDataKaryawanRequest;
use App\Http\Requests\PerbaruiDataKaryawanRequest;
use App\Http\Requests\SimpanDataKaryawanRequest;
use App\Imports\DataKaryawanImport;
use App\Models\DataKaryawan;
use App\Models\Departemen;
use App\Models\Peran;
use App\Models\Posisi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DataKaryawanController extends Controller
{
    public function index(Request $request): View
    {
        $kataKunci       = $request->input('cari');
        $filterDepartemen = $request->input('departemen');
        $filterPosisi    = $request->input('posisi');
        $perPage         = $this->getPerPage($request, 15);

        $data = DataKaryawan::with(['departemen', 'posisi', 'user'])
            ->when($kataKunci, function ($q) use ($kataKunci) {
                $q->where(function ($inner) use ($kataKunci) {
                    $inner->where('nik_karyawan', 'like', "%{$kataKunci}%")
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$kataKunci}%"));
                });
            })
            ->when($filterDepartemen, fn ($q) => $q->where('departemen_id', (int) $filterDepartemen))
            ->when($filterPosisi,    fn ($q) => $q->where('posisi_id', (int) $filterPosisi))
            ->orderBy(
                \App\Models\User::select('name')
                    ->whereColumn('users.id', 'data_karyawan.user_id')
                    ->limit(1)
            )
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.akun-karyawan.index', [
            'data'            => $data,
            'kataKunci'       => $kataKunci,
            'filterDepartemen' => $filterDepartemen,
            'filterPosisi'    => $filterPosisi,
            'semuaDepartemen' => Departemen::orderBy('nama_departemen')->get(),
            'semuaPosisi'     => Posisi::when($filterDepartemen, fn ($q) => $q->where('departemen_id', $filterDepartemen))->orderBy('nama_posisi')->get(),
        ]);
    }

    public function simpan(SimpanDataKaryawanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $peran = Peran::where('nama_peran', 'Karyawan')->first();
        if (! $peran) {
            return back()->withInput()->withErrors(['nama_karyawan' => 'Peran Karyawan belum tersedia di sistem.']);
        }

        DB::transaction(function () use ($validated, $peran) {
            $user = User::create([
                'name'                 => $validated['nama_karyawan'],
                'email'                => $validated['email'] ?: null,
                'id_karyawan'          => $validated['id_karyawan'],
                'password'             => Hash::make($validated['id_karyawan']),
                'tipe_akun'            => 'karyawan',
                'peran_id'             => $peran->id,
                'status'               => 'nonaktif',
                'must_change_password' => true,
            ]);

            DataKaryawan::create([
                'nik_karyawan'        => $validated['nik_karyawan'],
                'jenis_kelamin'       => $validated['jenis_kelamin'],
                'no_hp'               => $validated['no_hp'] ?? null,
                'tanggal_lahir'       => $validated['tanggal_lahir'],
                'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
                'departemen_id'       => $validated['departemen_id'],
                'posisi_id'           => $validated['posisi_id'],
                'user_id'             => $user->id,
            ]);
        });

        return redirect()
            ->route('admin.akun-karyawan.index')
            ->with($this::FLASH_SUCCESS, 'Karyawan berhasil ditambahkan.');
    }

    public function perbarui(PerbaruiDataKaryawanRequest $request, int $data_karyawan): RedirectResponse
    {
        $data      = DataKaryawan::with('user')->findOrFail($data_karyawan);
        $validated = $request->validated();

        DB::transaction(function () use ($data, $validated) {
            $data->update([
                'nik_karyawan'        => $validated['nik_karyawan'],
                'jenis_kelamin'       => $validated['jenis_kelamin'],
                'no_hp'               => $validated['no_hp'] ?? null,
                'tanggal_lahir'       => $validated['tanggal_lahir'],
                'pendidikan_terakhir' => $validated['pendidikan_terakhir'] ?? null,
                'departemen_id'       => $validated['departemen_id'],
                'posisi_id'           => $validated['posisi_id'],
            ]);

            if ($data->user) {
                $data->user->update([
                    'name'        => $validated['nama_karyawan'],
                    'email'       => $validated['email'] ?: null,
                    'id_karyawan' => $validated['id_karyawan'],
                ]);
            }
        });

        return redirect()
            ->route('admin.akun-karyawan.index')
            ->with($this::FLASH_SUCCESS, 'Data karyawan berhasil diperbarui.');
    }

    public function hapus(int $data_karyawan): RedirectResponse
    {
        $data = DataKaryawan::with('user')->findOrFail($data_karyawan);

        DB::transaction(function () use ($data) {
            if ($data->user) {
                if ($data->user->id === Auth::id()) {
                    throw new \RuntimeException('Tidak dapat menghapus akun Anda sendiri.');
                }
                $data->user->deleted_by = Auth::id();
                $data->user->save();
                $data->user->delete();
            }

            $data->deleted_by = Auth::id();
            $data->save();
            $data->delete();
        });

        return redirect()
            ->route('admin.akun-karyawan.index')
            ->with($this::FLASH_SUCCESS, 'Data karyawan berhasil dihapus.');
    }

    public function toggleStatus(int $data_karyawan): RedirectResponse
    {
        $data = DataKaryawan::with('user')->findOrFail($data_karyawan);

        if (! $data->user) {
            return back()->with('error', 'Karyawan ini belum memiliki akun.');
        }

        if ($data->user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $statusBaru = $data->user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $data->user->update(['status' => $statusBaru]);

        $pesan = $statusBaru === 'aktif' ? 'Akun karyawan berhasil diaktifkan.' : 'Akun karyawan berhasil dinonaktifkan.';

        return redirect()
            ->route('admin.akun-karyawan.index')
            ->with($this::FLASH_SUCCESS, $pesan);
    }

    public function resetPassword(int $data_karyawan): RedirectResponse
    {
        $data = DataKaryawan::with('user')->findOrFail($data_karyawan);

        if (! $data->user || ! $data->user->id_karyawan) {
            return back()->with('error', 'Tidak dapat mereset password.');
        }

        $data->user->update([
            'password'             => Hash::make($data->user->id_karyawan),
            'must_change_password' => true,
        ]);

        return redirect()
            ->route('admin.akun-karyawan.index')
            ->with($this::FLASH_SUCCESS, "Password direset ke ID Karyawan ({$data->user->id_karyawan}). Karyawan wajib ganti password saat login.");
    }

    public function import(ImportDataKaryawanRequest $request): RedirectResponse
    {
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
                ->route('admin.akun-karyawan.index')
                ->with($this::FLASH_SUCCESS, $pesan)
                ->with('duplikat_nik', $import->duplikat)
                ->with('info', 'NIK berikut sudah terdaftar dan dilewati: ' . $nikList);
        }

        return redirect()
            ->route('admin.akun-karyawan.index')
            ->with($this::FLASH_SUCCESS, $pesan);
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(new DataKaryawanTemplateExport(), 'template-data-karyawan.xlsx');
    }
}

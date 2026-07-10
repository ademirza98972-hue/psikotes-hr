<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerbaruiPenggunaRequest;
use App\Http\Requests\SimpanPenggunaRequest;
use App\Models\DataKaryawan;
use App\Models\Departemen;
use App\Models\Peran;
use App\Models\Posisi;
use App\Models\ProfilKaryawan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    public function index(Request $request): View
    {
        $kataKunci = $request->input('cari');

        $pengguna = User::with(['peran', 'profilKaryawan'])
            ->where('tipe_akun', 'karyawan')
            ->when($kataKunci, function ($query, $kataKunci) {
                $query->where(function ($q) use ($kataKunci) {
                    $q->where('name', 'like', '%' . $kataKunci . '%')
                        ->orWhere('email', 'like', '%' . $kataKunci . '%');
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.akun-karyawan.index', [
            'pengguna' => $pengguna,
            'kataKunci' => $kataKunci,
        ]);
    }

    public function tambah(): View
    {
        $departemen = Departemen::orderBy('nama_departemen')->get();

        return view('admin.akun-karyawan.tambah', [
            'departemen' => $departemen,
            'posisi' => collect(),
        ]);
    }

    public function simpan(SimpanPenggunaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $peran = Peran::where('nama_peran', 'Karyawan')->first();

        if (! $peran) {
            return back()
                ->withInput()
                ->withErrors(['tipe_akun' => 'Peran untuk tipe akun ini belum tersedia di sistem.']);
        }

        DB::transaction(function () use ($data, $peran) {
            $user = User::create([
                'name' => $data['nama_karyawan'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'no_hp' => $data['no_hp'],
                'tipe_akun' => 'karyawan',
                'peran_id' => $peran->id,
                'status' => 'aktif',
            ]);

            $departemen = Departemen::findOrFail($data['departemen']);
            $jabatanNama = null;
            if (!empty($data['jabatan'])) {
                $jabatan = Posisi::find($data['jabatan']);
                $jabatanNama = $jabatan ? $jabatan->nama_posisi : null;
            }

            $dataMaster = DataKaryawan::where('nik_karyawan', $data['nik_karyawan'])
                ->lockForUpdate()
                ->first();

            if (! $dataMaster || strcasecmp($dataMaster->nama_karyawan, $data['nama_karyawan']) !== 0) {
                throw new \RuntimeException('NIK dan nama tidak cocok dengan data karyawan.');
            }

            if ($dataMaster->status === 'sudah_terpakai') {
                throw new \RuntimeException('NIK ini sudah terpakai oleh akun lain.');
            }

            ProfilKaryawan::create([
                'user_id' => $user->id,
                'nama_karyawan' => $data['nama_karyawan'],
                'nik_karyawan' => $dataMaster->nik_karyawan,
                'departemen' => $departemen->nama_departemen,
                'jabatan' => $jabatanNama,
            ]);

            $dataMaster->update(['status' => 'sudah_terpakai']);
        });

        return redirect()
            ->route('admin.akun-karyawan.index')
            ->with('sukses', 'Akun karyawan berhasil ditambahkan.');
    }

    public function ubah(int $id): View
    {
        $pengguna = User::with(['peran', 'profilKaryawan'])->findOrFail($id);

        abort_unless($pengguna->tipe_akun === 'karyawan', 404);

        $departemen = Departemen::orderBy('nama_departemen')->get();

        $departemenId = Departemen::where('nama_departemen', optional($pengguna->profilKaryawan)->departemen)->value('id');
        $posisi = $departemenId
            ? Posisi::where('departemen_id', $departemenId)->orderBy('nama_posisi')->get()
            : collect();

        $currentPosisiId = $pengguna->profilKaryawan?->jabatan
            ? Posisi::where('nama_posisi', $pengguna->profilKaryawan->jabatan)->value('id')
            : null;

        return view('admin.akun-karyawan.ubah', [
            'pengguna' => $pengguna,
            'departemen' => $departemen,
            'posisi' => $posisi,
            'currentPosisiId' => $currentPosisiId,
            'currentDepartemenId' => $departemenId,
        ]);
    }

    public function perbarui(PerbaruiPenggunaRequest $request, int $id): RedirectResponse
    {
        $pengguna = User::findOrFail($id);
        $data = $request->validated();

        abort_unless($pengguna->tipe_akun === 'karyawan', 404);

        DB::transaction(function () use ($pengguna, $data) {
            $payload = [
                'name' => $data['nama_karyawan'],
                'email' => $data['email'],
                'no_hp' => $data['no_hp'],
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            $pengguna->update($payload);

            $profil = $pengguna->profilKaryawan;
            if ($profil) {
                $departemen = Departemen::findOrFail($data['departemen']);
                $jabatanNama = null;
                if (!empty($data['jabatan'])) {
                    $jabatan = Posisi::find($data['jabatan']);
                    $jabatanNama = $jabatan ? $jabatan->nama_posisi : null;
                }

                $profil->update([
                    'nama_karyawan' => $data['nama_karyawan'],
                    'departemen' => $departemen->nama_departemen,
                    'jabatan' => $jabatanNama,
                ]);
            }
        });

        return redirect()
            ->route('admin.akun-karyawan.index')
            ->with('sukses', 'Data akun karyawan berhasil diperbarui.');
    }

    public function hapus(int $id): RedirectResponse
    {
        $pengguna = User::findOrFail($id);

        abort_unless($pengguna->tipe_akun === 'karyawan', 404);

        if ($pengguna->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        DB::transaction(function () use ($pengguna) {
            $profil = $pengguna->profilKaryawan;
            if ($profil) {
                $dataMaster = DataKaryawan::where('nik_karyawan', $profil->nik_karyawan)
                    ->where('status', 'sudah_terpakai')
                    ->first();

                if ($dataMaster) {
                    $dataMaster->update(['status' => 'belum_terpakai']);
                }
            }

            $pengguna->delete();
        });

        return redirect()
            ->route('admin.akun-karyawan.index')
            ->with('sukses', 'Akun karyawan berhasil dihapus.');
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        $pengguna = User::findOrFail($id);

        abort_unless($pengguna->tipe_akun === 'karyawan', 404);

        if ($pengguna->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $pengguna->update(['status' => $pengguna->status === 'aktif' ? 'nonaktif' : 'aktif']);

        return redirect()
            ->route('admin.akun-karyawan.index')
            ->with('sukses', $pengguna->fresh()->status === 'aktif' ? 'Akun karyawan berhasil diaktifkan.' : 'Akun karyawan berhasil dinonaktifkan.');
    }
}
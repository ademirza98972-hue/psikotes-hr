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
        $filterDepartemenId = $request->input('departemen');
        $filterPosisiId = $request->input('posisi');

        $filterDepartemenNama = $filterDepartemenId
            ? Departemen::whereKey($filterDepartemenId)->value('nama_departemen')
            : null;
        $filterPosisiNama = $filterPosisiId
            ? Posisi::whereKey($filterPosisiId)->value('nama_posisi')
            : null;

        $query = User::with(['peran', 'profilKaryawan.dataKaryawan'])
            ->where('tipe_akun', 'karyawan');

        $query->when($kataKunci, function ($q) use ($kataKunci) {
            $q->where(function ($inner) use ($kataKunci) {
                $inner->where('name', 'like', '%' . $kataKunci . '%')
                    ->orWhere('email', 'like', '%' . $kataKunci . '%')
                    ->orWhereHas('profilKaryawan', fn ($pq) => $pq->where('nik_karyawan', 'like', '%' . $kataKunci . '%'))
                    ->orWhereHas('profilKaryawan.dataKaryawan', fn ($dq) => $dq->where('nik_karyawan', 'like', '%' . $kataKunci . '%'));
            });
        });

        $query->when($filterDepartemenNama, function ($q) use ($filterDepartemenNama) {
            $q->whereHas('profilKaryawan', fn ($pq) => $pq->where('departemen', $filterDepartemenNama));
        });

        $query->when($filterPosisiNama, function ($q) use ($filterPosisiNama) {
            $q->whereHas('profilKaryawan', fn ($pq) => $pq->where('jabatan', $filterPosisiNama));
        });

        $perPage = $this->getPerPage($request, 15);

        $pengguna = $query->orderBy('name')->paginate($perPage)->withQueryString();

        $semuaDepartemen = Departemen::orderBy('nama_departemen')->get();
        $semuaPosisiQuery = Posisi::orderBy('nama_posisi');
        if ($filterDepartemenId) {
            $semuaPosisiQuery->where('departemen_id', $filterDepartemenId);
        }
        $semuaPosisi = $semuaPosisiQuery->get();

        return view('admin.akun-karyawan.index', [
            'pengguna' => $pengguna,
            'kataKunci' => $kataKunci,
            'semuaDepartemen' => $semuaDepartemen,
            'semuaPosisi' => $semuaPosisi,
            'filterDepartemen' => $filterDepartemenId,
            'filterPosisi' => $filterPosisiId,
        ]);
    }

    public function tambah(): View
    {
        return view('admin.akun-karyawan.tambah');
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

        try {
            DB::transaction(function () use ($data, $peran) {
                $dataMaster = DataKaryawan::where('nik_karyawan', $data['nik_karyawan'])
                    ->lockForUpdate()
                    ->first();

                if (! $dataMaster || strcasecmp($dataMaster->nama_karyawan, $data['nama_karyawan']) !== 0) {
                    throw new \RuntimeException('NIK dan nama tidak cocok dengan data karyawan.');
                }

                if ($dataMaster->status === 'sudah_terpakai') {
                    throw new \RuntimeException('NIK ini sudah terpakai oleh akun lain.');
                }

                $user = User::create([
                    'name' => $dataMaster->nama_karyawan,
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'no_hp' => $data['no_hp'],
                    'tipe_akun' => 'karyawan',
                    'peran_id' => $peran->id,
                    'status' => 'aktif',
                ]);

                ProfilKaryawan::create([
                    'user_id' => $user->id,
                    'data_karyawan_id' => $dataMaster->id,
                    'nama_karyawan' => $dataMaster->nama_karyawan,
                    'nik_karyawan' => $dataMaster->nik_karyawan,
                    'departemen' => $dataMaster->departemen,
                    'jabatan' => $dataMaster->jabatan,
                    'tanggal_lahir' => $dataMaster->tanggal_lahir,
                ]);

                $dataMaster->update(['status' => 'sudah_terpakai']);
            });
        } catch (\RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors(['nik_karyawan' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.akun-karyawan.index')
            ->with($this::FLASH_SUCCESS, 'Akun karyawan berhasil ditambahkan.');
    }

    public function ubah(int $id): View
    {
        $pengguna = User::with(['peran', 'profilKaryawan'])->findOrFail($id);

        abort_unless($pengguna->tipe_akun === 'karyawan', 404);

        return view('admin.akun-karyawan.ubah', compact('pengguna'));
    }

    public function perbarui(PerbaruiPenggunaRequest $request, int $id): RedirectResponse
    {
        $pengguna = User::findOrFail($id);
        $data = $request->validated();

        abort_unless($pengguna->tipe_akun === 'karyawan', 404);

        DB::transaction(function () use ($pengguna, $data) {
            $payload = [
                'email' => $data['email'],
                'no_hp' => $data['no_hp'],
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            $pengguna->update($payload);
        });

        return redirect()
            ->route('admin.akun-karyawan.index')
            ->with($this::FLASH_SUCCESS, 'Data akun karyawan berhasil diperbarui.');
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

            $pengguna->deleted_by = Auth::id();
            $pengguna->save();
            $pengguna->delete();
        });

        return redirect()
            ->route('admin.akun-karyawan.index')
            ->with($this::FLASH_SUCCESS, 'Akun karyawan berhasil dihapus.');
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
            ->with($this::FLASH_SUCCESS, $pengguna->fresh()->status === 'aktif' ? 'Akun karyawan berhasil diaktifkan.' : 'Akun karyawan berhasil dinonaktifkan.');
    }
}
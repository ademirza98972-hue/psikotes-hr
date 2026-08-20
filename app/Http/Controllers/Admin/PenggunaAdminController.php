<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerbaruiPenggunaAdminRequest;
use App\Http\Requests\SimpanPenggunaAdminRequest;
use App\Models\Peran;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PenggunaAdminController extends Controller
{
    private const PERAN_DIKECUALIKAN = ['Kandidat', 'Karyawan'];

    public function index(Request $request): View
    {
        $kataKunci = $request->input('cari');
        $filterPeran = $request->input('peran');
        $filterStatus = $request->input('status');

        $validStatus = ['aktif', 'nonaktif'];
        if ($filterStatus !== null && $filterStatus !== '' && ! in_array($filterStatus, $validStatus, true)) {
            $filterStatus = null;
        }

        $pengguna = User::with('peran')
            ->where('tipe_akun', 'custom')
            ->whereHas('peran', function ($q) {
                $q->whereNotIn('nama_peran', self::PERAN_DIKECUALIKAN);
            })
            ->when($kataKunci, function ($query, $kataKunci) {
                $query->where(function ($q) use ($kataKunci) {
                    $q->where('name', 'like', '%' . $kataKunci . '%')
                        ->orWhere('email', 'like', '%' . $kataKunci . '%');
                });
            })
            ->when($filterPeran, function ($query, $filterPeran) {
                $query->where('peran_id', (int) $filterPeran);
            })
            ->when($filterStatus, function ($query, $filterStatus) {
                $query->where('status', $filterStatus);
            })
            ->orderBy('name')
            ->paginate(in_array((int)$request->input('per_page'), [10, 25, 50, 100]) ? (int)$request->input('per_page') : 15)
            ->withQueryString();

        $semuaPeran = Peran::whereNotIn('nama_peran', self::PERAN_DIKECUALIKAN)
            ->orderBy('nama_peran')
            ->get();

        return view('admin.pengguna-admin.index', [
            'pengguna' => $pengguna,
            'kataKunci' => $kataKunci,
            'filterPeran' => $filterPeran,
            'filterStatus' => $filterStatus,
            'semuaPeran' => $semuaPeran,
        ]);
    }

    public function tambah(): View
    {
        return view('admin.pengguna-admin.tambah', [
            'daftarPeran' => Peran::whereNotIn('nama_peran', self::PERAN_DIKECUALIKAN)->orderBy('nama_peran')->get(),
        ]);
    }

    public function simpan(SimpanPenggunaAdminRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $peran = Peran::find($data['peran_id']);
        if (! $peran || in_array($peran->nama_peran, self::PERAN_DIKECUALIKAN, true)) {
            return back()
                ->withInput()
                ->withErrors(['peran_id' => 'Peran Kandidat/Karyawan tidak dapat dipilih untuk admin/staff.']);
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'no_hp' => $data['no_hp'],
            'tipe_akun' => 'custom',
            'peran_id' => $peran->id,
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('admin.pengguna-admin.index')
            ->with('sukses', 'Pengguna internal berhasil ditambahkan.');
    }

    public function ubah(int $id): View
    {
        $pengguna = $this->cariPenggunaInternal($id);

        return view('admin.pengguna-admin.ubah', [
            'pengguna' => $pengguna,
            'daftarPeran' => Peran::whereNotIn('nama_peran', self::PERAN_DIKECUALIKAN)->orderBy('nama_peran')->get(),
        ]);
    }

    public function perbarui(PerbaruiPenggunaAdminRequest $request, int $id): RedirectResponse
    {
        $pengguna = $this->cariPenggunaInternal($id);
        $data = $request->validated();

        $peran = Peran::find($data['peran_id']);
        if (! $peran || in_array($peran->nama_peran, self::PERAN_DIKECUALIKAN, true)) {
            return back()
                ->withInput()
                ->withErrors(['peran_id' => 'Peran Kandidat/Karyawan tidak dapat dipilih untuk admin/staff.']);
        }

        if ($pengguna->id === Auth::id() && $peran->nama_peran !== 'Super Admin') {
            return back()
                ->withInput()
                ->withErrors(['peran_id' => 'Anda tidak dapat menurunkan peran Anda sendiri dari Super Admin.']);
        }

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'],
            'peran_id' => $peran->id,
            'status' => $data['status'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        DB::transaction(function () use ($pengguna, $payload) {
            $pengguna->update($payload);
        });

        return redirect()
            ->route('admin.pengguna-admin.index')
            ->with('sukses', 'Data pengguna internal berhasil diperbarui.');
    }

    public function hapus(int $id): RedirectResponse
    {
        $pengguna = $this->cariPenggunaInternal($id);

        if ($pengguna->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($pengguna->peran && $pengguna->peran->nama_peran === 'Super Admin') {
            $jumlahSuperAdmin = User::where('tipe_akun', 'custom')
                ->whereHas('peran', fn ($q) => $q->where('nama_peran', 'Super Admin'))
                ->count();

            if ($jumlahSuperAdmin <= 1) {
                return back()->with('error', 'Tidak dapat menghapus Super Admin terakhir yang tersisa.');
            }
        }

        $pengguna->delete();

        return redirect()
            ->route('admin.pengguna-admin.index')
            ->with('sukses', 'Pengguna internal berhasil dihapus.');
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        $pengguna = $this->cariPenggunaInternal($id);

        if ($pengguna->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        if ($pengguna->peran && $pengguna->peran->nama_peran === 'Super Admin' && $pengguna->status === 'aktif') {
            $jumlahSuperAdminAktif = User::where('tipe_akun', 'custom')
                ->where('status', 'aktif')
                ->whereHas('peran', fn ($q) => $q->where('nama_peran', 'Super Admin'))
                ->count();

            if ($jumlahSuperAdminAktif <= 1) {
                return back()->with('error', 'Tidak dapat menonaktifkan Super Admin terakhir yang aktif. Minimal harus tersisa 1 Super Admin aktif.');
            }
        }

        $pengguna->update(['status' => $pengguna->status === 'aktif' ? 'nonaktif' : 'aktif']);

        return redirect()
            ->route('admin.pengguna-admin.index')
            ->with('sukses', $pengguna->fresh()->status === 'aktif' ? 'Pengguna internal berhasil diaktifkan.' : 'Pengguna internal berhasil dinonaktifkan.');
    }

    private function cariPenggunaInternal(int $id): User
    {
        $pengguna = User::with('peran')->findOrFail($id);

        abort_unless($pengguna->tipe_akun === 'custom', 404);

        $namaPeran = optional($pengguna->peran)->nama_peran;
        abort_unless(! in_array($namaPeran, self::PERAN_DIKECUALIKAN, true), 404);

        return $pengguna;
    }
}

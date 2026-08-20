<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerbaruiPeranRequest;
use App\Http\Requests\SimpanPeranRequest;
use App\Models\Izin;
use App\Models\Peran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeranController extends Controller
{
    protected const SUPER_ADMIN = 'Super Admin';
    protected const IZIN_WAJIB_SUPER_ADMIN = ['peran.kelola'];

    public function index(Request $request): View
    {
        $kataKunci = $request->input('cari');

        $query = Peran::withCount(['izin', 'pengguna'])->orderBy('nama_peran');

        $query->when($kataKunci, function ($q) use ($kataKunci) {
            $q->where('nama_peran', 'like', '%' . $kataKunci . '%');
        });

        $peran = $query->paginate(15)->withQueryString();

        return view('admin.peran.index', [
            'peran' => $peran,
            'kataKunci' => $kataKunci,
        ]);
    }

    public function tambah(): View
    {
        $semuaIzin = Izin::orderBy('kode_izin')->get();

        return view('admin.peran.tambah', [
            'daftarIzin' => $semuaIzin,
            'kelompokIzin' => $this->kelompokkanIzin($semuaIzin),
            'izinTerpilih' => [],
        ]);
    }

    public function simpan(SimpanPeranRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $peran = Peran::create([
            'nama_peran' => $data['nama_peran'],
            'deskripsi' => $data['deskripsi'] ?? null,
        ]);

        $peran->izin()->sync($data['izin']);

        return redirect()
            ->route('admin.peran.index')
            ->with('sukses', 'Peran baru berhasil ditambahkan.');
    }

    public function ubah(int $peran): View
    {
        $peran = Peran::with('izin')->findOrFail($peran);
        $semuaIzin = Izin::orderBy('kode_izin')->get();

        return view('admin.peran.ubah', [
            'peran' => $peran,
            'daftarIzin' => $semuaIzin,
            'kelompokIzin' => $this->kelompokkanIzin($semuaIzin),
            'izinTerpilih' => $peran->izin->pluck('id')->all(),
        ]);
    }

    public function perbarui(PerbaruiPeranRequest $request, int $peran): RedirectResponse
    {
        $peran = Peran::findOrFail($peran);
        $data = $request->validated();

        $izinIds = $data['izin'];

        if ($peran->nama_peran === self::SUPER_ADMIN) {
            $izinIds = $this->paksaIzinWajibSuperAdmin($izinIds);
        }

        $peran->update([
            'nama_peran' => $data['nama_peran'],
            'deskripsi' => $data['deskripsi'] ?? null,
        ]);

        $peran->izin()->sync($izinIds);

        return redirect()
            ->route('admin.peran.index')
            ->with('sukses', 'Peran berhasil diperbarui.');
    }

    public function hapus(int $peran): RedirectResponse
    {
        $peran = Peran::withCount('pengguna')->findOrFail($peran);

        if ($peran->nama_peran === self::SUPER_ADMIN) {
            return back()->with('error', 'Peran Super Admin tidak dapat dihapus.');
        }

        if ($peran->pengguna_count > 0) {
            return back()->with('error', "Peran ini masih dipakai oleh {$peran->pengguna_count} pengguna, tidak dapat dihapus.");
        }

        $peran->delete();

        return redirect()
            ->route('admin.peran.index')
            ->with('sukses', 'Peran berhasil dihapus.');
    }

    protected function kelompokkanIzin($izin): array
    {
        $kelompok = [];

        foreach ($izin as $item) {
            $parts = explode('.', $item->kode_izin);
            $prefix = $parts[0] ?? 'lainnya';
            $label = $this->labelKelompok($prefix);
            $kelompok[$label][] = $item;
        }

        ksort($kelompok);

        return $kelompok;
    }

    protected function labelKelompok(string $prefix): string
    {
        $peta = [
            'dashboard' => 'Dashboard',
            'data_karyawan' => 'Data Karyawan',
            'hasil_tes' => 'Hasil Tes',
            'kategori_tes' => 'Kategori Tes',
            'pengguna' => 'Akun Karyawan & Kandidat',
            'pengguna_admin' => 'Admin & Staff',
            'peran' => 'Peran',
            'soal' => 'Bank Soal & Alat Tes',
        ];

        return $peta[$prefix] ?? ucwords(str_replace('_', ' ', $prefix));
    }

    protected function paksaIzinWajibSuperAdmin(array $izinIds): array
    {
        $wajibIds = Izin::whereIn('kode_izin', self::IZIN_WAJIB_SUPER_ADMIN)->pluck('id')->all();

        return array_values(array_unique(array_merge($izinIds, $wajibIds)));
    }
}
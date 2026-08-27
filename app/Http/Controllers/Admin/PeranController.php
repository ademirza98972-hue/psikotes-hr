<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerbaruiPeranRequest;
use App\Http\Requests\SimpanPeranRequest;
use App\Models\Izin;
use App\Models\Peran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PeranController extends Controller
{
    protected const SUPER_ADMIN = 'Super Admin';
    protected const IZIN_WAJIB_SUPER_ADMIN = ['peran.kelola'];

    public function index(Request $request): View
    {
        return view('admin.peran.index', $this->indexViewData($request));
    }

    public function tambah(Request $request): View
    {
        return view('admin.peran.index', array_merge($this->indexViewData($request), [
            'openModal'    => 'tambah',
            'editPeran'    => null,
            'izinTerpilih' => [],
        ]));
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
            ->with($this::FLASH_SUCCESS, 'Peran baru berhasil ditambahkan.');
    }

    public function ubah(Request $request, int $peran): View
    {
        $editPeran = Peran::with('izin')->findOrFail($peran);

        return view('admin.peran.index', array_merge($this->indexViewData($request), [
            'openModal'    => 'ubah',
            'editPeran'    => $editPeran,
            'izinTerpilih' => $editPeran->izin->pluck('id')->all(),
        ]));
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
            ->with($this::FLASH_SUCCESS, 'Peran berhasil diperbarui.');
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

        $peran->deleted_by = Auth::id();
        $peran->save();
        $peran->delete();

        return redirect()
            ->route('admin.peran.index')
            ->with($this::FLASH_SUCCESS, 'Peran berhasil dihapus.');
    }

    private function indexViewData(Request $request): array
    {
        $kataKunci = $request->input('cari');
        $semuaIzin = Izin::orderBy('kode_izin')->get();

        $query = Peran::withCount(['izin', 'pengguna'])->with('izin:id')->orderBy('nama_peran');
        $query->when($kataKunci, fn($q) => $q->where('nama_peran', 'like', "%$kataKunci%"));

        return [
            'peran'       => $query->paginate($this->getPerPage($request, 15))->withQueryString(),
            'kataKunci'   => $kataKunci,
            'kelompokIzin' => $this->kelompokkanIzin($semuaIzin),
        ];
    }

    protected function kelompokkanIzin(mixed $izin): array
    {
        $kelompok = [];

        foreach ($izin as $item) {
            $parts = explode('.', $item->kode_izin);
            $prefix = $parts[0] ?? 'lainnya';

            if ($prefix === 'kategori_tes') {
                continue;
            }

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
            'pengguna' => 'Akun Karyawan & Kandidat',
            'pengguna_admin' => 'Admin & Staff',
            'peran' => 'Peran',
            'soal' => 'Bank Soal, Alat Tes & Penjadwalan',
            'posisi' => 'Data Posisi',
        ];

        return $peta[$prefix] ?? ucwords(str_replace('_', ' ', $prefix));
    }

    protected function paksaIzinWajibSuperAdmin(array $izinIds): array
    {
        $wajibIds = Izin::whereIn('kode_izin', self::IZIN_WAJIB_SUPER_ADMIN)->pluck('id')->all();

        return array_values(array_unique(array_merge($izinIds, $wajibIds)));
    }
}
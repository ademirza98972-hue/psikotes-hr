<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\DataKaryawan;
use App\Models\Departemen;
use App\Models\Peran;
use App\Models\Posisi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataTerhapusController extends Controller
{
    /**
     * Whitelist of supported data type metadata (labels, badge colors).
     */
    private const META = [
        'karyawan'   => ['label' => 'Akun Karyawan', 'badge' => 'blue'],
        'kandidat'   => ['label' => 'Akun Kandidat', 'badge' => 'purple'],
        'admin'      => ['label' => 'Akun Admin/Staff', 'badge' => 'amber'],
        'data_karyawan' => ['label' => 'Data Karyawan', 'badge' => 'teal'],
        'departemen' => ['label' => 'Departemen', 'badge' => 'green'],
        'posisi'     => ['label' => 'Posisi', 'badge' => 'cyan'],
        'peran'      => ['label' => 'Peran', 'badge' => 'rose'],
        'alat_tes'   => ['label' => 'Alat Tes', 'badge' => 'teal'],
    ];

    /**
     * Build an Eloquent query for the given jenis key, restricted to soft-deleted rows only.
     */
    private function buatQuery(string $jenis): \Illuminate\Database\Eloquent\Builder
    {
        return match ($jenis) {
            'karyawan' => User::where('tipe_akun', 'karyawan')->onlyTrashed(),
            'kandidat' => User::where('tipe_akun', 'kandidat')->onlyTrashed(),
            'admin' => User::where('tipe_akun', 'custom')
                ->onlyTrashed()
                ->whereHas('peran', fn ($q) => $q->whereNotIn('nama_peran', ['Kandidat', 'Karyawan'])),
            'data_karyawan' => DataKaryawan::onlyTrashed()->with(['departemen', 'posisi']),
            'departemen' => Departemen::onlyTrashed()->withCount('posisi'),
            'posisi' => Posisi::onlyTrashed()->with('departemen'),
            'peran' => Peran::onlyTrashed()->withCount('pengguna'),
            'alat_tes' => AlatTes::onlyTrashed()->orderBy('nama'),
            default => abort(404),
        };
    }

    public function index(Request $request, ?string $jenis = null): View
    {
        $jenisAktif = $this->resolveJenis($request->query->get('jenis'));

        $badgeCounts = [];
        foreach (array_keys(self::META) as $key) {
            $badgeCounts[$key] = $this->buatQuery($key)->count();
        }

        $items = $this->buatQuery($jenisAktif)
            ->orderBy('deleted_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.data-terhapus.index', [
            'jenisAktif' => $jenisAktif,
            'konfigAktif' => self::META[$jenisAktif],
            'daftarJenis' => self::META,
            'badgeCounts' => $badgeCounts,
            'items' => $items,
        ]);
    }

    public function pulihkan(Request $request, string $jenis, int $id): RedirectResponse
    {
        $label = self::META[$jenis]['label'] ?? null;
        if ($label === null) {
            abort(404);
        }

        $item = $this->buatQuery($jenis)->findOrFail($id);
        $item->restore();

        return redirect()
            ->route('admin.data-terhapus.index', ['jenis' => $jenis])
            ->with('sukses', "{$label} berhasil dipulihkan.");
    }

    public function hapusPermanen(Request $request, string $jenis, int $id): RedirectResponse
    {
        $label = self::META[$jenis]['label'] ?? null;
        if ($label === null) {
            abort(404);
        }

        $item = $this->buatQuery($jenis)->findOrFail($id);
        $item->forceDelete();

        return redirect()
            ->route('admin.data-terhapus.index', ['jenis' => $jenis])
            ->with('sukses', "{$label} berhasil dihapus permanen.");
    }

    private function resolveJenis(?string $jenis): string
    {
        if ($jenis !== null && array_key_exists($jenis, self::META)) {
            return $jenis;
        }

        return array_key_first(self::META);
    }
}

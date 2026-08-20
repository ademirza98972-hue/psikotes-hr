<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DepartemenController extends Controller
{
    public function index(Request $request): View
    {
        $kataKunci = $request->input('cari');

        $query = Departemen::withCount('posisi')->orderBy('nama_departemen');

        $query->when($kataKunci, function ($q) use ($kataKunci) {
            $q->where('nama_departemen', 'like', '%' . $kataKunci . '%');
        });

        $perPage = in_array((int)$request->input('per_page'), [10, 25, 50, 100]) ? (int)$request->input('per_page') : 10;

        $departemen = $query->paginate($perPage)->withQueryString();

        return view('admin.departemen.index', [
            'departemen' => $departemen,
            'kataKunci' => $kataKunci,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_departemen' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    $exists = DB::table('departemen')
                        ->where('nama_departemen', $value)
                        ->exists();
                    if ($exists) {
                        $fail('Nama departemen sudah terdaftar atau pernah digunakan sebelumnya. Gunakan nama lain atau pulihkan dari Data Terhapus.');
                    }
                },
            ],
        ]);

        Departemen::create($request->only('nama_departemen'));

        return redirect()->route('admin.departemen.index')
            ->with('sukses', 'Data departemen berhasil ditambahkan.');
    }

    public function update(Request $request, Departemen $departemen): RedirectResponse
    {
        $request->validate([
            'nama_departemen' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($departemen) {
                    $exists = DB::table('departemen')
                        ->whereNull('deleted_at')
                        ->where('nama_departemen', $value)
                        ->where('id', '!=', $departemen->id)
                        ->exists();
                    if ($exists) {
                        $fail('Nama departemen sudah terdaftar.');
                    }
                },
            ],
        ]);

        $departemen->update($request->only('nama_departemen'));

        return redirect()->route('admin.departemen.index')
            ->with('sukses', 'Data departemen berhasil diperbarui.');
    }

    public function destroy(Departemen $departemen): RedirectResponse
    {
        if ($departemen->posisi()->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus departemen yang masih memiliki posisi terkait.');
        }

        $departemen->delete();

        return redirect()->route('admin.departemen.index')
            ->with('sukses', 'Data departemen berhasil dihapus.');
    }
}
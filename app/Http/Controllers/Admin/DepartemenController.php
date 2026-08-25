<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PerbaruiDepartemenRequest;
use App\Http\Requests\SimpanDepartemenRequest;
use App\Http\Controllers\Controller;
use App\Models\Departemen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $perPage = $this->getPerPage($request, 10);

        $departemen = $query->paginate($perPage)->withQueryString();

        return view('admin.departemen.index', [
            'departemen' => $departemen,
            'kataKunci' => $kataKunci,
        ]);
    }

    public function store(SimpanDepartemenRequest $request): RedirectResponse
    {
        Departemen::create($request->validated());

        Departemen::create($request->only('nama_departemen'));

        return redirect()->route('admin.departemen.index')
            ->with($this::FLASH_SUCCESS, 'Data departemen berhasil ditambahkan.');
    }

    public function update(PerbaruiDepartemenRequest $request, Departemen $departemen): RedirectResponse
    {
        $departemen->update($request->validated());

        return redirect()->route('admin.departemen.index')
            ->with($this::FLASH_SUCCESS, 'Data departemen berhasil diperbarui.');
    }

    public function destroy(Departemen $departemen): RedirectResponse
    {
        if ($departemen->posisi()->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus departemen yang masih memiliki posisi terkait.');
        }

        $departemen->deleted_by = Auth::id();
        $departemen->save();
        $departemen->delete();

        return redirect()->route('admin.departemen.index')
            ->with($this::FLASH_SUCCESS, 'Data departemen berhasil dihapus.');
    }
}
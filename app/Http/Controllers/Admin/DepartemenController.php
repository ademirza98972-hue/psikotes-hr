<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartemenController extends Controller
{
    public function index(): View
    {
        $departemen = Departemen::withCount('posisi')->orderBy('nama_departemen')->paginate(10);
        return view('admin.departemen.index', compact('departemen'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:100|unique:departemen,nama_departemen',
        ]);

        Departemen::create($request->only('nama_departemen'));

        return redirect()->route('admin.departemen.index')
            ->with('sukses', 'Data departemen berhasil ditambahkan.');
    }

    public function update(Request $request, Departemen $departemen): RedirectResponse
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:100|unique:departemen,nama_departemen,'.$departemen->id,
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
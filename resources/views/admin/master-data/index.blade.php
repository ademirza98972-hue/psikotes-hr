@extends('layouts.admin', ['judulHalaman' => 'Data Master'])

@section('content')
@php
    $departemenMap = $departemen->mapWithKeys(function ($item) {
        return [$item->id => $item->nama_departemen];
    });
    $posisiMap = $posisi->mapWithKeys(function ($item) {
        return [$item->id => ['nama' => $item->nama_posisi, 'departemen_id' => $item->departemen_id]];
    });
@endphp

<div class="space-y-6" x-data="masterData(@js($departemenMap), @js($posisiMap))">

    {{-- DEPARTEMEN --}}
    <div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Daftar Departemen</h2>
                <p class="text-xs text-slate-500">Batas maksimal 100 karakter.</p>
            </div>

            @can('master_data.kelola')
                <button @click="openTambahDepartemen()"
                        class="rounded-md bg-[#2C5F6F] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#234853]">
                    + Tambah
                </button>
            @endcan
        </div>

        @if ($errors->any('departemen'))
            <div class="mb-4 rounded-md border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all('departemen') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($departemen->isEmpty())
            <p class="py-8 text-center text-sm text-slate-500">Belum ada data departemen. Klik "Tambah" untuk menambahkan data.</p>
        @else
            <form method="GET" action="{{ route('admin.master-data.index') }}" class="mb-4">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari departemen..."
                       class="w-full rounded-md border border-slate-300 px-3 py-1.5 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-[#F9FAFB]">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Nama Departemen</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Jumlah Posisi</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($departemen as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $item->nama_departemen }}</td>
                                <td class="px-4 py-3">{{ $item->posisi_count }}</td>
                                <td class="px-4 py-3 text-right">
                                    @can('master_data.kelola')
                                        <button type="button"
                                                @click='openEditDepartemen({{ $item->id }})'
                                                class="text-indigo-600 hover:text-indigo-700">Edit</button>
                                        <form method="POST" action="{{ route('admin.departemen.destroy', $item->id) }}" class="inline" onsubmit="return confirm('Hapus departemen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="ml-2 text-rose-600 hover:text-rose-700">Hapus</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $departemen->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- POSISI --}}
    <div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Daftar Posisi</h2>
                <p class="text-xs text-slate-500">Batas maksimal 100 karakter.</p>
            </div>

            @can('master_data.kelola')
                <button @click="openTambahPosisi()"
                        class="rounded-md bg-[#2C5F6F] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#234853]">
                    + Tambah
                </button>
            @endcan
        </div>

        @if ($errors->any('posisi'))
            <div class="mb-4 rounded-md border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all('posisi') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($posisi->isEmpty())
            <p class="py-8 text-center text-sm text-slate-500">Belum ada data posisi. Klik "Tambah" untuk menambahkan data.</p>
        @else
            <form method="GET" action="{{ route('admin.master-data.index') }}" class="mb-4">
                <input type="text" name="q_posisi" value="{{ request('q_posisi') }}" placeholder="Cari posisi..."
                       class="w-full rounded-md border border-slate-300 px-3 py-1.5 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-[#F9FAFB]">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Nama Posisi</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Departemen</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($posisi as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">{{ $item->nama_posisi }}</td>
                                <td class="px-4 py-3">{{ $item->departemen->nama_departemen ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    @can('master_data.kelola')
                                        <button type="button"
                                                @click='openEditPosisi({{ $item->id }})'
                                                class="text-indigo-600 hover:text-indigo-700">Edit</button>
                                        <form method="POST" action="{{ route('admin.posisi.destroy', $item->id) }}" class="inline" onsubmit="return confirm('Hapus posisi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="ml-2 text-rose-600 hover:text-rose-700">Hapus</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $posisi->onEachSide(1)->links() }}
            </div>
        @endif
    </div>

</div>

{{-- MODAL TAMBAH DEPARTEMEN --}}
<div id="modal-departemen" class="fixed inset-0 z-50 hidden" x-data>
    <div class="fixed inset-0 bg-black/50" @click="closeModal('modal-departemen')"></div>
    <div class="fixed left-1/2 top-1/2 z-50 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="w-full rounded-lg bg-white shadow-lg">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-slate-900">Tambah Departemen</h3>
                <p class="text-xs text-slate-500">Batas maksimal 100 karakter.</p>
            </div>

            @if ($errors->has('departemen'))
                <div class="m-4 rounded-md border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all('departemen') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.departemen.store') }}" class="m-4 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Departemen</label>
                    <input type="text" name="nama_departemen" required maxlength="100"
                           placeholder="Contoh: HR & GA"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="closeModal('modal-departemen')"
                            class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</button>
                    <button type="submit"
                            class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white hover:bg-[#234853]">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT DEPARTEMEN --}}
<div id="modal-edit-departemen" class="fixed inset-0 z-50 hidden" x-data>
    <div class="fixed inset-0 bg-black/50" @click="closeModal('modal-edit-departemen')"></div>
    <div class="fixed left-1/2 top-1/2 z-50 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="w-full rounded-lg bg-white shadow-lg">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-slate-900">Edit Departemen</h3>
                <p class="text-xs text-slate-500">Batas maksimal 100 karakter.</p>
            </div>

            <form method="POST" id="form-edit-departemen" class="m-4 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Departemen</label>
                    <input type="text" name="nama_departemen" required maxlength="100"
                           id="edit-departemen-nama"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="closeModal('modal-edit-departemen')"
                            class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</button>
                    <button type="submit"
                            class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white hover:bg-[#234853]">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH POSISI --}}
<div id="modal-posisi" class="fixed inset-0 z-50 hidden" x-data>
    <div class="fixed inset-0 bg-black/50" @click="closeModal('modal-posisi')"></div>
    <div class="fixed left-1/2 top-1/2 z-50 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="w-full rounded-lg bg-white shadow-lg">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-slate-900">Tambah Posisi</h3>
                <p class="text-xs text-slate-500">Batas maksimal 100 karakter.</p>
            </div>

            @if ($errors->has('posisi'))
                <div class="m-4 rounded-md border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all('posisi') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.posisi.store') }}" class="m-4 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Posisi</label>
                    <input type="text" name="nama_posisi" required maxlength="100"
                           placeholder="Contoh: Staff Administrasi"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Departemen</label>
                    <select name="departemen_id" required
                            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach(\App\Models\Departemen::orderBy('nama_departemen')->get() as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="closeModal('modal-posisi')"
                            class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</button>
                    <button type="submit"
                            class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white hover:bg-[#234853]">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT POSISI --}}
<div id="modal-edit-posisi" class="fixed inset-0 z-50 hidden" x-data>
    <div class="fixed inset-0 bg-black/50" @click="closeModal('modal-edit-posisi')"></div>
    <div class="fixed left-1/2 top-1/2 z-50 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="w-full rounded-lg bg-white shadow-lg">
            <div class="p-4">
                <h3 class="text-lg font-semibold text-slate-900">Edit Posisi</h3>
                <p class="text-xs text-slate-500">Batas maksimal 100 karakter.</p>
            </div>

            <form method="POST" id="form-edit-posisi" class="m-4 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Posisi</label>
                    <input type="text" name="nama_posisi" required maxlength="100"
                           id="edit-posisi-nama"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Departemen</label>
                    <select name="departemen_id" required
                            id="edit-posisi-departemen"
                            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach(\App\Models\Departemen::orderBy('nama_departemen')->get() as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="closeModal('modal-edit-posisi')"
                            class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</button>
                    <button type="submit"
                            class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white hover:bg-[#234853]">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function masterData(departemenMap, posisiMap) {
    var baseDepartemen = '/admin/departemen';
    var basePosisi = '/admin/posisi';
    return {
        closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        },
        openTambahDepartemen() {
            document.getElementById('form-edit-departemen').action = baseDepartemen;
            document.getElementById('edit-departemen-nama').value = '';
            document.getElementById('modal-departemen').classList.remove('hidden');
        },
        openEditDepartemen(id) {
            var nama = departemenMap[id];
            document.getElementById('form-edit-departemen').action = baseDepartemen + '/' + id;
            document.getElementById('edit-departemen-nama').value = nama;
            document.getElementById('modal-edit-departemen').classList.remove('hidden');
        },
        openTambahPosisi() {
            document.getElementById('form-edit-posisi').action = basePosisi;
            document.getElementById('edit-posisi-nama').value = '';
            document.getElementById('edit-posisi-departemen').value = '';
            document.getElementById('modal-posisi').classList.remove('hidden');
        },
        openEditPosisi(id) {
            var item = posisiMap[id];
            document.getElementById('form-edit-posisi').action = basePosisi + '/' + id;
            document.getElementById('edit-posisi-nama').value = item.nama;
            document.getElementById('edit-posisi-departemen').value = item.dept;
            document.getElementById('modal-edit-posisi').classList.remove('hidden');
        }
    };
}
</script>
@endsection


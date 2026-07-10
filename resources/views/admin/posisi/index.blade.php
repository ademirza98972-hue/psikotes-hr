@extends('layouts.admin', ['judulHalaman' => 'Data Posisi'])

@section('content')
<div x-data="posisiPage()">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Data Posisi</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola daftar posisi/jabatan perusahaan</p>
        </div>
        <button @click="openModal()"
                class="rounded-lg bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1a4550] transition">
            + Tambah Posisi
        </button>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-slate-700">Posisi</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-700">Departemen</th>
                    <th class="text-right px-4 py-3 font-semibold text-slate-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posisi as $item)
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3">{{ $item->nama_posisi }}</td>
                    <td class="px-4 py-3">{{ $item->departemen ? $item->departemen->nama_departemen : '-' }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex gap-2">
                            <button @click="openModal({{ $item->id }}, '{{ addslashes($item->nama_posisi) }}', {{ $item->departemen_id ?? 'null' }})"
                                    class="rounded bg-blue-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Ubah</button>
                            <button onclick="confirmHapus('{{ $item->id }}', '{{ addslashes($item->nama_posisi) }}')"
                                    class="rounded bg-red-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700">Hapus</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $posisi->links() }}
    </div>

    {{-- Modal --}}
    <template x-teleport="body">
        <div x-show="showModal"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
             @click.self="showModal=false"
             x-cloak>
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl"
                 @keydown.escape.window="showModal=false">
                <h3 class="mb-4 text-base font-semibold">Tambah / Ubah Posisi</h3>
                <form :action="formAction" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <template x-if="editId">
                        <input type="hidden" name="_method" value="PATCH">
                    </template>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Departemen</label>
                            <select name="departemen_id" x-model="selectedDepartemenId" required
                                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departemen as $dp)
                                <option value="{{ $dp->id }}">{{ $dp->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Nama Posisi</label>
                            <input type="text" name="nama_posisi" x-model="nama" required
                                   class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                            <template x-if="errors.nama_posisi">
                                <span class="mt-1 block text-xs text-rose-600" x-text="errors.nama_posisi[0]"></span>
                            </template>
                        </div>
                    </div>

                    <div class="mt-5 flex gap-2 justify-end">
                        <button type="button" @click="showModal=false"
                                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-lg bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1a4550] transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

<script>
function posisiPage() {
    return {
        showModal: false,
        editId: null,
        nama: '',
        selectedDepartemenId: '',
        errors: {},
        get formAction() {
            return this.editId ? `/admin/posisi/${this.editId}` : '/admin/posisi';
        },
        openModal(id = null, nm = '', deptId = '') {
            this.editId = id;
            this.nama = nm;
            this.selectedDepartemenId = String(deptId);
            this.errors = {};
            this.showModal = true;
        },
    }
}

function confirmHapus(id, nama) {
    if (confirm(`Apakah Anda yakin ingin menghapus posisi "${nama}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/posisi/${id}`;
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>[x-cloak] { display: none !important; }</style>
@endsection

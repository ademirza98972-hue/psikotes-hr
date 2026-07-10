@extends('layouts.admin', ['judulHalaman' => 'Data Departemen'])

@section('content')
<div x-data="departemenPage()">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Data Departemen</h2>
            <p class="mt-1 text-sm text-slate-500">Kelola daftar departemen perusahaan</p>
        </div>
        <button @click="openModal('tambah')"
                class="rounded-lg bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1a4550] transition">
            + Tambah Departemen
        </button>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-slate-700">Nama Departemen</th>
                    <th class="text-center px-4 py-3 font-semibold text-slate-700">Jumlah Posisi</th>
                    <th class="text-right px-4 py-3 font-semibold text-slate-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departemen as $item)
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3">{{ $item->nama_departemen }}</td>
                    <td class="px-4 py-3 text-center">{{ $item->posisi_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex gap-2">
                            <button @click="openModal('edit', {{ $item->id }}, '{{ addslashes($item->nama_departemen) }}')"
                                    class="rounded bg-blue-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Ubah</button>
                            <button onclick="confirmHapus('{{ $item->id }}', '{{ addslashes($item->nama_departemen) }}')"
                                    class="rounded bg-red-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada data departemen.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $departemen->links() }}
    </div>

    {{-- Modal --}}
    <template x-teleport="body">
        <div x-show="showModal"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
             @click.self="showModal=false"
             x-cloak>
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl"
                 @keydown.escape.window="showModal=false">
                <h3 class="mb-4 text-base font-semibold" x-text="mode === 'tambah' ? 'Tambah Departemen' : 'Ubah Departemen'"></h3>
                <form :action="formAction" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <template x-if="mode === 'edit'">
                        <input type="hidden" name="_method" value="PATCH">
                    </template>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Nama Departemen</label>
                        <input type="text" name="nama_departemen" x-model="nama" required
                               class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                        <template x-if="errors.nama_departemen">
                            <span class="mt-1 block text-xs text-rose-600" x-text="errors.nama_departemen[0]"></span>
                        </template>
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
function departemenPage() {
    return {
        showModal: false,
        mode: 'tambah',
        editId: null,
        nama: '',
        errors: {},
        get formAction() {
            return this.mode === 'tambah' ? '/admin/departemen' : `/admin/departemen/${this.editId}`;
        },
        openModal(m, id = null, nm = '') {
            this.mode = m;
            this.editId = id;
            this.nama = nm;
            this.errors = {};
            this.showModal = true;
        },
    }
}

function confirmHapus(id, nama) {
    if (confirm(`Apakah Anda yakin ingin menghapus departemen "${nama}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/departemen/${id}`;
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>[x-cloak] { display: none !important; }</style>
@endsection

@extends('layouts.admin', ['judulHalaman' => 'Data Departemen'])

@section('content')
<div x-data="departemenPage()">
    <div class="w-full rounded-lg border border-slate-200 bg-white px-6 pt-3 pb-4 shadow-sm">

        {{-- FILTER BAR --}}
        <form method="GET" action="{{ route('admin.departemen.index') }}" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-1 flex-wrap items-end gap-2">
                {{-- Search --}}
                <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama departemen..."
                    class="block w-56 rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">

                <button type="submit" class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                    Terapkan Filter
                </button>

                @if ($kataKunci)
                    <a href="{{ route('admin.departemen.index') }}"
                        class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 whitespace-nowrap">
                        Reset Filter
                    </a>
                @endif
            </div>

            @auth
                @if(auth()->user()->hasIzin('master_data.kelola'))
                    <button type="button" @click="openModal('tambah')"
                            class="self-start rounded-lg bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1a4550] transition whitespace-nowrap">
                        + Tambah Departemen
                    </button>
                @endif
            @endauth
        </form>

        {{-- INDICATOR --}}
        <div class="mb-2 text-xs text-slate-500">
            Menampilkan <strong>{{ $departemen->firstItem() ?? $departemen->total() }}</strong> dari <strong>{{ $departemen->total() }}</strong> departemen
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Nama Departemen</th>
                        <th class="px-4 py-3 text-center">Jumlah Posisi</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($departemen as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $item->nama_departemen }}</td>
                            <td class="px-4 py-3 text-center text-slate-600">{{ $item->posisi_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex justify-end gap-2">
                                    @auth
                                        @if(auth()->user()->hasIzin('master_data.kelola'))
                                            <button type="button"
                                                    @click="openModal('edit', {{ $item->id }}, '{{ addslashes($item->nama_departemen) }}')"
                                                    class="rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Ubah</button>
                                            <button type="button"
                                                    onclick="confirmHapus('{{ $item->id }}', '{{ addslashes($item->nama_departemen) }}')"
                                                    class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-red-700">Hapus</button>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center">
                                @if ($kataKunci)
                                    <p class="text-sm text-slate-500">Tidak ada departemen yang cocok dengan pencarian "{{ $kataKunci }}".</p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.departemen.index') }}"
                                            class="inline-block rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">
                                            Reset Filter
                                        </a>
                                    </div>
                                @else
                                    <p class="text-sm text-slate-500">Belum ada data departemen.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($departemen->hasPages())
            <div class="mt-4">
                {{ $departemen->links() }}
            </div>
        @endif
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

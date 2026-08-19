@extends('layouts.admin', ['judulHalaman' => 'Data Posisi'])

@section('content')
<div x-data="posisiPage()">

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-[#191c1e]">Data Posisi</h1>
                <p class="mt-1 text-sm text-[#40484b]">Kelola daftar posisi dan jabatan dalam setiap departemen.</p>
            </div>
        </div>
        {{-- FILTER BAR --}}
        <form method="GET" action="{{ route('admin.posisi.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-1 flex-wrap items-end gap-2">
                {{-- Search --}}
                <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama posisi..."
                    class="block w-56 rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">

                {{-- Filter Departemen --}}
                <select name="departemen"
                    class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <option value="">Semua Departemen</option>
                    @foreach ($departemen as $d)
                        <option value="{{ $d->id }}" @if($filterDepartemen == $d->id) selected @endif>{{ $d->nama_departemen }}</option>
                    @endforeach
                </select>

                <button type="submit" class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                    Terapkan Filter
                </button>

                @if ($kataKunci || $filterDepartemen)
                    <a href="{{ route('admin.posisi.index') }}"
                        class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 whitespace-nowrap">
                        Reset Filter
                    </a>
                @endif
            </div>

            @auth
                @if(auth()->user()->hasIzin('master_data.kelola'))
                    <button type="button" @click="openModal()"
                            class="self-start rounded-lg bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1a4550] transition whitespace-nowrap">
                        + Tambah Posisi
                    </button>
                @endif
            @endauth
        </form>
    </div>{{-- /STICKY HEADER --}}

    <div class="mt-4 w-full rounded-lg border border-slate-200 bg-white px-6 pt-3 pb-4 shadow-sm">

        <div style="overflow: auto; max-height: calc(100vh - 300px);">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead style="position: sticky; top: 0; z-index: 5;">
                    <tr style="background: #0f2230; border-bottom: 1px solid #0a1a25;">
                        <th class="px-4 py-3" style="color: #7db8c2;">Posisi</th>
                        <th class="px-4 py-3" style="color: #7db8c2;">Departemen</th>
                        <th class="px-4 py-3 text-right" style="color: #7db8c2;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($posisi as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $item->nama_posisi }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $item->departemen ? $item->departemen->nama_departemen : '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex justify-end gap-2">
                                    @auth
                                        @if(auth()->user()->hasIzin('master_data.kelola'))
                                            <button type="button"
                                                    @click="openModal({{ $item->id }}, '{{ addslashes($item->nama_posisi) }}', {{ $item->departemen_id ?? 'null' }})"
                                                    class="rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Ubah</button>
                                            <button type="button"
                                                    onclick="confirmHapus('{{ $item->id }}', '{{ addslashes($item->nama_posisi) }}')"
                                                    class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-red-700">Hapus</button>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center">
                                @if ($kataKunci || $filterDepartemen)
                                    <p class="text-sm text-slate-500">Tidak ada posisi yang cocok dengan filter aktif.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.posisi.index') }}"
                                            class="inline-block rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">
                                            Reset Filter
                                        </a>
                                    </div>
                                @else
                                    <p class="text-sm text-slate-500">Belum ada data posisi.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4 flex items-center justify-between gap-4">
            <p class="text-[11px] text-[#40484b]">
                Menampilkan <span class="font-semibold text-[#191c1e]">{{ $posisi->firstItem() ?? $posisi->total() }}</span> -
                <span class="font-semibold text-[#191c1e]">{{ $posisi->lastItem() ?? $posisi->total() }}</span> dari
                <span class="font-semibold text-[#191c1e]">{{ $posisi->total() }}</span> posisi
            </p>
            @if ($posisi->hasPages())
                {{ $posisi->links() }}
            @endif
        </div>
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

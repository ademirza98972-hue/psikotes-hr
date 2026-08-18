@extends('layouts.admin', ['judulHalaman' => 'Data Departemen'])

@section('content')
<div x-data="departemenPage()">

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-[#191c1e]">Data Departemen</h1>
                <p class="mt-1 text-sm text-[#40484b]">Kelola daftar departemen yang tersedia dalam sistem.</p>
            </div>
        </div>
        {{-- FILTER BAR --}}
        <form method="GET" action="{{ route('admin.departemen.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-1 flex-wrap items-end gap-2">
                <div class="relative flex-1 min-w-[200px] max-w-sm">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#71787b] text-[18px]">search</span>
                    <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama departemen..."
                        class="block w-full rounded-lg border border-[#c0c8cb] pl-10 pr-4 py-2.5 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]">
                </div>

                <button type="submit" class="rounded-lg bg-[#2C5F6F] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#1a4550] transition whitespace-nowrap">
                    Terapkan Filter
                </button>

                @if ($kataKunci)
                    <a href="{{ route('admin.departemen.index') }}"
                        class="rounded-lg border border-[#c0c8cb] px-4 py-2.5 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] whitespace-nowrap">
                        Reset Filter
                    </a>
                @endif
            </div>

            @auth
                @if(auth()->user()->hasIzin('master_data.kelola'))
                    <button type="button" @click="openModal('tambah')"
                            class="rounded-lg bg-[#2C5F6F] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all active:scale-95 flex items-center gap-2 whitespace-nowrap">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Tambah Departemen
                    </button>
                @endif
            @endauth
        </form>
    </div>{{-- /STICKY HEADER --}}

    <div class="mt-4 w-full rounded-xl border border-[#c0c8cb] bg-white overflow-hidden shadow-sm">

        {{-- INDICATOR --}}
        <div class="px-6 pb-3 text-xs text-[#40484b]">
            Menampilkan <strong>{{ $departemen->firstItem() ?? $departemen->total() }}</strong> dari <strong>{{ $departemen->total() }}</strong> departemen
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#f2f4f6]">
                    <tr>
                        <th class="px-6 py-4 font-[11px] uppercase tracking-widest text-[#40484b]">NAMA DEPARTEMEN</th>
                        <th class="px-6 py-4 font-[11px] uppercase tracking-widest text-[#40484b] text-center">JUMLAH POSISI</th>
                        <th class="px-6 py-4 font-[11px] uppercase tracking-widest text-[#40484b] text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c0c8cb]/30 text-sm">
                    @forelse ($departemen as $item)
                        <tr class="hover:bg-[#f2f4f6]/50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-[#191c1e]">{{ $item->nama_departemen }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border bg-[#d4e3ff]/30 text-[#39485e] border-[#c0c8cb]/50">
                                    {{ $item->posisi_count }} posisi
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex justify-end gap-2">
                                    @auth
                                        @if(auth()->user()->hasIzin('master_data.kelola'))
                                            <button type="button"
                                                    @click="openModal('edit', {{ $item->id }}, '{{ addslashes($item->nama_departemen) }}')"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-[#2C5F6F] px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:opacity-90 transition-all active:scale-95">
                                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                                Ubah
                                            </button>
                                            <button type="button"
                                                    onclick="confirmHapus('{{ $item->id }}', '{{ addslashes($item->nama_departemen) }}')"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-[#ba1a1a] px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:opacity-90 transition-all active:scale-95">
                                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                                Hapus
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center">
                                @if ($kataKunci)
                                    <p class="text-sm text-[#40484b]">Tidak ada departemen yang cocok dengan pencarian "{{ $kataKunci }}".</p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.departemen.index') }}"
                                            class="inline-block rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6]">
                                            Reset Filter
                                        </a>
                                    </div>
                                @else
                                    <p class="text-sm text-[#40484b]">Belum ada data departemen.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($departemen->hasPages())
            <div class="px-6 py-4 bg-[#f2f4f6] flex items-center justify-between border-t border-[#c0c8cb]">
                <p class="text-xs text-[#40484b]">Menampilkan {{ $departemen->firstItem() ?? 1 }} - {{ min($departemen->lastItem() ?? $departemen->total(), $departemen->total()) }} dari {{ $departemen->total() }} departemen</p>
                <div class="flex items-center gap-1">
                    {{ $departemen->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL --}}
    <template x-teleport="body">
        <div x-show="showModal"
             class="fixed inset-0 z-50 flex items-center justify-center bg-[#2d3133]/40 backdrop-blur-sm"
             @click.self="showModal=false"
             x-cloak>
            <div class="w-full max-w-[440px] rounded-xl bg-white shadow-2xl border border-[#c0c8cb] mx-4"
                 @keydown.escape.window="showModal=false">
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-[#c0c8cb] flex items-center justify-between">
                    <h3 class="text-base font-semibold text-[#191c1e]" x-text="mode === 'tambah' ? 'Tambah Departemen' : 'Ubah Departemen'"></h3>
                    <button type="button" @click="showModal=false" class="text-[#40484b] hover:text-[#ba1a1a] transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-5">
                    <form id="departemen-form" :action="formAction" method="POST" class="space-y-4">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <template x-if="mode === 'edit'">
                            <input type="hidden" name="_method" value="PATCH">
                        </template>
                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-widest text-[#40484b] mb-1.5">Nama Departemen</label>
                            <input type="text" name="nama_departemen" x-model="nama" required
                                   class="block w-full rounded-lg border border-[#c0c8cb] px-4 py-3 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]">
                            <template x-if="errors.nama_departemen">
                                <span class="mt-1 block text-xs text-rose-600" x-text="errors.nama_departemen[0]"></span>
                            </template>
                        </div>

                        <template x-if="mode === 'edit'">
                            <div class="bg-[#f2f4f6] rounded-lg p-3 flex items-start gap-3">
                                <span class="material-symbols-outlined text-[#2C5F6F] text-[18px] mt-0.5">info</span>
                                <p class="text-xs text-[#40484b]">
                                    Perubahan nama departemen akan otomatis diperbarui pada seluruh profil karyawan yang terhubung.
                                </p>
                            </div>
                        </template>
                    </form>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-[#f2f4f6] rounded-b-xl flex justify-end gap-3">
                    <button type="button" @click="showModal=false"
                            class="rounded-lg border border-[#c0c8cb] px-5 py-2.5 text-sm font-medium text-[#40484b] hover:bg-white transition-all active:scale-95">
                        Batal
                    </button>
                    <button type="submit" form="departemen-form"
                            class="rounded-lg bg-[#2C5F6F] px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90 transition-all active:scale-95 shadow-sm">
                        Simpan
                    </button>
                </div>
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

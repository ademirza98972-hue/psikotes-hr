@extends('layouts.admin', ['judulHalaman' => 'Kelola Peran & Izin'])

@section('content')
<div x-data="{
    modalHapus: false, idHapus: null, namaHapus: '',
    modalTambah: @js(($openModal ?? '') === 'tambah' || ($errors->any() && old('_form') === 'tambah')),
    modalUbah:   @js(($errors->any() && old('_form') === 'ubah')),
    editData: @js($errors->any() && old('_form') === 'ubah'
        ? ['id' => (int) old('edit_id', 0), 'nama_peran' => old('nama_peran', ''), 'deskripsi' => old('deskripsi', ''), 'izin_ids' => array_map('intval', (array) old('izin', []))]
        : ['id' => 0, 'nama_peran' => '', 'deskripsi' => '', 'izin_ids' => []]),
    openEdit(d) {
        this.editData = d;
        this.modalUbah = true;
        this.$nextTick(() => {
            document.querySelectorAll('#modal-ubah-izin input[type=checkbox][name=\'izin[]\']').forEach(el => {
                el.checked = d.izin_ids.includes(parseInt(el.value));
            });
        });
    },
    centangSemua(containerId) {
        document.querySelectorAll('#' + containerId + ' input[type=checkbox][name=\'izin[]\']:not(:disabled)').forEach(el => el.checked = true);
    },
    batalkanSemua(containerId) {
        document.querySelectorAll('#' + containerId + ' input[type=checkbox][name=\'izin[]\']:not(:disabled)').forEach(el => el.checked = false);
    },
    centangSemuaKelompok(id) {
        document.querySelectorAll('#' + id + ' input[type=checkbox][name=\'izin[]\']:not(:disabled)').forEach(el => el.checked = true);
    },
    batalkanSemuaKelompok(id) {
        document.querySelectorAll('#' + id + ' input[type=checkbox][name=\'izin[]\']:not(:disabled)').forEach(el => el.checked = false);
    },
}">

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-0">
        <div>
            <nav class="flex items-center gap-2 mb-2 text-[#40484b] text-sm">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#2C5F6F] transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <span class="font-semibold text-[#2C5F6F]">Kelola Peran & Izin</span>
            </nav>
            <h2 class="text-[32px] leading-[40px] font-bold text-[#001a22]">Kelola Peran & Izin</h2>
            <p class="text-[14px] leading-[20px] text-[#40484b] mt-1">Kelola hak akses dan tanggung jawab pengguna dalam sistem.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ route('admin.peran.index') }}" class="flex items-center gap-2">
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#40484b] text-[20px] transition-colors group-focus-within:text-[#2C5F6F]">search</span>
                    <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama peran..."
                        class="pl-10 pr-4 py-2.5 w-64 bg-white border border-[#c0c8cb] rounded-xl focus:ring-2 focus:ring-[#2C5F6F] focus:border-[#2C5F6F] outline-none text-[14px] leading-[20px] transition-all shadow-sm">
                </div>
                <button type="submit" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-[#c0c8cb] rounded-xl text-[12px] leading-[16px] font-medium text-[#191c1e] hover:bg-[#f2f4f6] transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">filter_list</span>
                    Terapkan Filter
                </button>
                @if ($kataKunci)
                    <a href="{{ route('admin.peran.index') }}"
                        class="px-4 py-2.5 bg-white border border-[#c0c8cb] rounded-xl text-[12px] leading-[16px] font-medium text-[#191c1e] hover:bg-[#f2f4f6] transition-colors shadow-sm">
                        Reset
                    </a>
                @endif
            </form>
            @auth
                @if(auth()->user()->hasIzin('peran.kelola'))
                    <button type="button" @click="modalTambah = true"
                       class="flex items-center gap-2 px-5 py-2.5 bg-[#2C5F6F] text-white rounded-xl text-[12px] leading-[16px] font-medium hover:opacity-90 active:scale-[0.98] transition-all shadow-md shadow-[#2C5F6F]/20">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        Tambah Peran
                    </button>
                @endif
            @endauth
        </div>
    </div>

    </div>{{-- /STICKY HEADER --}}

    {{-- Warning Info --}}
    <div class="mb-4 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 flex items-start gap-3">
        <span class="material-symbols-outlined text-[#d97706] text-[20px] mt-0.5 shrink-0">warning</span>
        <div>
            <p class="text-sm font-medium text-amber-800">Perubahan izin berlaku langsung</p>
            <p class="text-xs text-amber-700 mt-0.5">Perubahan hak akses pada peran akan langsung mempengaruhi semua pengguna dengan peran tersebut.</p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white border border-[#c0c8cb] rounded-xl overflow-hidden shadow-sm">
        <div style="overflow: auto; max-height: calc(100vh - 300px);">
            <table class="w-full text-left border-collapse">
                <thead style="position: sticky; top: 0; z-index: 5;">
                    <tr style="background: #0f2230; border-bottom: 1px solid #0a1a25;">
                        <th class="px-4 py-3.5 text-[11px] font-bold uppercase tracking-wider text-center" style="color:#7db8c2;width:52px">No.</th>
                        <th class="px-6 py-4 font-[11px] leading-[16px] font-semibold uppercase tracking-[0.05em]" style="color: #7db8c2;">Nama Peran</th>
                        <th class="px-6 py-4 font-[11px] leading-[16px] font-semibold uppercase tracking-[0.05em]" style="color: #7db8c2;">Deskripsi</th>
                        <th class="px-6 py-4 font-[11px] leading-[16px] font-semibold uppercase tracking-[0.05em] text-center" style="color: #7db8c2;">Jumlah Izin</th>
                        <th class="px-6 py-4 font-[11px] leading-[16px] font-semibold uppercase tracking-[0.05em] text-center" style="color: #7db8c2;">Jumlah Pengguna</th>
                        <th class="px-6 py-4 font-[11px] leading-[16px] font-semibold uppercase tracking-[0.05em] text-right" style="color: #7db8c2;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c0c8cb]/30">
                    @forelse ($peran as $p)
                        <tr class="hover:bg-[#f2f4f6]/50 transition-colors group">
                            <td class="px-4 py-4 text-sm text-[#71787c] tabular-nums text-center">{{ ($peran->currentPage() - 1) * $peran->perPage() + $loop->iteration }}</td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <span class="text-[18px] leading-[24px] font-semibold text-[#001a22]">{{ $p->nama_peran }}</span>
                                    @if ($p->nama_peran === 'Super Admin')
                                        <span class="px-2 py-0.5 rounded-md bg-purple-50 border border-purple-200 text-[10px] font-bold text-purple-700 uppercase tracking-tight">Protected</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 max-w-xs">
                                <p class="text-[14px] leading-[20px] text-[#40484b] line-clamp-2">{{ $p->deskripsi ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#eceef0] text-[#001a22] font-semibold text-[14px] leading-[20px]">{{ $p->izin_count }}</span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if ($p->pengguna_count > 0)
                                    <div class="flex items-center justify-center -space-x-2">
                                        @foreach($p->pengguna()->limit(3)->get() as $user)
                                            <div class="w-8 h-8 rounded-full border-2 border-white {{ $loop->first ? 'bg-[#c0e9f9]' : 'bg-[#eceef0]' }} flex items-center justify-center overflow-hidden">
                                                @if($user->foto_profil)
                                                    <img src="{{ asset('storage/' . $user->foto_profil) }}" class="w-full h-full object-cover" alt="">
                                                @else
                                                    <span class="text-[10px] font-bold text-[#39485e]">{{ strtoupper(mb_substr(explode(' ', $user->name)[0], 0, 2)) }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if ($p->pengguna_count > 3)
                                            <div class="w-8 h-8 rounded-full border-2 border-white bg-[#eceef0] text-[10px] font-medium text-[#40484b] flex items-center justify-center">
                                                +{{ $p->pengguna_count - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[11px] font-bold text-[#40484b] bg-[#eceef0] rounded-lg px-2 py-1">0 Users</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @auth
                                        @if(auth()->user()->hasIzin('peran.kelola'))
                                            <button type="button"
                                                    @click="openEdit({ id: {{ $p->id }}, nama_peran: @js($p->nama_peran), deskripsi: @js($p->deskripsi ?? ''), izin_ids: @js($p->izin->pluck('id')->values()->all()) })"
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                                <span class="material-symbols-outlined text-[20px]">edit_square</span>
                                            </button>
                                            @if ($p->nama_peran === 'Super Admin')
                                                <button type="button" disabled
                                                        title="Peran Super Admin tidak dapat dihapus."
                                                        class="p-2 text-[#40484b] cursor-not-allowed" title="System protected">
                                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                                </button>
                                            @else
                                                <button type="button"
                                                        @click="modalHapus = true; idHapus = {{ $p->id }}; namaHapus = '{{ addslashes($p->nama_peran) }}'"
                                                        class="p-2 text-[#ba1a1a] hover:bg-[#fef2f2] rounded-lg transition-colors" title="Hapus">
                                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                                </button>
                                            @endif
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                @if ($kataKunci)
                                    <span class="material-symbols-outlined block mb-3" style="font-size:40px; color:#c0c8cb;">search_off</span>
                                    <p class="text-[14px] font-medium text-[#40484b] mb-1">Tidak ada hasil yang cocok</p>
                                    <p class="text-[12px] text-[#71787c] mb-4">Coba ubah kata kunci atau hapus filter aktif.</p>
                                    <a href="{{ route('admin.peran.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                        Reset Filter
                                    </a>
                                @else
                                    <span class="material-symbols-outlined block mb-3" style="font-size:40px; color:#c0c8cb;">key</span>
                                    <p class="text-[14px] font-medium text-[#40484b] mb-1">Belum Ada Data Peran</p>
                                    <p class="text-[12px] text-[#71787c] mb-4">Buat peran baru untuk mengatur hak akses pengguna.</p>
                                    @auth
                                        @if(auth()->user()->hasIzin('peran.kelola'))
                                            <button type="button" @click="modalTambah = true" class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                                                <span class="material-symbols-outlined text-[16px]">add</span>
                                                Tambah Peran
                                            </button>
                                        @endif
                                    @endauth
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-[#c0c8cb] px-6 py-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-[12px] text-[#71787c]">
                Menampilkan
                <span class="font-semibold text-[#191c1e]">{{ $peran->firstItem() ?? 0 }}</span>–<span class="font-semibold text-[#191c1e]">{{ $peran->lastItem() ?? 0 }}</span>
                dari <span class="font-semibold text-[#191c1e]">{{ $peran->total() }}</span> data
            </p>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-purple-500"></div>
                        <span class="text-[11px] text-[#40484b] uppercase tracking-wider">System Role</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-[#2C5F6F]"></div>
                        <span class="text-[11px] text-[#40484b] uppercase tracking-wider">Custom Role</span>
                    </div>
                </div>
                @if ($peran->hasPages())
                    {{ $peran->links() }}
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Tambah Peran --}}
    <template x-teleport="body">
    <div x-show="modalTambah" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-[#191c1e]/60 px-4"
         style="backdrop-filter:blur(2px)">
        <div @click.outside="modalTambah = false"
             class="relative bg-[#f7f9fb] rounded-2xl shadow-2xl w-full flex flex-col"
             style="max-width:880px;">

            {{-- Header --}}
            <div class="flex items-center gap-3 px-6 py-4 border-b border-[#e0e3e5] shrink-0 bg-white rounded-t-2xl">
                <div class="w-9 h-9 rounded-xl bg-[#2C5F6F]/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px] text-[#2C5F6F]">shield_person</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-[15px] font-semibold text-[#001a22] leading-tight">Tambah Peran Baru</h3>
                    <p class="text-[12px] text-[#40484b] mt-0.5">Buat peran dan tentukan izin aksesnya.</p>
                </div>
                <button type="button" @click="modalTambah = false"
                        class="p-2 rounded-xl hover:bg-[#f2f4f6] text-[#40484b] transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.peran.simpan') }}" class="flex flex-col flex-1 min-h-0">
                @csrf
                <input type="hidden" name="_form" value="tambah">

                <div class="overflow-y-auto px-6 py-5 space-y-5" style="max-height:calc(90vh - 140px)">

                    {{-- Section: Informasi Peran --}}
                    <div class="bg-white rounded-xl border border-[#e0e3e5] shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-[#e0e3e5]">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Informasi Peran</p>
                        </div>
                        <div class="px-5 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Nama Peran <span class="text-rose-600">*</span></label>
                                <input name="nama_peran" type="text" value="{{ old('nama_peran') }}" required maxlength="255"
                                       placeholder="cth. HR Manager"
                                       class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 transition-all @error('nama_peran') border-rose-400 bg-rose-50 @enderror">
                                @error('nama_peran')<p class="mt-1 text-[12px] text-rose-600 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Deskripsi <span class="text-[12px] font-normal text-[#40484b]">(opsional)</span></label>
                                <textarea name="deskripsi" rows="3" maxlength="1000"
                                          placeholder="Deskripsi singkat peran ini..."
                                          class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 resize-none transition-all">{{ old('deskripsi') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Daftar Izin --}}
                    <div class="bg-white rounded-xl border border-[#e0e3e5] shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-[#e0e3e5] flex items-center justify-between">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Daftar Izin <span class="text-rose-500 ml-0.5">*</span></p>
                            <div class="flex gap-2">
                                <button type="button" @click="centangSemua('modal-tambah-izin')"
                                        class="inline-flex items-center gap-1 px-3 py-1 text-[11px] font-medium text-[#2C5F6F] border border-[#2C5F6F]/30 bg-[#2C5F6F]/5 rounded-lg hover:bg-[#2C5F6F]/10 transition-colors">
                                    <span class="material-symbols-outlined text-[13px]">check_circle</span> Centang Semua
                                </button>
                                <button type="button" @click="batalkanSemua('modal-tambah-izin')"
                                        class="inline-flex items-center gap-1 px-3 py-1 text-[11px] font-medium text-[#40484b] border border-[#c0c8cb] bg-white rounded-lg hover:bg-[#f2f4f6] transition-colors">
                                    <span class="material-symbols-outlined text-[13px]">cancel</span> Batalkan
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <div id="modal-tambah-izin" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach ($kelompokIzin as $labelKelompok => $daftar)
                                    @php $gId = 't-kg-' . \Illuminate\Support\Str::slug($labelKelompok); @endphp
                                    <div class="border border-[#e0e3e5] rounded-xl overflow-hidden hover:border-[#2C5F6F]/40 transition-colors">
                                        <div class="flex items-center justify-between px-4 py-2.5 bg-[#f7f9fb] border-b border-[#e0e3e5]">
                                            <p class="text-[11px] font-bold uppercase tracking-wider text-[#40484b]">{{ $labelKelompok }}</p>
                                            <div class="flex gap-1">
                                                <button type="button" @click="centangSemuaKelompok('{{ $gId }}')"
                                                        class="px-2 py-0.5 text-[10px] font-semibold text-[#2C5F6F] hover:bg-[#2C5F6F]/10 rounded-md transition-colors">+ Semua</button>
                                                <button type="button" @click="batalkanSemuaKelompok('{{ $gId }}')"
                                                        class="px-2 py-0.5 text-[10px] font-semibold text-[#40484b] hover:bg-[#f2f4f6] rounded-md transition-colors">− Semua</button>
                                            </div>
                                        </div>
                                        <div id="{{ $gId }}" class="divide-y divide-[#f2f4f6]">
                                            @foreach ($daftar as $izin)
                                                <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-[#f7f9fb] transition-colors select-none">
                                                    <input type="checkbox" name="izin[]" value="{{ $izin->id }}"
                                                           {{ in_array($izin->id, old('izin', [])) ? 'checked' : '' }}
                                                           class="w-4 h-4 rounded border-[#c0c8cb] text-[#2C5F6F] focus:ring-[#2C5F6F] shrink-0">
                                                    <span class="text-[13px] text-[#191c1e] leading-snug">{{ $izin->deskripsi }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-[#e0e3e5] px-6 py-4 flex justify-end gap-2 shrink-0 bg-white rounded-b-2xl">
                    <button type="button" @click="modalTambah = false"
                            class="rounded-xl border border-[#c0c8cb] px-5 py-2.5 text-[14px] font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2.5 text-[14px] font-semibold text-white hover:bg-[#1E414C] transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">save</span> Simpan Peran
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- Modal Ubah Peran --}}
    <template x-teleport="body">
    <div x-show="modalUbah" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-[#191c1e]/60 px-4"
         style="backdrop-filter:blur(2px)">
        <div @click.outside="modalUbah = false"
             class="relative bg-[#f7f9fb] rounded-2xl shadow-2xl w-full flex flex-col"
             style="max-width:880px;">

            {{-- Header --}}
            <div class="flex items-center gap-3 px-6 py-4 border-b border-[#e0e3e5] shrink-0 bg-white rounded-t-2xl">
                <div class="w-9 h-9 rounded-xl bg-[#2C5F6F]/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px] text-[#2C5F6F]">edit_square</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-[15px] font-semibold text-[#001a22] leading-tight">Ubah Peran</h3>
                    <p class="text-[12px] text-[#40484b] mt-0.5 truncate" x-text="editData.nama_peran"></p>
                </div>
                <button type="button" @click="modalUbah = false"
                        class="p-2 rounded-xl hover:bg-[#f2f4f6] text-[#40484b] transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form method="POST" :action="'{{ url('admin/peran') }}/' + editData.id" class="flex flex-col flex-1 min-h-0">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form" value="ubah">
                <input type="hidden" name="edit_id" :value="editData.id">

                <div class="overflow-y-auto px-6 py-5 space-y-5" style="max-height:calc(90vh - 140px)">

                    <div x-show="editData.nama_peran === 'Super Admin'"
                         class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 flex items-start gap-3">
                        <span class="material-symbols-outlined text-blue-600 text-[20px] mt-0.5 shrink-0">info</span>
                        <p class="text-[13px] text-blue-700">Izin <strong>"Kelola Peran"</strong> wajib tetap aktif untuk menjaga integritas Super Admin.</p>
                    </div>

                    {{-- Section: Informasi Peran --}}
                    <div class="bg-white rounded-xl border border-[#e0e3e5] shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-[#e0e3e5]">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Informasi Peran</p>
                        </div>
                        <div class="px-5 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Nama Peran <span class="text-rose-600">*</span></label>
                                <input name="nama_peran" type="text" x-model="editData.nama_peran" required maxlength="255"
                                       class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 transition-all @error('nama_peran') border-rose-400 bg-rose-50 @enderror">
                                @error('nama_peran')<p class="mt-1 text-[12px] text-rose-600 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Deskripsi <span class="text-[12px] font-normal text-[#40484b]">(opsional)</span></label>
                                <textarea name="deskripsi" rows="3" maxlength="1000" x-model="editData.deskripsi"
                                          class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 resize-none transition-all"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Daftar Izin --}}
                    <div class="bg-white rounded-xl border border-[#e0e3e5] shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-[#e0e3e5] flex items-center justify-between">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Daftar Izin <span class="text-rose-500 ml-0.5">*</span></p>
                            <div class="flex gap-2">
                                <button type="button" @click="centangSemua('modal-ubah-izin')"
                                        class="inline-flex items-center gap-1 px-3 py-1 text-[11px] font-medium text-[#2C5F6F] border border-[#2C5F6F]/30 bg-[#2C5F6F]/5 rounded-lg hover:bg-[#2C5F6F]/10 transition-colors">
                                    <span class="material-symbols-outlined text-[13px]">check_circle</span> Centang Semua
                                </button>
                                <button type="button" @click="batalkanSemua('modal-ubah-izin')"
                                        class="inline-flex items-center gap-1 px-3 py-1 text-[11px] font-medium text-[#40484b] border border-[#c0c8cb] bg-white rounded-lg hover:bg-[#f2f4f6] transition-colors">
                                    <span class="material-symbols-outlined text-[13px]">cancel</span> Batalkan
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <div id="modal-ubah-izin" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach ($kelompokIzin as $labelKelompok => $daftar)
                                    @php $gId = 'u-kg-' . \Illuminate\Support\Str::slug($labelKelompok); @endphp
                                    <div class="border border-[#e0e3e5] rounded-xl overflow-hidden hover:border-[#2C5F6F]/40 transition-colors">
                                        <div class="flex items-center justify-between px-4 py-2.5 bg-[#f7f9fb] border-b border-[#e0e3e5]">
                                            <p class="text-[11px] font-bold uppercase tracking-wider text-[#40484b]">{{ $labelKelompok }}</p>
                                            <div class="flex gap-1">
                                                <button type="button" @click="centangSemuaKelompok('{{ $gId }}')"
                                                        class="px-2 py-0.5 text-[10px] font-semibold text-[#2C5F6F] hover:bg-[#2C5F6F]/10 rounded-md transition-colors">+ Semua</button>
                                                <button type="button" @click="batalkanSemuaKelompok('{{ $gId }}')"
                                                        class="px-2 py-0.5 text-[10px] font-semibold text-[#40484b] hover:bg-[#f2f4f6] rounded-md transition-colors">− Semua</button>
                                            </div>
                                        </div>
                                        <div id="{{ $gId }}" class="divide-y divide-[#f2f4f6]">
                                            @foreach ($daftar as $izin)
                                                @php $izinWajib = $izin->kode_izin === 'peran.kelola'; @endphp
                                                <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-[#f7f9fb] transition-colors select-none"
                                                       :class="{{ $izinWajib ? 'true' : 'false' }} && editData.nama_peran === 'Super Admin' ? 'bg-amber-50/60' : ''">
                                                    <input type="checkbox" name="izin[]" value="{{ $izin->id }}"
                                                           {{ $errors->any() && old('_form') === 'ubah' && in_array($izin->id, (array) old('izin', [])) ? 'checked' : '' }}
                                                           :disabled="{{ $izinWajib ? 'true' : 'false' }} && editData.nama_peran === 'Super Admin'"
                                                           class="w-4 h-4 rounded border-[#c0c8cb] text-[#2C5F6F] focus:ring-[#2C5F6F] shrink-0 disabled:cursor-not-allowed disabled:opacity-60">
                                                    <span class="text-[13px] text-[#191c1e] leading-snug flex-1">
                                                        {{ $izin->deskripsi }}
                                                        @if($izinWajib)
                                                            <span x-show="editData.nama_peran === 'Super Admin'" class="ml-1.5 inline-block rounded-md border border-amber-500 bg-amber-400/20 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700">Wajib</span>
                                                            <input type="hidden" name="izin[]" value="{{ $izin->id }}"
                                                                   :disabled="editData.nama_peran !== 'Super Admin'">
                                                        @endif
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-[#e0e3e5] px-6 py-4 flex justify-end gap-2 shrink-0 bg-white rounded-b-2xl">
                    <button type="button" @click="modalUbah = false"
                            class="rounded-xl border border-[#c0c8cb] px-5 py-2.5 text-[14px] font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2.5 text-[14px] font-semibold text-white hover:bg-[#1E414C] transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">save</span> Perbarui Peran
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- Delete Modal --}}
    <div x-show="modalHapus" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-[#191c1e]/50 px-4">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl" @click.outside="modalHapus = false">
            <h3 class="text-[16px] leading-[24px] font-semibold text-[#191c1e]">Hapus Peran</h3>
            <p class="mt-2 text-[14px] leading-[20px] text-[#40484b]">
                Apakah Anda yakin ingin menghapus peran <span class="font-semibold text-[#191c1e]" x-text="namaHapus"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="modalHapus = false"
                        class="rounded-xl border border-[#c0c8cb] bg-white px-4 py-2.5 text-[14px] leading-[20px] font-medium text-[#191c1e] hover:bg-[#f2f4f6] transition-all">
                    Batal
                </button>
                <form :action="`{{ url('admin/peran') }}/${idHapus}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="rounded-xl bg-[#ba1a1a] px-4 py-2.5 text-[14px] leading-[20px] font-semibold text-white hover:brightness-110 transition-all">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection

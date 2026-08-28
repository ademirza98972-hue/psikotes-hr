@extends('layouts.admin', ['judulHalaman' => 'Kelola Admin & Staff'])

@section('content')
@php
    $bisaEdit = auth()->user()->hasIzin('pengguna.edit');
    $bisaHapus = auth()->user()->hasIzin('pengguna.hapus');
    $bisaToggle = auth()->user()->hasIzin('pengguna.edit');
@endphp
<div x-data="{
    modalHapus: false, idHapus: null, namaHapus: '',
    modalTambah: @js(($openModal ?? '') === 'tambah' || ($errors->any() && old('_form') === 'tambah')),
    modalUbah:   @js($errors->any() && old('_form') === 'ubah'),
    editData: @js($errors->any() && old('_form') === 'ubah'
        ? ['id' => (int) old('edit_id', 0), 'name' => old('name', ''), 'email' => old('email', ''), 'no_hp' => old('no_hp', ''), 'peran_id' => (string) old('peran_id', ''), 'status' => old('status', 'aktif'), 'peranNama' => '', 'isSuperAdmin' => false]
        : ['id' => 0, 'name' => '', 'email' => '', 'no_hp' => '', 'peran_id' => '', 'status' => 'aktif', 'peranNama' => '', 'isSuperAdmin' => false]),
    openEdit(d) { this.editData = d; this.modalUbah = true; },
}">

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-4">
        <div>
            <h2 class="text-[24px] font-bold text-[#001a22] leading-snug">Kelola Admin & Staff</h2>
            <p class="text-[14px] text-[#41484b] mt-1">Manajemen akses dan kontrol untuk seluruh administrator sistem.</p>
        </div>
        @auth
            @if(auth()->user()->hasIzin('pengguna.tambah'))
                <button type="button" @click="modalTambah = true"
                   class="self-start inline-flex items-center gap-2 bg-[#2C5F6F] text-white px-5 py-2.5 rounded-xl text-[14px] font-semibold hover:opacity-90 active:scale-[0.98] transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    Tambah Admin/Staff
                </button>
            @endif
        @endauth
    </div>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('admin.pengguna-admin.index') }}" class="bg-[#f2f4f6] p-4 rounded-xl border border-[#c0c8cb] flex flex-wrap items-center gap-3 shadow-sm">
        <div class="flex-1 min-w-[240px] relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#41484b] text-[18px]">search</span>
            <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama atau email..."
                class="w-full bg-white border border-[#c0c8cb] rounded-xl py-2 pl-10 pr-4 text-[14px] text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F] focus:border-[#2C5F6F] outline-none transition-all">
        </div>
        <div class="w-full md:w-auto">
            <select name="peran"
                class="w-full bg-white border border-[#c0c8cb] rounded-xl py-2 px-4 text-[14px] text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F] focus:border-[#2C5F6F] outline-none min-w-[160px] transition-all">
                <option value="">Semua Peran</option>
                @foreach ($semuaPeran as $r)
                    <option value="{{ $r->id }}" @if($filterPeran == $r->id) selected @endif>{{ $r->nama_peran }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-auto">
            <select name="status"
                class="w-full bg-white border border-[#c0c8cb] rounded-xl py-2 px-4 text-[14px] text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F] focus:border-[#2C5F6F] outline-none min-w-[160px] transition-all">
                <option value="">Semua Status</option>
                <option value="aktif" @if($filterStatus === 'aktif') selected @endif>Aktif</option>
                <option value="nonaktif" @if($filterStatus === 'nonaktif') selected @endif>Nonaktif</option>
            </select>
        </div>
        <button type="submit" class="w-full md:w-auto bg-[#001a22] text-white px-6 py-2 rounded-xl text-[14px] font-medium hover:opacity-90 transition-colors">
            Terapkan Filter
        </button>
        @if ($kataKunci || $filterPeran || $filterStatus)
            <a href="{{ route('admin.pengguna-admin.index') }}"
                class="w-full md:w-auto text-center rounded-xl border border-[#71787b] px-5 py-2 text-[14px] font-medium text-[#41484b] hover:bg-white transition-colors">
                Reset Filter
            </a>
        @endif
    </form>

    </div>{{-- /STICKY HEADER --}}

    {{-- DATA TABLE --}}
    <div class="mt-4 bg-white rounded-xl border border-[#c0c8cb] overflow-hidden shadow-sm">
        <div style="overflow: auto; max-height: calc(100vh - 300px);">
            <table class="w-full text-left border-collapse">
                <thead style="position: sticky; top: 0; z-index: 5;">
                    <tr style="background: #0f2230; border-bottom: 1px solid #0a1a25;">
                        <th class="px-4 py-3.5 text-[11px] font-bold uppercase tracking-wider text-center" style="color:#7db8c2;width:52px">No.</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-wider" style="color: #7db8c2;">Nama</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-wider" style="color: #7db8c2;">Email</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-wider" style="color: #7db8c2;">Peran</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-wider" style="color: #7db8c2;">Status</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-wider text-right" style="color: #7db8c2;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c0c8cb]/30">
                    @forelse ($pengguna as $u)
                        @php
                            $initials = mb_substr(explode(' ', $u->name)[0], 0, 1, 'UTF-8') . (strlen(trim(explode(' ', $u->name)[1] ?? '')) > 0 ? mb_substr(explode(' ', $u->name)[1], 0, 1, 'UTF-8') : '');
                            $warnaStatus = match($u->status) {
                                'aktif' => 'bg-[#1B5E20] text-white',
                                'nonaktif' => 'bg-[#757575] text-white',
                                default => 'bg-[#757575] text-white',
                            };
                            $labelStatus = match($u->status) {
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                                default => $u->status,
                            };
                            $isSuperAdmin = $u->peran && $u->peran->nama_peran === 'Super Admin';
                        @endphp
                        <tr class="hover:bg-[#f2f4f6] transition-colors">
                            <td class="px-4 py-4 text-sm text-[#71787c] tabular-nums text-center">{{ ($pengguna->currentPage() - 1) * $pengguna->perPage() + $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full {{ $isSuperAdmin ? 'bg-[#001a22] text-white' : 'bg-[#001a22]/10 text-[#001a22]' }} flex items-center justify-center font-bold text-[14px] border {{ $isSuperAdmin ? 'border-transparent' : 'border-[#001a22]/20' }}">
                                        {{ strtoupper($initials) }}
                                    </div>
                                    <span class="font-medium text-[14px] {{ $isSuperAdmin ? 'font-bold text-[#191c1e]' : 'text-[#191c1e]' }}">{{ $u->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-[14px] text-[#41484b]">{{ $u->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-[#e6e8ea] rounded-full text-[12px] font-medium text-[#41484b] border border-[#c0c8cb]">
                                    {{ $u->peran->nama_peran ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[12px] font-bold {{ $warnaStatus }}">
                                    {{ $labelStatus }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-2">
                                    @auth
                                        @if($bisaEdit)
                                            <button type="button"
                                                    @click="openEdit({ id: {{ $u->id }}, name: @js($u->name), email: @js($u->email), no_hp: @js($u->no_hp ?? ''), peran_id: @js((string)$u->peran_id), status: @js($u->status), peranNama: @js($u->peran->nama_peran ?? ''), isSuperAdmin: {{ $u->peran && $u->peran->nama_peran === 'Super Admin' ? 'true' : 'false' }} })"
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                                <span class="material-symbols-outlined text-[20px]">edit_square</span>
                                            </button>
                                            @if ($u->status !== 'aktif' && !$isSuperAdmin)
                                                @if ($u->status === 'aktif')
                                                    <form method="POST" action="{{ route('admin.pengguna-admin.toggle-status', $u->id) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="px-3 py-1.5 bg-[#455A64] text-white rounded-lg text-[12px] font-medium hover:opacity-90 transition-opacity">Nonaktifkan</button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.pengguna-admin.toggle-status', $u->id) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="px-3 py-1.5 bg-[#1B5E20] text-white rounded-lg text-[12px] font-medium hover:opacity-90 transition-opacity">Aktifkan</button>
                                                    </form>
                                                @endif
                                            @endif
                                        @endif
                                        @if($bisaHapus && $u->id !== auth()->id())
                                            <button type="button"
                                                    @click="modalHapus = true; idHapus = {{ $u->id }}; namaHapus = '{{ addslashes($u->name) }}'"
                                                    class="p-2 text-[#ba1a1a] hover:bg-[#fef2f2] rounded-lg transition-colors" title="Hapus">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                @if ($kataKunci || $filterPeran || $filterStatus)
                                    <span class="material-symbols-outlined block mb-3" style="font-size:40px; color:#c0c8cb;">search_off</span>
                                    <p class="text-[14px] font-medium text-[#40484b] mb-1">Tidak ada hasil yang cocok</p>
                                    <p class="text-[12px] text-[#71787c] mb-4">Coba ubah kata kunci atau hapus filter aktif.</p>
                                    <a href="{{ route('admin.pengguna-admin.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                        Reset Filter
                                    </a>
                                @else
                                    <span class="material-symbols-outlined block mb-3" style="font-size:40px; color:#c0c8cb;">admin_panel_settings</span>
                                    <p class="text-[14px] font-medium text-[#40484b] mb-1">Belum Ada Pengguna Admin</p>
                                    <p class="text-[12px] text-[#71787c] mb-4">Tambahkan pengguna admin atau staff untuk sistem.</p>
                                    @auth
                                        @if(auth()->user()->hasIzin('pengguna.tambah'))
                                            <button type="button" @click="modalTambah = true" class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                                                <span class="material-symbols-outlined text-[16px]">add</span>
                                                Tambah Admin/Staff
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
                <span class="font-semibold text-[#191c1e]">{{ $pengguna->firstItem() ?? 0 }}</span>–<span class="font-semibold text-[#191c1e]">{{ $pengguna->lastItem() ?? 0 }}</span>
                dari <span class="font-semibold text-[#191c1e]">{{ $pengguna->total() }}</span> data
            </p>
            @if ($pengguna->hasPages())
                {{ $pengguna->links() }}
            @endif
        </div>
    </div>

    {{-- Modal Tambah Admin/Staff --}}
    <template x-teleport="body">
    <div x-show="modalTambah" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-[#191c1e]/60 px-4"
         style="backdrop-filter:blur(2px)">
        <div @click.outside="modalTambah = false"
             class="relative bg-[#f7f9fb] rounded-2xl shadow-2xl w-full flex flex-col"
             style="max-width:540px;">

            {{-- Header --}}
            <div class="flex items-center gap-3 px-6 py-4 border-b border-[#e0e3e5] shrink-0 bg-white rounded-t-2xl">
                <div class="w-9 h-9 rounded-xl bg-[#2C5F6F]/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px] text-[#2C5F6F]">person_add</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-[15px] font-semibold text-[#001a22] leading-tight">Tambah Pengguna Internal</h3>
                    <p class="text-[12px] text-[#40484b] mt-0.5">Buat akun baru untuk admin atau staff.</p>
                </div>
                <button type="button" @click="modalTambah = false"
                        class="p-2 rounded-xl hover:bg-[#f2f4f6] text-[#40484b] transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.pengguna-admin.simpan') }}" class="flex flex-col flex-1 min-h-0">
                @csrf
                <input type="hidden" name="_form" value="tambah">

                <div class="overflow-y-auto px-5 py-5 space-y-4" style="max-height:calc(90vh - 140px)">

                    {{-- Section: Akun --}}
                    <div class="bg-white rounded-xl border border-[#e0e3e5] shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-[#e0e3e5]">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Data Akun</p>
                        </div>
                        <div class="px-5 py-4 space-y-4">
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Peran <span class="text-rose-600">*</span></label>
                                <select name="peran_id" required
                                        class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 appearance-none cursor-pointer @error('peran_id') border-rose-400 @enderror">
                                    <option value="">-- Pilih Peran Internal --</option>
                                    @foreach ($semuaPeran as $pr)
                                        <option value="{{ $pr->id }}" {{ (string)old('peran_id') === (string)$pr->id ? 'selected' : '' }}>{{ $pr->nama_peran }}</option>
                                    @endforeach
                                </select>
                                @error('peran_id')<p class="mt-1 text-[12px] text-rose-600 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Nama Lengkap <span class="text-rose-600">*</span></label>
                                    <input name="name" type="text" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso"
                                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 transition-all @error('name') border-rose-400 @enderror">
                                    @error('name')<p class="mt-1 text-[12px] text-rose-600 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Email <span class="text-rose-600">*</span></label>
                                    <input name="email" type="email" value="{{ old('email') }}" required placeholder="email@perusahaan.com"
                                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 transition-all @error('email') border-rose-400 @enderror">
                                    @error('email')<p class="mt-1 text-[12px] text-rose-600 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">No HP <span class="text-rose-600">*</span></label>
                                    <input name="no_hp" type="text" value="{{ old('no_hp') }}" required placeholder="081234567890"
                                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 transition-all @error('no_hp') border-rose-400 @enderror">
                                    @error('no_hp')<p class="mt-1 text-[12px] text-rose-600 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Status</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer px-4 py-2 rounded-lg border border-[#c0c8cb] hover:border-[#2C5F6F]/40 hover:bg-[#f7f9fb] transition-all">
                                        <input type="radio" name="status" value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'checked' : '' }}
                                               class="w-4 h-4 text-[#2C5F6F] border-[#c0c8cb] focus:ring-[#2C5F6F]">
                                        <span class="text-[13px] font-medium text-[#191c1e]">Aktif</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer px-4 py-2 rounded-lg border border-[#c0c8cb] hover:border-[#2C5F6F]/40 hover:bg-[#f7f9fb] transition-all">
                                        <input type="radio" name="status" value="nonaktif" {{ old('status') === 'nonaktif' ? 'checked' : '' }}
                                               class="w-4 h-4 text-[#2C5F6F] border-[#c0c8cb] focus:ring-[#2C5F6F]">
                                        <span class="text-[13px] font-medium text-[#191c1e]">Nonaktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Password --}}
                    <div class="bg-white rounded-xl border border-[#e0e3e5] shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-[#e0e3e5]">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Password</p>
                        </div>
                        <div class="px-5 py-4 space-y-4">
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Password <span class="text-rose-600">*</span></label>
                                <div class="relative">
                                    <input id="tp_password" name="password" type="password" required minlength="8" placeholder="Minimal 8 karakter"
                                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 pr-11 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('password') border-rose-400 @enderror">
                                    <button type="button" onclick="togglePassword('tp_password', this)"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#40484b] hover:text-[#191c1e] transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                </div>
                                @error('password')<p class="mt-1 text-[12px] text-rose-600 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Konfirmasi Password <span class="text-rose-600">*</span></label>
                                <div class="relative">
                                    <input id="tp_password_confirmation" name="password_confirmation" type="password" required minlength="8" placeholder="Ulangi password"
                                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 pr-11 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20">
                                    <button type="button" onclick="togglePassword('tp_password_confirmation', this)"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#40484b] hover:text-[#191c1e] transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="border-t border-[#e0e3e5] px-6 py-4 flex justify-end gap-2 shrink-0 bg-white rounded-b-2xl">
                    <button type="button" @click="modalTambah = false"
                            class="rounded-xl border border-[#c0c8cb] px-5 py-2.5 text-[14px] font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2.5 text-[14px] font-semibold text-white hover:bg-[#1E414C] transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">person_add</span> Tambahkan
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- Modal Ubah Admin/Staff --}}
    <template x-teleport="body">
    <div x-show="modalUbah" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-[#191c1e]/60 px-4"
         style="backdrop-filter:blur(2px)">
        <div @click.outside="modalUbah = false"
             class="relative bg-[#f7f9fb] rounded-2xl shadow-2xl w-full flex flex-col"
             style="max-width:540px;">

            {{-- Header dengan avatar --}}
            <div class="flex items-center gap-3 px-6 py-4 border-b border-[#e0e3e5] shrink-0 bg-white rounded-t-2xl">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-[15px] shrink-0"
                     :class="editData.isSuperAdmin ? 'bg-[#001a22] text-white' : 'bg-[#2C5F6F]/10 text-[#2C5F6F]'"
                     x-text="editData.name ? editData.name.charAt(0).toUpperCase() : '?'"></div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-[15px] font-semibold text-[#001a22] leading-tight truncate" x-text="editData.name"></h3>
                    <p class="text-[12px] text-[#40484b] mt-0.5" x-text="'ID: USR-' + String(editData.id).padStart(5, '0') + ' · ' + (editData.peranNama || '-')"></p>
                </div>
                <span x-show="editData.status === 'aktif'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-50 border border-green-200 text-[11px] font-bold text-green-700 shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> AKTIF
                </span>
                <span x-show="editData.status !== 'aktif'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#f5f5f5] border border-[#c0c8cb] text-[11px] font-bold text-[#757575] shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#757575]"></span> NONAKTIF
                </span>
                <button type="button" @click="modalUbah = false"
                        class="p-2 rounded-xl hover:bg-[#f2f4f6] text-[#40484b] transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form method="POST" :action="'{{ url('admin/pengguna-admin') }}/' + editData.id" class="flex flex-col flex-1 min-h-0">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form" value="ubah">
                <input type="hidden" name="edit_id" :value="editData.id">

                <div class="overflow-y-auto px-5 py-5 space-y-4" style="max-height:calc(90vh - 140px)">

                    {{-- Section: Data Personal --}}
                    <div class="bg-white rounded-xl border border-[#e0e3e5] shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-[#e0e3e5]">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Data Personal</p>
                        </div>
                        <div class="px-5 py-4 space-y-4">
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Peran <span class="text-rose-600">*</span></label>
                                <select name="peran_id" x-model="editData.peran_id" required
                                        class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 appearance-none @error('peran_id') border-rose-400 @enderror">
                                    <option value="">-- Pilih Peran --</option>
                                    @foreach ($semuaPeran as $pr)
                                        <option value="{{ $pr->id }}">{{ $pr->nama_peran }}</option>
                                    @endforeach
                                </select>
                                @error('peran_id')<p class="mt-1 text-[12px] text-rose-600 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Nama Lengkap <span class="text-rose-600">*</span></label>
                                <input name="name" type="text" x-model="editData.name" required
                                       class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 transition-all @error('name') border-rose-400 @enderror">
                                @error('name')<p class="mt-1 text-[12px] text-rose-600 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Email <span class="text-rose-600">*</span></label>
                                <input name="email" type="email" x-model="editData.email" required
                                       class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 transition-all @error('email') border-rose-400 @enderror">
                                @error('email')<p class="mt-1 text-[12px] text-rose-600 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">No HP <span class="text-rose-600">*</span></label>
                                <input name="no_hp" type="text" x-model="editData.no_hp" required
                                       class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 transition-all @error('no_hp') border-rose-400 @enderror">
                                @error('no_hp')<p class="mt-1 text-[12px] text-rose-600 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Status</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer px-4 py-2 rounded-lg border border-[#c0c8cb] hover:border-[#2C5F6F]/40 hover:bg-[#f7f9fb] transition-all">
                                        <input type="radio" name="status" value="aktif" x-model="editData.status"
                                               class="w-4 h-4 text-[#2C5F6F] border-[#c0c8cb] focus:ring-[#2C5F6F]">
                                        <span class="text-[13px] font-medium text-[#191c1e]">Aktif</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer px-4 py-2 rounded-lg border border-[#c0c8cb] hover:border-[#2C5F6F]/40 hover:bg-[#f7f9fb] transition-all">
                                        <input type="radio" name="status" value="nonaktif" x-model="editData.status"
                                               class="w-4 h-4 text-[#2C5F6F] border-[#c0c8cb] focus:ring-[#2C5F6F]">
                                        <span class="text-[13px] font-medium text-[#191c1e]">Nonaktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Password Baru --}}
                    <div class="bg-white rounded-xl border border-[#e0e3e5] shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-[#e0e3e5]">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Password Baru <span class="text-[10px] font-normal normal-case text-[#40484b]">— kosongkan jika tidak ingin diubah</span></p>
                        </div>
                        <div class="px-5 py-4 space-y-4">
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Password</label>
                                <div class="relative">
                                    <input id="up_password" name="password" type="password" minlength="8" placeholder="Minimal 8 karakter"
                                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 pr-11 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20">
                                    <button type="button" onclick="togglePassword('up_password', this)"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#40484b] hover:text-[#191c1e] transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1.5">Konfirmasi Password</label>
                                <div class="relative">
                                    <input id="up_password_confirmation" name="password_confirmation" type="password" minlength="8" placeholder="Ulangi password"
                                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 pr-11 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20">
                                    <button type="button" onclick="togglePassword('up_password_confirmation', this)"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#40484b] hover:text-[#191c1e] transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="border-t border-[#e0e3e5] px-6 py-4 flex justify-end gap-2 shrink-0 bg-white rounded-b-2xl">
                    <button type="button" @click="modalUbah = false"
                            class="rounded-xl border border-[#c0c8cb] px-5 py-2.5 text-[14px] font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2.5 text-[14px] font-semibold text-white hover:bg-[#1E414C] transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">save</span> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <div x-show="modalHapus" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-[#191c1e]/50 px-4">
        <div class="w-full max-w-md bg-white p-6 rounded-xl shadow-xl" @click.outside="modalHapus = false">
            <h3 class="text-[16px] font-semibold text-[#191c1e]">Hapus Pengguna Internal</h3>
            <p class="mt-2 text-[14px] text-[#41484b]">
                Apakah Anda yakin ingin menghapus pengguna <span class="font-semibold text-[#191c1e]" x-text="namaHapus"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="modalHapus = false"
                        class="px-5 py-2.5 rounded-xl border border-[#71787b] text-[14px] font-medium text-[#41484b] hover:bg-[#f2f4f6] transition-colors">Batal</button>
                <form :action="`{{ url('admin/pengguna-admin') }}/${idHapus}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-[#B71C1C] text-white text-[14px] font-semibold hover:opacity-90 transition-opacity">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('.material-symbols-outlined');
    if (input.type === 'password') { input.type = 'text'; icon.textContent = 'visibility_off'; }
    else { input.type = 'password'; icon.textContent = 'visibility'; }
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection

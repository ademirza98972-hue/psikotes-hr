@extends('layouts.admin', ['judulHalaman' => 'Ubah Peran'])

@php
    $isSuperAdmin = $peran->nama_peran === 'Super Admin';
@endphp

@section('content')
<div class="w-full"
     x-data="{
        centangSemuaKelompok(id) {
            document.querySelectorAll('#' + id + ' input[type=checkbox][name=\'izin[]\']:not(:disabled)').forEach(el => el.checked = true);
        },
        batalkanSemuaKelompok(id) {
            document.querySelectorAll('#' + id + ' input[type=checkbox][name=\'izin[]\']:not(:disabled)').forEach(el => el.checked = false);
        },
        centangSemua() {
            document.querySelectorAll('input[type=checkbox][name=\'izin[]\']:not(:disabled)').forEach(el => el.checked = true);
        },
        batalkanSemua() {
            document.querySelectorAll('input[type=checkbox][name=\'izin[]\']:not(:disabled)').forEach(el => el.checked = false);
        }
     }">

    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-[24px] font-semibold text-[#00303c]">Ubah Peran: {{ $peran->nama_peran }}</h2>
            <p class="mt-0.5 text-[13px] text-[#40484b]">Perbarui nama, deskripsi, dan izin akses peran ini.</p>
        </div>
        <a href="{{ route('admin.peran.index') }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali
        </a>
    </div>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 mb-6">
            <p class="mb-2 text-sm font-semibold text-rose-700">Terdapat kesalahan:</p>
            <ul class="list-disc space-y-1 pl-5 text-sm text-rose-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.peran.perbarui', $peran->id) }}" class="space-y-8" id="roleForm">
        @csrf
        @method('PUT')

        {{-- SECTION: Nama & Deskripsi --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Informasi Peran</p>
            </div>
            <div class="px-6 py-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nama_peran" class="block text-[13px] font-medium text-[#191c1e] mb-1">Nama Peran <span class="text-rose-600">*</span></label>
                        <input id="nama_peran" name="nama_peran" type="text" value="{{ old('nama_peran', $peran->nama_peran) }}" required maxlength="255"
                               class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('nama_peran') border-rose-400 @enderror">
                    </div>
                    <div>
                        <label for="deskripsi" class="block text-[13px] font-medium text-[#191c1e] mb-1">Deskripsi <span class="text-[#40484b] font-normal">(opsional)</span></label>
                        <textarea id="deskripsi" name="deskripsi" rows="2" maxlength="1000"
                                  class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 resize-none">{{ old('deskripsi', $peran->deskripsi) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info warning --}}
        @if ($isSuperAdmin)
        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 flex items-start gap-3">
            <span class="material-symbols-outlined text-blue-600 text-[20px] mt-0.5 shrink-0">info</span>
            <div>
                <p class="text-sm font-medium text-blue-800">Peran Super Admin</p>
                <p class="text-xs text-blue-700 mt-0.5">Izin "Kelola Peran" dan "Kelola Izin Sistem" wajib tetap aktif pada peran Super Admin untuk menjaga integritas keamanan sistem.</p>
            </div>
        </div>
        @endif

        {{-- Section: Daftar Izin --}}
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-[18px] font-semibold text-[#191c1e]">Daftar Izin <span class="text-rose-600">*</span></h2>
                    <p class="text-[14px] text-[#40484b] opacity-80">Centang izin-izin yang dimiliki peran ini.</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="centangSemua()"
                            class="px-4 py-2 border border-[#c0c8cb] text-[#191c1e] text-[12px] font-medium rounded-xl hover:bg-[#f2f4f6] transition-colors active:scale-95 flex items-center">
                        <span class="material-symbols-outlined text-[16px] mr-2">check_circle</span> Centang Semua
                    </button>
                    <button type="button" @click="batalkanSemua()"
                            class="px-4 py-2 border border-[#c0c8cb] text-[#191c1e] text-[12px] font-medium rounded-xl hover:bg-[#f2f4f6] transition-colors active:scale-95 flex items-center">
                        <span class="material-symbols-outlined text-[16px] mr-2">cancel</span> Batalkan Semua
                    </button>
                </div>
            </div>

            {{-- Permission Sub-groups (Bento-like Cards) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($kelompokIzin as $labelKelompok => $daftar)
                    @php $prefixId = 'kg-' . \Illuminate\Support\Str::slug($labelKelompok); @endphp
                    <div class="bg-white border border-[#c0c8cb] rounded-xl p-5 hover:border-[#2C5F6F]/40 transition-colors group">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-[11px] leading-[16px] font-semibold uppercase tracking-[0.05em] text-[#40484b]">{{ $labelKelompok }}</h3>
                            <div class="flex gap-1">
                                <button type="button" @click="centangSemuaKelompok('{{ $prefixId }}')"
                                        class="px-2 py-0.5 text-[10px] leading-[14px] font-medium text-[#2C5F6F] hover:bg-[#2C5F6F]/10 rounded-md transition-colors">
                                    + Semua
                                </button>
                                <button type="button" @click="batalkanSemuaKelompok('{{ $prefixId }}')"
                                        class="px-2 py-0.5 text-[10px] leading-[14px] font-medium text-[#40484b] hover:bg-[#f2f4f6] rounded-md transition-colors">
                                    - Semua
                                </button>
                            </div>
                        </div>
                        <div id="{{ $prefixId }}" class="space-y-3">
                            @foreach ($daftar as $izin)
                                @php
                                    $dipaksa = $isSuperAdmin && $izin->kode_izin === 'peran.kelola';
                                @endphp
                                <label class="flex items-center gap-3 cursor-pointer select-none {{ $dipaksa ? '' : '' }}">
                                    <input type="checkbox" name="izin[]" value="{{ $izin->id }}"
                                           {{ in_array($izin->id, old('izin', $izinTerpilih)) ? 'checked' : '' }}
                                           @if ($dipaksa) disabled checked @endif
                                           class="w-5 h-5 rounded border-[#c0c8cb] text-[#2C5F6F] focus:ring-[#2C5F6F] transition-all cursor-pointer disabled:cursor-not-allowed disabled:bg-[#e0e3e5] disabled:border-[#c0c8cb]">
                                    <span class="text-[14px] text-[#191c1e] flex-1">
                                        {{ $izin->deskripsi }}
                                        @if ($dipaksa)
                                            <span class="ml-2 inline-block rounded border border-amber-600 bg-amber-500 px-1.5 py-0.5 text-[10px] font-medium normal-case text-white">Wajib Super Admin</span>
                                            <input type="hidden" name="izin[]" value="{{ $izin->id }}">
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Warning --}}
        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 flex items-start gap-3">
            <span class="material-symbols-outlined text-[#d97706] text-[20px] mt-0.5 shrink-0">warning</span>
            <div>
                <p class="text-sm font-medium text-amber-800">Perubahan izin berlaku langsung</p>
                <p class="text-xs text-amber-700 mt-0.5">Perubahan hak akses pada peran ini akan langsung mempengaruhi semua pengguna dengan peran tersebut.</p>
            </div>
        </div>

        {{-- ACTION BAR --}}
        <div class="sticky bottom-0 mt-4 flex items-center justify-end gap-3 rounded-xl border border-[#e0e3e5] bg-white/95 px-6 py-4 shadow-[0_-2px_12px_rgba(0,0,0,0.06)] backdrop-blur">
            <a href="{{ route('admin.peran.index') }}"
               class="rounded-xl border border-[#c0c8cb] px-5 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</a>
            <button type="submit" form="roleForm"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Perbarui
            </button>
        </div>
    </form>
</div>
@endsection

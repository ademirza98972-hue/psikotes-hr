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

    {{-- Top Nav Bar --}}
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.peran.index') }}" class="text-[#2C5F6F] font-[12px] leading-[16px] font-medium hover:underline flex items-center transition-all">
                <span class="material-symbols-outlined text-[18px] mr-1">arrow_back</span> Kembali
            </a>
            <span class="font-[18px] leading-[24px] font-semibold text-[#191c1e]">Ubah Peran: {{ $peran->nama_peran }}</span>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-[#ba1a1a] bg-[#fef2f2] px-4 py-3 text-[14px] text-[#93000a]">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.peran.perbarui', $peran->id) }}" class="space-y-8" id="roleForm">
        @csrf
        @method('PUT')

        {{-- Form Row 1: 2-column grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label for="nama_peran" class="font-[12px] leading-[16px] font-medium text-[#40484b]">Nama Peran <span class="text-[#ba1a1a]">*</span></label>
                <input id="nama_peran" name="nama_peran" type="text" value="{{ old('nama_peran', $peran->nama_peran) }}" required maxlength="255"
                       class="w-full rounded-xl border border-[#c0c8cb] bg-white focus:border-[#2C5F6F] focus:ring-[#2C5F6F] font-[14px] leading-[20px] text-[#191c1e] px-4 py-3 transition-all shadow-sm">
            </div>
            <div class="space-y-2">
                <label for="deskripsi" class="font-[12px] leading-[16px] font-medium text-[#40484b]">Deskripsi <span class="text-[#919eab]">(opsional)</span></label>
                <textarea id="deskripsi" name="deskripsi" rows="2" maxlength="1000"
                          class="w-full rounded-xl border border-[#c0c8cb] bg-white focus:border-[#2C5F6F] focus:ring-[#2C5F6F] font-[14px] leading-[20px] text-[#191c1e] px-4 py-3 transition-all shadow-sm resize-none">{{ old('deskripsi', $peran->deskripsi) }}</textarea>
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
        <div class="border-t border-[#e0e3e5] pt-6 space-y-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="font-[18px] leading-[24px] font-semibold text-[#191c1e]">Daftar Izin <span class="text-[#ba1a1a]">*</span></h2>
                    <p class="font-[14px] leading-[20px] text-[#40484b] opacity-80">Centang izin-izin yang dimiliki peran ini.</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="centangSemua()"
                            class="px-4 py-2 border border-[#71787b] text-[#191c1e] font-[12px] leading-[16px] font-medium rounded-xl hover:bg-[#f2f4f6] transition-colors active:scale-95 flex items-center">
                        <span class="material-symbols-outlined text-[16px] mr-2">check_circle</span> Centang Semua
                    </button>
                    <button type="button" @click="batalkanSemua()"
                            class="px-4 py-2 border border-[#71787b] text-[#191c1e] font-[12px] leading-[16px] font-medium rounded-xl hover:bg-[#f2f4f6] transition-colors active:scale-95 flex items-center">
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
                                    $dipaksa = $isSuperAdmin && in_array($izin->kode_izin, ['peran.kelola', 'izin.kelola'], true);
                                @endphp
                                <label class="flex items-center gap-3 cursor-pointer select-none {{ $dipaksa ? '' : '' }}">
                                    <input type="checkbox" name="izin[]" value="{{ $izin->id }}"
                                           {{ in_array($izin->id, old('izin', $izinTerpilih)) ? 'checked' : '' }}
                                           @if ($dipaksa) disabled checked @endif
                                           class="w-5 h-5 rounded border-[#c0c8cb] text-[#2C5F6F] focus:ring-[#2C5F6F] transition-all cursor-pointer disabled:cursor-not-allowed disabled:bg-[#e0e3e5] disabled:border-[#c0c8cb]">
                                    <span class="font-[14px] leading-[20px] text-[#191c1e] flex-1">
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

        {{-- Sticky Bottom Footer --}}
        <div class="fixed bottom-0 left-[280px] right-0 bg-white border-t border-[#c0c8cb] shadow-[0_-4px_20px_rgba(0,0,0,0.03)] z-20">
            <div class="max-w-full mx-auto px-6 h-[80px] flex items-center justify-end gap-4">
                <a href="{{ route('admin.peran.index') }}"
                   class="px-8 py-3 rounded-xl border border-[#71787b] text-[#191c1e] font-[14px] leading-[20px] font-medium hover:bg-[#f2f4f6] transition-all active:scale-95">
                    Batal
                </a>
                <button type="submit" form="roleForm"
                        class="px-10 py-3 rounded-xl bg-[#2C5F6F] text-white font-[18px] leading-[24px] font-semibold shadow-lg shadow-[#2C5F6F]/20 hover:brightness-110 transition-all active:scale-95">
                    Perbarui
                </button>
            </div>
        </div>
        <div class="h-24"></div>
    </form>
</div>
@endsection

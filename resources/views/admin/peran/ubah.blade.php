@extends('layouts.admin', ['judulHalaman' => 'Ubah Peran'])

@section('content')
@php
    $isSuperAdmin = $peran->nama_peran === 'Super Admin';
@endphp
<div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
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
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-900">Ubah Peran: {{ $peran->nama_peran }}</h2>
        <a href="{{ route('admin.peran.index') }}" class="text-sm text-slate-600 hover:text-slate-800">&larr; Kembali</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-md border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.peran.perbarui', $peran->id) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="nama_peran" class="block text-sm font-medium text-slate-700">Nama Peran</label>
                <input id="nama_peran" name="nama_peran" type="text" value="{{ old('nama_peran', $peran->nama_peran) }}" required maxlength="255"
                       class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-medium text-slate-700">Deskripsi <span class="text-slate-400">(opsional)</span></label>
                <textarea id="deskripsi" name="deskripsi" rows="2" maxlength="1000"
                          class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">{{ old('deskripsi', $peran->deskripsi) }}</textarea>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-700">Daftar Izin <span class="text-rose-500">*</span></p>
                    <p class="mt-1 text-xs text-slate-500">Centang izin-izin yang dimiliki peran ini.</p>
                </div>
                <div class="flex gap-2 text-xs">
                    <button type="button" @click="centangSemua()"
                            class="rounded border border-slate-300 bg-white px-2 py-1 font-medium text-slate-700 hover:bg-slate-50">Centang Semua</button>
                    <button type="button" @click="batalkanSemua()"
                            class="rounded border border-slate-300 bg-white px-2 py-1 font-medium text-slate-700 hover:bg-slate-50">Batalkan Semua</button>
                </div>
            </div>

            <div class="mt-3 space-y-3">
                @foreach ($kelompokIzin as $labelKelompok => $daftar)
                    @php $prefixId = 'kg-' . \Illuminate\Support\Str::slug($labelKelompok); @endphp
                    <div class="rounded-md border border-slate-200 bg-slate-50 p-3">
                        <div class="mb-2 flex items-center justify-between">
                            <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-700">{{ $labelKelompok }}</h3>
                            <div class="flex gap-2 text-[11px]">
                                <button type="button" @click="centangSemuaKelompok('{{ $prefixId }}')"
                                        class="rounded border border-slate-300 bg-white px-1.5 py-0.5 font-medium text-slate-600 hover:bg-slate-100">Centang Semua</button>
                                <button type="button" @click="batalkanSemuaKelompok('{{ $prefixId }}')"
                                        class="rounded border border-slate-300 bg-white px-1.5 py-0.5 font-medium text-slate-600 hover:bg-slate-100">Batalkan Semua</button>
                            </div>
                        </div>
                        <div id="{{ $prefixId }}" class="grid grid-cols-1 gap-1.5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            @foreach ($daftar as $izin)
                                @php
                                    $dipaksa = $isSuperAdmin && in_array($izin->kode_izin, ['peran.kelola', 'izin.kelola'], true);
                                @endphp
                                <label class="flex items-center gap-2 text-sm font-medium uppercase text-slate-700 {{ $dipaksa ? 'opacity-90' : '' }}">
                                    <input type="checkbox" name="izin[]" value="{{ $izin->id }}"
                                           {{ in_array($izin->id, old('izin', $izinTerpilih)) ? 'checked' : '' }}
                                           @if ($dipaksa) disabled checked @endif
                                           class="h-4 w-4 rounded border-slate-300 text-[#2C5F6F] focus:ring-[#2C5F6F] disabled:cursor-not-allowed disabled:bg-slate-200">
                                    <span class="flex-1">
                                        <span>{{ $izin->deskripsi }}</span>
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

        <div class="sticky bottom-0 -mx-6 mt-4 flex justify-end gap-2 border-t border-slate-200 bg-white px-6 py-3 shadow-[0_-2px_4px_rgba(0,0,0,0.04)]">
            <a href="{{ route('admin.peran.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</a>
            <button type="submit" class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">Perbarui</button>
        </div>
    </form>
</div>
@endsection
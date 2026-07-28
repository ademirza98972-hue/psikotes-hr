@extends('layouts.admin', ['judulHalaman' => 'Tambah Alat Tes'])

@section('content')
<div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
     x-data="{
         batasAktif: {{ old('batas_waktu_per_soal_aktif') ? 'true' : 'false' }},
         formatDasar: '{{ old('format_dasar', 'Pilihan Ganda') }}'
     }">

    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Tambah Alat Tes</h2>
            <p class="mt-0.5 text-xs text-slate-500">Lengkapi formulir di bawah untuk mendaftarkan alat tes baru.</p>
        </div>
        <a href="{{ route('admin.alat-tes.index') }}" class="text-sm text-slate-600 hover:text-slate-800">&larr; Kembali</a>
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

    <form method="POST" action="{{ route('admin.alat-tes.simpan') }}" class="space-y-4">
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="nama" class="block text-sm font-medium text-slate-700">Nama Alat Tes <span class="text-rose-500">*</span></label>
                <input id="nama" name="nama" type="text" value="{{ old('nama') }}" required maxlength="255"
                       class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
            </div>

            <div>
                <label for="format_dasar" class="block text-sm font-medium text-slate-700">Format Dasar <span class="text-rose-500">*</span></label>
                <select id="format_dasar" name="format_dasar" x-model="formatDasar" required
                        class="mt-1 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    @foreach ($pilihanFormat as $format)
                        <option value="{{ $format }}" @selected(old('format_dasar') === $format)>{{ $format }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="durasi_total_menit" class="block text-sm font-medium text-slate-700">Durasi Total <span class="text-slate-400">(menit, opsional)</span></label>
                <input id="durasi_total_menit" name="durasi_total_menit" type="number" min="0" value="{{ old('durasi_total_menit') }}"
                       class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
            </div>
        </div>

        <div class="rounded-md border border-slate-200 bg-slate-50 p-4">
            <label class="flex items-center gap-3">
                <input type="hidden" name="batas_waktu_per_soal_aktif" value="0">
                <input type="checkbox" name="batas_waktu_per_soal_aktif" value="1" x-model="batasAktif"
                       {{ old('batas_waktu_per_soal_aktif') ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-slate-300 text-[#2C5F6F] focus:ring-[#2C5F6F]">
                <div>
                    <p class="text-sm font-medium text-slate-800">Aktifkan Batas Waktu per Soal</p>
                    <p class="text-xs text-slate-500">Jika aktif, peserta harus menjawab dalam waktu yang ditentukan.</p>
                </div>
            </label>

            <div x-show="batasAktif" x-cloak class="mt-3">
                <label for="batas_waktu_per_soal_detik" class="block text-sm font-medium text-slate-700">Detik per Soal</label>
                <input id="batas_waktu_per_soal_detik" name="batas_waktu_per_soal_detik" type="number" min="1"
                       value="{{ old('batas_waktu_per_soal_detik') }}"
                       class="mt-1 block w-40 rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
            </div>
        </div>

        <div class="rounded-md border border-amber-200 bg-amber-50 p-4">
            <label class="flex items-start gap-3">
                <input type="hidden" name="is_sensitif" value="0">
                <input type="checkbox" name="is_sensitif" value="1" {{ old('is_sensitif') ? 'checked' : '' }}
                       class="mt-0.5 h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-600">
                <div>
                    <p class="text-sm font-medium text-slate-800">Kategori Sensitif</p>
                    <p class="text-xs text-slate-500">Tandai jika alat tes memuat konten psikologis klinis (misalnya kepribadian patologis). Aksesnya akan dibatasi untuk psikolog terverifikasi.</p>
                </div>
            </label>
        </div>

        <div class="sticky bottom-0 -mx-6 mt-4 flex justify-end gap-2 border-t border-slate-200 bg-white px-6 py-3 shadow-[0_-2px_4px_rgba(0,0,0,0.04)]">
            <a href="{{ route('admin.alat-tes.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</a>
            <button type="submit" class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">Simpan</button>
        </div>
    </form>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection
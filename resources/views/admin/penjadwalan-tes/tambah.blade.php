@extends('layouts.admin', ['judulHalaman' => 'Tambah Penjadwalan Tes'])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-bold text-[#191c1e]">Tambah Penjadwalan Tes</h2>
            <p class="mt-0.5 text-sm text-[#41484b]">Buat sesi penilaian psikologis baru untuk karyawan atau kandidat.</p>
        </div>
        <a href="{{ route('admin.penjadwalan-tes.index') }}"
           class="inline-flex items-center gap-1 text-[#2C5F6F] font-semibold text-sm hover:opacity-80 transition-opacity">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali
        </a>
    </div>


    <form method="POST" action="{{ route('admin.penjadwalan-tes.simpan') }}" class="max-w-2xl mx-auto space-y-5">
        @csrf

        <div class="space-y-5">

            <section class="rounded-xl border border-[#c1c7cb] bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-5">
                    <span class="material-symbols-outlined text-[#2C5F6F]">info</span>
                    <h3 class="text-base font-semibold text-[#191c1e]">Identitas Sesi</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <label for="nama_sesi" class="block text-[12px] font-medium text-[#41484b] mb-1.5">Nama Sesi <span class="text-rose-500">*</span></label>
                        <input id="nama_sesi" name="nama_sesi" type="text" value="{{ old('nama_sesi') }}" required maxlength="255"
                               placeholder="mis. Rekrutmen Staff Finance Batch 1"
                               class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                    </div>
                    <div>
                        <label for="departemen_terkait" class="block text-[12px] font-medium text-[#41484b] mb-1.5">Departemen Terkait <span class="text-[#919eab]">(opsional)</span></label>
                        <div class="space-y-2">
                            @foreach ($daftarDepartemen as $dept)
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-[#e0e3e5] bg-[#f2f4f6] cursor-pointer hover:border-[#2C5F6F]/40 transition-colors">
                                    <input type="checkbox" name="departemen_ids[]"
                                           value="{{ $dept->id }}"
                                           {{ is_array(old('departemen_ids')) && in_array($dept->id, old('departemen_ids')) ? 'checked' : '' }}
                                           class="h-4 w-4 rounded border-[#c1c7cb] text-[#2C5F6F] focus:ring-[#2C5F6F]">
                                    <span class="text-sm font-medium text-[#191c1e]">{{ $dept->nama_departemen }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-[12px] font-medium text-[#41484b] mb-1.5">
                            Alat Tes <span class="text-rose-500">*</span>
                        </label>
                        <div class="space-y-2">
                            @foreach ($daftarAlatTes as $alat)
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-[#e0e3e5] bg-[#f2f4f6] cursor-pointer hover:border-[#2C5F6F]/40 transition-colors">
                                    <input type="checkbox" name="alat_tes_ids[]"
                                           value="{{ $alat->id }}"
                                           {{ is_array(old('alat_tes_ids')) && in_array($alat->id, old('alat_tes_ids')) ? 'checked' : '' }}
                                           class="h-4 w-4 rounded border-[#c1c7cb] text-[#2C5F6F] focus:ring-[#2C5F6F]">
                                    <div>
                                        <p class="text-sm font-semibold text-[#191c1e]">{{ $alat->nama }}</p>
                                        <p class="text-[11px] text-[#41484b]">{{ $alat->format_dasar }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="tanggal_mulai" class="block text-[12px] font-medium text-[#41484b] mb-1.5">Tanggal Mulai <span class="text-rose-500">*</span></label>
                            <input id="tanggal_mulai" name="tanggal_mulai" type="date" value="{{ old('tanggal_mulai') }}" required
                                   class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                        </div>
                        <div>
                            <label for="tanggal_selesai" class="block text-[12px] font-medium text-[#41484b] mb-1.5">Tanggal Selesai <span class="text-rose-500">*</span></label>
                            <input id="tanggal_selesai" name="tanggal_selesai" type="date" value="{{ old('tanggal_selesai') }}" required
                                   class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                        </div>
                    </div>
                    <div>
                        <label for="status" class="block text-[12px] font-medium text-[#41484b] mb-1.5">Status <span class="text-rose-500">*</span></label>
                        <select id="status" name="status" required
                                class="w-full bg-[#f2f4f6] border border-[#e0e3e5] rounded-xl px-4 py-2.5 text-sm text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F]/40 focus:border-[#2C5F6F] transition-all outline-none">
                            <option value="Draft" @selected(old('status','Draft')==='Draft')>Draft</option>
                            <option value="Aktif" @selected(old('status')==='Aktif')>Aktif</option>
                            <option value="Selesai" @selected(old('status')==='Selesai')>Selesai</option>
                        </select>
                    </div>
                </div>
            </section>

        </div>

        {{-- STICKY FOOTER --}}
        <div class="sticky bottom-0 -mx-2 bg-white border-t border-[#c1c7cb] px-6 py-4 flex justify-end gap-3 shadow-[0_-4px_20px_-10px_rgba(0,0,0,0.1)] z-10">
            <a href="{{ route('admin.penjadwalan-tes.index') }}"
               class="px-5 py-2.5 rounded-xl border border-[#c1c7cb] text-[#41484b] text-sm font-medium hover:bg-[#f2f4f6] transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-[#2C5F6F] text-white text-sm font-bold hover:opacity-90 active:scale-[0.98] transition-all shadow-md">
                Simpan Penjadwalan
            </button>
        </div>
    </form>
</div>
@endsection

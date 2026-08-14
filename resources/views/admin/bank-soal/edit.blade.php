@extends('layouts.admin', ['judulHalaman' => 'Edit Soal'])

@section('content')
@php
    $warnaFormat = [
        'Pilihan Ganda' => 'bg-blue-600',
        'Skala Likert'  => 'bg-indigo-600',
        'Forced Choice' => 'bg-amber-600',
        'Mixed'         => 'bg-purple-600',
    ];
    $format = $soal->alatTes->format_dasar ?? 'Pilihan Ganda';
@endphp

<div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm">

    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h2 class="text-lg font-semibold text-slate-900">Edit Soal</h2>
            <span class="inline-block rounded-md {{ $warnaFormat[$format] ?? 'bg-slate-600' }} px-2 py-0.5 text-xs font-semibold text-white">
                {{ $format }}
            </span>
        </div>
        <a href="{{ route('admin.bank-soal.index', ['alat_tes_id' => $soal->alatTes->id]) }}" class="text-sm text-slate-600 hover:text-slate-800">&larr; Kembali</a>
    </div>

    <p class="mb-4 text-sm text-slate-600">
        Alat Tes: <strong>{{ $soal->alatTes->nama }}</strong>.
        Form mengikuti format dasar <strong>{{ $format }}</strong>.
    </p>

    @if ($errors->any())
        <div class="mb-4 rounded-md border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.bank-soal.update', $soal->id) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        @if ($format === 'Grid')
            <div class="mb-6">
                <label class="block text-sm font-semibold
                       text-[#191c1e] mb-2">
                    Deretan Angka Kolom
                    <span class="text-rose-500">*</span>
                </label>
                <p class="text-[12px] text-[#919eab] mb-2">
                    Masukkan 27 angka (0-9) dipisah koma.
                    Contoh: 1,6,2,8,8,2,2,1,6,9,...
                </p>
                <textarea name="teks_soal" rows="3" required
                    class="w-full bg-[#f2f4f6] border border-[#e0e3e5]
                           rounded-xl px-4 py-3 text-sm font-mono
                           text-[#191c1e] focus:ring-2
                           focus:ring-[#2C5F6F]/40
                           focus:border-[#2C5F6F] outline-none
                           transition-all resize-none"
                    placeholder="1,6,2,8,8,2,2,1,6,9,6,4,6,6,2,1,7,6,1,6,2,4,1,6,4,9,1"
                >{{ old('teks_soal', $soal->teks_soal) }}</textarea>
            </div>
        @elseif ($format === 'Pilihan Ganda')
            <div>
                <label for="teks_soal" class="block text-sm font-medium text-slate-700">Teks Soal <span class="text-rose-500">*</span></label>
                <textarea id="teks_soal" name="teks_soal" rows="3" required
                          class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">{{ old('teks_soal', $soal->teks_soal) }}</textarea>
            </div>

            <div class="space-y-3">
                <p class="text-sm font-medium text-slate-700">Opsi Jawaban</p>
                @php
                    $opsiList = $soal->opsiJawaban->sortBy('urutan')->values();
                @endphp
                @foreach (['A','B','C','D'] as $huruf)
                    @php
                        $idx = $loop->index;
                        $opsi = $opsiList[$idx] ?? null;
                    @endphp
                    <div class="flex items-center gap-3 rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                        <label for="kunci_{{ $huruf }}" class="w-6 text-sm font-semibold text-slate-700 cursor-pointer">{{ $huruf }}.</label>
                        <input type="radio" name="kunci" value="{{ $huruf }}" id="kunci_{{ $huruf }}"
                               @checked(old('kunci', $soal->kunci_jawaban) === $huruf)
                               class="h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-600">
                        <input type="text" name="opsi[{{ $huruf }}]" value="{{ old('opsi.' . $huruf, $opsi?->teks_opsi ?? '') }}" required
                               placeholder="Teks opsi {{ $huruf }}"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    </div>
                @endforeach
            </div>

        @elseif ($format === 'Skala Likert')
            <div>
                <label for="pernyataan" class="block text-sm font-medium text-slate-700">Pernyataan <span class="text-rose-500">*</span></label>
                <textarea id="pernyataan" name="pernyataan" rows="3" required
                          class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">{{ old('pernyataan', $soal->teks_soal) }}</textarea>
            </div>

            <div>
                <label for="dimensi" class="block text-sm font-medium text-slate-700">Nama Dimensi <span class="text-rose-500">*</span></label>
                <input id="dimensi" name="dimensi" type="text" value="{{ old('dimensi', $soal->alatTes->dimensiAlatTes->first()?->nama_dimensi ?? '') }}" required maxlength="100"
                       placeholder="mis. Influence, Dominance, D, Pa, ..."
                       class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                <p class="mt-1 text-xs text-slate-500">&nbsp;</p>
            </div>

        @elseif ($format === 'Forced Choice')
            @php
                $opsiList = $soal->opsiJawaban->sortBy('urutan');
                $opsiA = $opsiList->first();
                $opsiB = $opsiList->count() > 1 ? $opsiList->skip(1)->first() : null;
                $dimensiA = $opsiA?->bobotOpsiDimensi->first()?->dimensi;
                $dimensiB = $opsiB?->bobotOpsiDimensi->first()?->dimensi;
            @endphp
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-md border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pernyataan A</p>
                    <div class="mt-2">
                        <label for="pernyataan_a" class="block text-sm font-medium text-slate-700">Teks Pernyataan <span class="text-rose-500">*</span></label>
                        <textarea id="pernyataan_a" name="pernyataan_a" rows="3" required
                                  class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">{{ old('pernyataan_a', $opsiA?->teks_opsi ?? '') }}</textarea>
                    </div>
                    <div class="mt-3">
                        <label for="dimensi_a" class="block text-sm font-medium text-slate-700">Dimensi A <span class="text-rose-500">*</span></label>
                        <input id="dimensi_a" name="dimensi_a" type="text" value="{{ old('dimensi_a', $dimensiA?->nama_dimensi ?? '') }}" required maxlength="100"
                               class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    </div>
                </div>

                <div class="rounded-md border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pernyataan B</p>
                    <div class="mt-2">
                        <label for="pernyataan_b" class="block text-sm font-medium text-slate-700">Teks Pernyataan <span class="text-rose-500">*</span></label>
                        <textarea id="pernyataan_b" name="pernyataan_b" rows="3" required
                                  class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">{{ old('pernyataan_b', $opsiB?->teks_opsi ?? '') }}</textarea>
                    </div>
                    <div class="mt-3">
                        <label for="dimensi_b" class="block text-sm font-medium text-slate-700">Dimensi B <span class="text-rose-500">*</span></label>
                        <input id="dimensi_b" name="dimensi_b" type="text" value="{{ old('dimensi_b', $dimensiB?->nama_dimensi ?? '') }}" required maxlength="100"
                               class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    </div>
                </div>
            </div>
        @elseif ($format === 'Mixed')
            @php
                $tipeFormat = $soal->tipe_format ?? 'pilihan_ganda';
                $opsiList = $soal->opsiJawaban->sortBy('urutan')->values();
            @endphp

            {{-- Info subtes --}}
            <div class="rounded-md bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-600">
                <strong>Subtes:</strong> {{ $soal->nomor <= 20 ? 'SE' : ($soal->nomor <= 40 ? 'WA' : ($soal->nomor <= 60 ? 'AN' : ($soal->nomor <= 76 ? 'GE' : ($soal->nomor <= 96 ? 'RA' : ($soal->nomor <= 116 ? 'ZR' : ($soal->nomor <= 136 ? 'FA' : ($soal->nomor <= 156 ? 'WU' : 'ME'))))))) }}
                &nbsp;|&nbsp; <strong>Tipe Format:</strong> {{ $tipeFormat }}
                &nbsp;|&nbsp; <strong>Nomor:</strong> {{ $soal->nomor }}
            </div>

            {{-- Teks Soal --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">Teks Soal <span class="text-rose-500">*</span></label>
                <textarea name="teks_soal" rows="3" required
                          class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">{{ old('teks_soal', $soal->teks_soal) }}</textarea>
            </div>

            {{-- Upload Gambar Soal (FA/WU) --}}
            @if (in_array($tipeFormat, ['pilihan_gambar']))
                <div>
                    <label class="block text-sm font-medium text-slate-700">Gambar Soal</label>
                    @if ($soal->gambar_soal)
                        <img src="{{ asset('storage/' . $soal->gambar_soal) }}" class="mt-2 h-32 rounded border border-slate-200 object-contain">
                    @endif
                    <input type="file" name="gambar_soal" accept="image/*"
                           class="mt-2 block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-[#2C5F6F] file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-[#234853]">
                </div>
            @endif

            {{-- Kunci Jawaban --}}
            @if (in_array($tipeFormat, ['isian_teks', 'isian_angka']))
                <div>
                    <label class="block text-sm font-medium text-slate-700">Kunci Jawaban <span class="text-rose-500">*</span></label>
                    <input type="text" name="kunci_jawaban" value="{{ old('kunci_jawaban', $soal->kunci_jawaban) }}"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]"
                           placeholder="{{ $tipeFormat === 'isian_angka' ? 'Jawaban angka, contoh: 35' : 'Jawaban teks, contoh: bunga' }}">
                </div>
            @endif

            {{-- Opsi Jawaban (pilihan_ganda dan pilihan_gambar) --}}
            @if (in_array($tipeFormat, ['pilihan_ganda', 'pilihan_gambar']))
                <div class="space-y-3">
                    <p class="text-sm font-medium text-slate-700">Opsi Jawaban (a–e)</p>
                    @foreach (['a','b','c','d','e'] as $huruf)
                        @php $idx = $loop->index; $opsi = $opsiList[$idx] ?? null; @endphp
                        <div class="rounded-md border border-slate-200 bg-slate-50 p-3 space-y-2">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="kunci_jawaban" value="{{ $huruf }}" id="kunci_{{ $huruf }}"
                                       @checked(old('kunci_jawaban', $soal->kunci_jawaban) === $huruf)
                                       class="h-4 w-4 border-slate-300 text-[#2C5F6F]">
                                <label for="kunci_{{ $huruf }}" class="w-6 text-sm font-semibold text-slate-700">{{ strtoupper($huruf) }}.</label>
                                <input type="text" name="opsi[{{ $huruf }}]" value="{{ old('opsi.' . $huruf, $opsi?->teks_opsi ?? '') }}"
                                       placeholder="Teks opsi {{ strtoupper($huruf) }}"
                                       class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none">
                                @if ($tipeFormat === 'pilihan_gambar')
                                    @if ($opsi?->gambar_opsi)
                                        <img src="{{ asset('storage/' . $opsi->gambar_opsi) }}" class="h-20 rounded border border-slate-200 object-contain">
                                    @endif
                                    <input type="file" name="gambar_opsi[{{ $huruf }}]" accept="image/*"
                                           class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-slate-200 file:px-3 file:py-1 file:text-sm file:text-slate-700">
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="sticky bottom-0 -mx-6 mt-4 flex justify-end gap-2 border-t border-slate-200 bg-white px-6 py-3 shadow-[0_-2px_4px_rgba(0,0,0,0.04)]">
            <a href="{{ route('admin.bank-soal.index', ['alat_tes_id' => $soal->alatTes->id]) }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</a>
            <button type="submit" class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">Simpan</button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.admin', ['judulHalaman' => 'Tambah Soal'])

@section('content')
@php
    $warnaFormat = [
        'Pilihan Ganda' => 'bg-blue-600',
        'Skala Likert'  => 'bg-indigo-600',
        'Forced Choice' => 'bg-amber-600',
        'Mixed'         => 'bg-purple-600',
        'Grid'          => 'bg-emerald-600',
    ];
    $format = $alatTes->format_dasar;
@endphp

<div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm">

    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h2 class="text-lg font-semibold text-slate-900">Tambah Soal</h2>
            <span class="inline-block rounded-md {{ $warnaFormat[$format] ?? 'bg-slate-600' }} px-2 py-0.5 text-xs font-semibold text-white">
                {{ $format }}
            </span>
        </div>
        <a href="{{ route('admin.bank-soal.index', ['alat_tes_id' => $alatTes->id]) }}" class="text-sm text-slate-600 hover:text-slate-800">&larr; Kembali</a>
    </div>

    <p class="mb-4 text-sm text-slate-600">
        Alat Tes: <strong>{{ $alatTes->nama }}</strong>.
        Form mengikuti format dasar <strong>{{ $format }}</strong>.
    </p>

    <form method="POST" action="{{ route('admin.bank-soal.simpan', $alatTes->id) }}" class="space-y-4">
        @csrf

        @if ($format === 'Grid')
            <div class="space-y-4">
                <div>
                    <label class="block text-[12px] font-semibold
                           text-[#40484b] uppercase tracking-wider mb-2">
                        Deretan Angka Kolom <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-[12px] text-[#919eab] mb-3">
                        Masukkan 27 angka (0-9) dipisah koma.
                        Contoh: 1,6,2,8,8,2,2,1,6,9,6,4,6,6,2,1,7,6,1,6,2,4,1,6,4,9,1
                    </p>
                    <textarea name="teks_soal" rows="3" required
                        class="w-full bg-[#f2f4f6] border border-[#e0e3e5]
                               rounded-xl px-4 py-3 text-sm font-mono
                               text-[#191c1e] focus:ring-2
                               focus:ring-[#2C5F6F]/40
                               focus:border-[#2C5F6F] outline-none
                               transition-all resize-none"
                        placeholder="1,6,2,8,8,2,2,1,6,9,6,4,6,6,2,1,7,6,1,6,2,4,1,6,4,9,1"
                    >{{ old('teks_soal') }}</textarea>
                    <p class="text-[11px] text-[#919eab] mt-1.5">
                        Angka akan dipisah otomatis saat pengerjaan tes.
                    </p>
                </div>
            </div>
        @elseif ($format === 'Pilihan Ganda')
            <div>
                <label for="teks_soal" class="block text-sm font-medium text-slate-700">Teks Soal <span class="text-rose-500">*</span></label>
                <textarea id="teks_soal" name="teks_soal" rows="3" required
                          class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">{{ old('teks_soal') }}</textarea>
            </div>

            <div class="space-y-3">
                <p class="text-sm font-medium text-slate-700">Opsi Jawaban <span class="text-slate-400">(pilih satu sebagai kunci)</span></p>
                @foreach (['A','B','C','D'] as $huruf)
                    <div class="flex items-center gap-3 rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                        <input type="radio" name="kunci" value="{{ $huruf }}" id="kunci_{{ $huruf }}"
                               @checked(old('kunci') === $huruf)
                               class="h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-600">
                        <label for="kunci_{{ $huruf }}" class="w-6 text-sm font-semibold text-slate-700">{{ $huruf }}.</label>
                        <input type="text" name="opsi[{{ $huruf }}]" value="{{ old('opsi.' . $huruf) }}" required
                               placeholder="Teks opsi {{ $huruf }}"
                               class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    </div>
                @endforeach
            </div>

        @elseif ($format === 'Skala Likert')
            <div>
                <label for="pernyataan" class="block text-sm font-medium text-slate-700">Pernyataan <span class="text-rose-500">*</span></label>
                <textarea id="pernyataan" name="pernyataan" rows="3" required
                          class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">{{ old('pernyataan') }}</textarea>
            </div>

            <div>
                <label for="dimensi" class="block text-sm font-medium text-slate-700">Nama Dimensi <span class="text-rose-500">*</span></label>
                <input id="dimensi" name="dimensi" type="text" value="{{ old('dimensi') }}" required maxlength="100"
                       placeholder="mis. Influence, Dominance, D, Pa, ..."
                       class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                <p class="mt-1 text-xs text-slate-500">&nbsp;</p>
            </div>

        @elseif ($format === 'Forced Choice')
            @php
                $defaultDimensiA = old('dimensi_a');
                $defaultDimensiB = old('dimensi_b');
            @endphp
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-md border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pernyataan A</p>
                    <div class="mt-2">
                        <label for="pernyataan_a" class="block text-sm font-medium text-slate-700">Teks Pernyataan <span class="text-rose-500">*</span></label>
                        <textarea id="pernyataan_a" name="pernyataan_a" rows="3" required
                                  class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">{{ old('pernyataan_a') }}</textarea>
                    </div>
                    <div class="mt-3">
                        <label for="dimensi_a" class="block text-sm font-medium text-slate-700">Dimensi A <span class="text-rose-500">*</span></label>
                        @if ($dimensiList->isNotEmpty())
                            <select id="dimensi_a" name="dimensi_a" required
                                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                                <option value="">-- Pilih Dimensi A --</option>
                                @foreach ($dimensiList as $d)
                                    <option value="{{ $d->kode_dimensi }}" {{ $defaultDimensiA === $d->kode_dimensi ? 'selected' : '' }}>{{ $d->kode_dimensi }} — {{ $d->nama_dimensi }}</option>
                                @endforeach
                            </select>
                        @else
                            <input id="dimensi_a" name="dimensi_a" type="text" value="{{ $defaultDimensiA }}" required maxlength="100"
                                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                        @endif
                    </div>
                </div>

                <div class="rounded-md border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pernyataan B</p>
                    <div class="mt-2">
                        <label for="pernyataan_b" class="block text-sm font-medium text-slate-700">Teks Pernyataan <span class="text-rose-500">*</span></label>
                        <textarea id="pernyataan_b" name="pernyataan_b" rows="3" required
                                  class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">{{ old('pernyataan_b') }}</textarea>
                    </div>
                    <div class="mt-3">
                        <label for="dimensi_b" class="block text-sm font-medium text-slate-700">Dimensi B <span class="text-rose-500">*</span></label>
                        @if ($dimensiList->isNotEmpty())
                            <select id="dimensi_b" name="dimensi_b" required
                                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                                <option value="">-- Pilih Dimensi B --</option>
                                @foreach ($dimensiList as $d)
                                    <option value="{{ $d->kode_dimensi }}" {{ $defaultDimensiB === $d->kode_dimensi ? 'selected' : '' }}>{{ $d->kode_dimensi }} — {{ $d->nama_dimensi }}</option>
                                @endforeach
                            </select>
                        @else
                            <input id="dimensi_b" name="dimensi_b" type="text" value="{{ $defaultDimensiB }}" required maxlength="100"
                                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                        @endif
                    </div>
                </div>
            </div>
        @elseif ($format === 'Mixed')
            @php
                $tipeFormat = old('tipe_format', 'pilihan_ganda');
            @endphp

            {{-- Tipe Format selector --}}
            <div>
                <label for="tipe_format" class="block text-sm font-medium text-slate-700">Tipe Format <span class="text-rose-500">*</span></label>
                <select id="tipe_format" name="tipe_format"
                        class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <option value="pilihan_ganda" {{ old('tipe_format') === 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda</option>
                    <option value="pilihan_gambar" {{ old('tipe_format') === 'pilihan_gambar' ? 'selected' : '' }}>Pilihan Gambar</option>
                    <option value="isian_teks" {{ old('tipe_format') === 'isian_teks' ? 'selected' : '' }}>Isian Teks</option>
                    <option value="isian_angka" {{ old('tipe_format') === 'isian_angka' ? 'selected' : '' }}>Isian Angka</option>
                </select>
            </div>

            {{-- Teks Soal --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">Teks Soal <span class="text-rose-500">*</span></label>
                <textarea name="teks_soal" rows="3" required
                          class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">{{ old('teks_soal') }}</textarea>
            </div>

            {{-- Upload Gambar Soal (FA/WU) --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">Gambar Soal</label>
                <input type="file" name="gambar_soal" accept="image/*"
                       class="mt-2 block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-[#2C5F6F] file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-[#234853]">
            </div>

            {{-- Kunci Jawaban --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">Kunci Jawaban <span class="text-rose-500">*</span></label>
                <input type="text" name="kunci_jawaban" value="{{ old('kunci_jawaban') }}"
                       class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]"
                       placeholder="Jawaban">
            </div>

            {{-- Opsi Jawaban (a–e) --}}
            <div class="space-y-3">
                <p class="text-sm font-medium text-slate-700">Opsi Jawaban (a–e)</p>
                @foreach (['a','b','c','d','e'] as $huruf)
                    <div class="rounded-md border border-slate-200 bg-slate-50 p-3 space-y-2">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="kunci_jawaban" value="{{ $huruf }}" id="kunci_{{ $huruf }}"
                                   @checked(old('kunci_jawaban') === $huruf)
                                   class="h-4 w-4 border-slate-300 text-[#2C5F6F]">
                            <label for="kunci_{{ $huruf }}" class="w-6 text-sm font-semibold text-slate-700">{{ strtoupper($huruf) }}.</label>
                            <input type="text" name="opsi[{{ $huruf }}]" value="{{ old('opsi.' . $huruf) }}"
                                   placeholder="Teks opsi {{ strtoupper($huruf) }}"
                                   class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none">
                            <input type="file" name="gambar_opsi[{{ $huruf }}]" accept="image/*"
                                   class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-md file:border-0 file:bg-slate-200 file:px-3 file:py-1 file:text-sm file:text-slate-700">
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="sticky bottom-0 -mx-6 mt-4 flex justify-end gap-2 border-t border-slate-200 bg-white px-6 py-3 shadow-[0_-2px_4px_rgba(0,0,0,0.04)]">
            <a href="{{ route('admin.bank-soal.index', ['alat_tes_id' => $alatTes->id]) }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</a>
            <button type="submit" class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">Simpan</button>
        </div>
    </form>
</div>
@endsection
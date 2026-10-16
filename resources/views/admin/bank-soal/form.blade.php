@php
    $ubah = $soal->exists;
    $format = $alatTes->format_dasar;
    $opsiList = $soal->opsiJawaban->sortBy('urutan')->values();
    $urlKembali = route('admin.bank-soal.index', ['alat_tes_id' => $alatTes->id]);
    $legend = 'mb-3 text-xs font-semibold uppercase tracking-wider text-muted-foreground';
    $berkas = 'h-auto py-1.5 file:mr-3 file:rounded file:border-0 file:bg-muted file:px-2 file:py-1 file:text-sm file:font-medium';
    $subtes = $format === 'Mixed' ? \App\Http\Controllers\Admin\BankSoalController::subtesIst($soal->nomor) : null;
@endphp
@extends('layouts.admin', ['judulHalaman' => $ubah ? 'Ubah Soal' : 'Tambah Soal'])

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div class="space-y-4">
        <a href="{{ $urlKembali }}" class="inline-flex items-center gap-1 text-sm text-muted-foreground transition-colors hover:text-foreground">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>Bank soal {{ $alatTes->kode }}
        </a>
        <div class="flex min-w-0 items-start gap-4">
            <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-brand-ink font-mono text-sm font-semibold text-white">{{ $alatTes->kode }}</span>
            <div class="min-w-0 space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight">{{ $ubah ? 'Ubah soal nomor ' . $soal->nomor : 'Tambah soal' }}</h1>
                <p class="text-sm text-muted-foreground">
                    {{ $alatTes->nama }} · {{ $format }}
                    @if ($subtes) · subtes {{ $subtes }} @endif
                    @unless ($ubah) · akan menjadi nomor {{ $soal->nomor }} @endunless
                </p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <x-ui.alert variant="destructive" icon="error" title="Soal belum tersimpan">Periksa kembali isian yang ditandai di bawah.</x-ui.alert>
    @endif

    @if ($format === 'Skala Likert')
        <x-ui.alert variant="warning" icon="construction" title="Format belum didukung">Bank soal belum bisa menyusun soal Skala Likert. Gunakan format lain pada alat tes ini.</x-ui.alert>
    @else
    <x-ui.card>
        <form method="POST" enctype="multipart/form-data"
              action="{{ $ubah ? route('admin.bank-soal.update', $soal->id) : route('admin.bank-soal.simpan', $alatTes->id) }}"
              class="grid gap-6 p-6">
            @csrf
            @if ($ubah) @method('PUT') @endif

            @if ($format === 'Grid')
                <x-ui.field label="Deretan angka kolom" for="f-teks" error="teks_soal" description="Angka 0–9 dipisah koma, biasanya 27 angka per kolom. Angka dipecah otomatis saat tes dikerjakan.">
                    <x-ui.textarea id="f-teks" name="teks_soal" rows="3" required class="font-mono tracking-wider" placeholder="1,6,2,8,8,2,2,1,6,9,6,4,6,6,2,1,7,6,1,6,2,4,1,6,4,9,1">{{ old('teks_soal', $soal->teks_soal) }}</x-ui.textarea>
                </x-ui.field>

            @elseif ($format === 'Forced Choice')
                <p class="text-sm text-muted-foreground">Peserta memilih satu dari dua pernyataan. Pernyataan yang dipilih menambah skor dimensinya.</p>
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach (['a', 'b'] as $i => $h)
                        @php $o = $opsiList->get($i); $dimensiLama = old("dimensi_$h", $o?->bobotOpsiDimensi->first()?->dimensi?->kode_dimensi ?? ''); @endphp
                        <fieldset class="grid content-start gap-4 rounded-lg border border-border p-4">
                            <legend class="px-1 text-sm font-semibold">Pernyataan {{ strtoupper($h) }}</legend>
                            <x-ui.field label="Teks pernyataan" for="f-p{{ $h }}" error="pernyataan_{{ $h }}">
                                <x-ui.textarea id="f-p{{ $h }}" name="pernyataan_{{ $h }}" rows="3" required>{{ old("pernyataan_$h", $o?->teks_opsi) }}</x-ui.textarea>
                            </x-ui.field>
                            <x-ui.field label="Dimensi" for="f-d{{ $h }}" error="dimensi_{{ $h }}">
                                @if ($dimensiList->isNotEmpty())
                                    <x-ui.select id="f-d{{ $h }}" name="dimensi_{{ $h }}" required>
                                        <option value="">Pilih dimensi</option>
                                        @foreach ($dimensiList as $d)
                                            <option value="{{ $d->kode_dimensi }}" @selected($dimensiLama === $d->kode_dimensi)>{{ $d->kode_dimensi }} — {{ $d->nama_dimensi }}</option>
                                        @endforeach
                                    </x-ui.select>
                                @else
                                    <x-ui.input id="f-d{{ $h }}" name="dimensi_{{ $h }}" required maxlength="100" value="{{ $dimensiLama }}" placeholder="Kode dimensi" />
                                @endif
                            </x-ui.field>
                        </fieldset>
                    @endforeach
                </div>
                @if ($dimensiList->isEmpty())
                    <x-ui.alert variant="warning" icon="psychology">Alat tes ini belum punya dimensi. <a href="{{ route('admin.alat-tes.dimensi', $alatTes->id) }}" class="font-medium underline underline-offset-4">Atur dimensi</a> terlebih dahulu agar soal bisa diskor.</x-ui.alert>
                @endif

            @else
                {{-- Pilihan Ganda & Mixed (IST): satu form, tipe tetap pilihan_ganda untuk format Pilihan Ganda --}}
                @php
                    $mixed = $format === 'Mixed';
                    $huruf = $mixed ? ['a', 'b', 'c', 'd', 'e'] : ['a', 'b', 'c', 'd'];
                    $kunciLama = strtolower((string) old('kunci_jawaban', $soal->kunci_jawaban));
                    $kunciGe = json_decode((string) old('kunci_jawaban', $soal->kunci_jawaban), true);
                    $kunciGe = is_array($kunciGe) ? $kunciGe : [];
                    $tipeAwal = $mixed ? old('tipe_format', $soal->tipe_format) : 'pilihan_ganda';
                @endphp
                <div class="grid gap-6"
                     x-data="{ tipe: @js($tipeAwal), poin2: @js(implode(', ', $kunciGe['2'] ?? [])), poin1: @js(implode(', ', $kunciGe['1'] ?? [])),
                               pisah(v) { return v.split(',').map(s => s.trim()).filter(Boolean); },
                               get pilihan() { return ['pilihan_ganda', 'pilihan_gambar'].includes(this.tipe); } }">

                    <fieldset class="grid gap-4 {{ $mixed ? 'sm:grid-cols-[12rem_minmax(0,1fr)]' : '' }}">
                        <legend class="{{ $legend }}">Pertanyaan</legend>
                        @if ($mixed)
                            <x-ui.field label="Tipe soal" for="f-tipe" error="tipe_format">
                                <x-ui.select id="f-tipe" name="tipe_format" required x-model="tipe">
                                    <option value="pilihan_ganda">Pilihan ganda</option>
                                    <option value="pilihan_gambar">Pilihan gambar</option>
                                    <option value="isian_teks">Isian teks (2/1 poin)</option>
                                    <option value="isian_angka">Isian angka</option>
                                </x-ui.select>
                            </x-ui.field>
                        @endif
                        <x-ui.field label="Teks soal" for="f-teks" error="teks_soal">
                            <x-ui.textarea id="f-teks" name="teks_soal" rows="3" ::required="pilihan" placeholder="Tulis pertanyaan, atau pasangan kata untuk soal isian">{{ old('teks_soal', $soal->teks_soal) }}</x-ui.textarea>
                        </x-ui.field>
                        @if ($mixed)
                            <x-ui.field x-show="tipe === 'pilihan_gambar'" label="Gambar soal" for="f-gambar" error="gambar_soal" description="PNG/JPG, maksimal 2 MB. Kosongkan untuk mempertahankan gambar lama." class="sm:col-span-2">
                                <div class="flex flex-wrap items-center gap-3">
                                    @if ($soal->gambar_soal)
                                        <img src="{{ asset('storage/' . $soal->gambar_soal) }}" alt="Gambar soal saat ini" class="h-20 rounded-lg border border-border object-contain">
                                    @endif
                                    <x-ui.input id="f-gambar" name="gambar_soal" type="file" accept="image/*" class="max-w-sm {{ $berkas }}" />
                                </div>
                            </x-ui.field>
                        @endif
                    </fieldset>

                    {{-- Opsi: fieldset dinonaktifkan saat tipe isian supaya tidak ikut terkirim --}}
                    <fieldset class="grid gap-3" x-show="pilihan" :disabled="!pilihan">
                        <legend class="{{ $legend }}">Opsi jawaban <span class="normal-case tracking-normal">· pilih bulatan pada kunci jawaban</span></legend>
                        @foreach ($huruf as $i => $h)
                            @php $o = $opsiList->get($i); @endphp
                            <div class="flex flex-wrap items-center gap-3 rounded-lg border border-border p-2 pl-3 has-checked:border-emerald-300 has-checked:bg-emerald-50">
                                <input type="radio" name="kunci_jawaban" value="{{ $h }}" id="f-kunci-{{ $h }}" @checked($kunciLama === $h)
                                       class="size-4 cursor-pointer accent-emerald-600" aria-label="Jadikan {{ strtoupper($h) }} kunci jawaban">
                                <label for="f-kunci-{{ $h }}" class="w-4 cursor-pointer text-sm font-semibold">{{ strtoupper($h) }}</label>
                                <x-ui.input name="opsi[{{ $h }}]" value="{{ old('opsi.' . $h, $o?->teks_opsi) }}" placeholder="Teks opsi {{ strtoupper($h) }}" class="min-w-40 flex-1" :required="! $mixed" aria-label="Teks opsi {{ strtoupper($h) }}" />
                                @if ($mixed)
                                    <div x-show="tipe === 'pilihan_gambar'" class="flex items-center gap-2">
                                        @if ($o?->gambar_opsi)
                                            <img src="{{ asset('storage/' . $o->gambar_opsi) }}" alt="Gambar opsi {{ strtoupper($h) }}" class="h-9 rounded border border-border object-contain">
                                        @endif
                                        <x-ui.input name="gambar_opsi[{{ $h }}]" type="file" accept="image/*" class="w-56 {{ $berkas }}" aria-label="Gambar opsi {{ strtoupper($h) }}" />
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        @foreach (['kunci_jawaban', 'opsi', 'opsi.*', 'gambar_opsi.*'] as $kunciError)
                            @error($kunciError)<p class="text-xs font-medium text-destructive">{{ $message }}</p>@enderror
                        @endforeach
                    </fieldset>

                    @if ($mixed)
                        <fieldset class="grid gap-4 sm:grid-cols-2" x-show="tipe === 'isian_teks'" :disabled="tipe !== 'isian_teks'">
                            <legend class="{{ $legend }}">Kunci jawaban</legend>
                            <x-ui.field label="Jawaban benar (2 poin)" for="f-poin2" description="Pisahkan beberapa jawaban dengan koma.">
                                <x-ui.input id="f-poin2" x-model="poin2" placeholder="bunga, kembang" ::required="tipe === 'isian_teks'" />
                            </x-ui.field>
                            <x-ui.field label="Jawaban kurang tepat (1 poin, opsional)" for="f-poin1" description="Masih diterima, tetapi bernilai separuh.">
                                <x-ui.input id="f-poin1" x-model="poin1" placeholder="tumbuhan, tanaman" />
                            </x-ui.field>
                            <input type="hidden" name="kunci_jawaban" :value="'{&quot;2&quot;:' + JSON.stringify(pisah(poin2)) + ',&quot;1&quot;:' + JSON.stringify(pisah(poin1)) + '}'">
                            @error('kunci_jawaban')<p class="text-xs font-medium text-destructive sm:col-span-2">{{ $message }}</p>@enderror
                        </fieldset>

                        <fieldset class="grid gap-4" x-show="tipe === 'isian_angka'" :disabled="tipe !== 'isian_angka'">
                            <legend class="{{ $legend }}">Kunci jawaban</legend>
                            <x-ui.field label="Jawaban benar" for="f-angka" error="kunci_jawaban">
                                <x-ui.input id="f-angka" name="kunci_jawaban" value="{{ old('kunci_jawaban', in_array($soal->tipe_format, ['isian_angka']) ? $soal->kunci_jawaban : '') }}" placeholder="35" class="max-w-48 font-mono" ::required="tipe === 'isian_angka'" />
                            </x-ui.field>
                        </fieldset>
                    @endif
                </div>
            @endif

            <div class="flex flex-col-reverse gap-2 border-t border-border pt-6 sm:flex-row sm:justify-end">
                <x-ui.button variant="outline" :href="$urlKembali">Batal</x-ui.button>
                <x-ui.button type="submit"><span class="material-symbols-outlined">save</span>{{ $ubah ? 'Simpan perubahan' : 'Simpan soal' }}</x-ui.button>
            </div>
        </form>
    </x-ui.card>
    @endif
</div>
@endsection

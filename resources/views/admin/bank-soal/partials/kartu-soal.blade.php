{{-- Satu butir soal. Butuh $soal (Soal + opsiJawaban.bobotOpsiDimensi.dimensi), $alat, $bisaUbah, $bisaHapus. --}}
@php
    $tipe = $soal->tipe_format;
    $opsi = $soal->opsiJawaban->sortBy('urutan')->values();
    $kunci = strtolower((string) $soal->kunci_jawaban);
    $labelTipe = [
        'pilihan_ganda' => 'Pilihan ganda', 'pilihan_gambar' => 'Pilihan gambar', 'isian_teks' => 'Isian teks',
        'isian_angka' => 'Isian angka', 'forced_choice' => 'Forced choice', 'grid' => 'Deret angka', 'memori' => 'Memori',
    ];
    $kurangGambar = $tipe === 'pilihan_gambar' && ! $soal->gambar_soal && $opsi->whereNotNull('gambar_opsi')->isEmpty();
@endphp
<x-ui.card class="p-5">
    <div class="mb-4 flex items-start gap-3">
        <span class="flex h-7 min-w-10 shrink-0 items-center justify-center rounded-md bg-muted px-2 font-mono text-xs font-semibold tabular-nums">{{ str_pad($soal->nomor, 3, '0', STR_PAD_LEFT) }}</span>
        <div class="flex min-w-0 flex-1 flex-wrap items-center gap-1.5 pt-0.5">
            @if ($alat->format_dasar === 'Mixed')
                <x-ui.badge variant="outline" class="font-mono">{{ \App\Http\Controllers\Admin\BankSoalController::subtesIst($soal->nomor) }}</x-ui.badge>
            @endif
            <x-ui.badge variant="secondary">{{ $labelTipe[$tipe] ?? $tipe }}</x-ui.badge>
            @if ($kurangGambar)
                <x-ui.badge variant="warning"><span class="material-symbols-outlined text-[14px]">image_not_supported</span>Gambar belum diunggah</x-ui.badge>
            @endif
        </div>
        @if ($bisaUbah || $bisaHapus)
            <div class="-mr-2 -mt-1 flex shrink-0">
                @if ($bisaUbah)
                    <x-ui.button variant="ghost" size="icon-sm" :href="route('admin.bank-soal.edit', $soal->id)" title="Ubah soal">
                        <span class="material-symbols-outlined">edit</span><span class="sr-only">Ubah soal nomor {{ $soal->nomor }}</span>
                    </x-ui.button>
                @endif
                @if ($bisaHapus)
                    <x-ui.button variant="ghost" size="icon-sm" class="hover:text-destructive" title="Hapus soal"
                        @click="konfirmasi = { buka: true, label: 'Hapus', judul: 'Hapus soal nomor {{ $soal->nomor }}', pesan: 'Soal ini tidak akan muncul lagi di sesi tes. Jawaban peserta yang sudah tersimpan tetap ada.', url: {{ Js::from(route('admin.bank-soal.hapus', $soal->id)) }} }">
                        <span class="material-symbols-outlined">delete</span><span class="sr-only">Hapus soal nomor {{ $soal->nomor }}</span>
                    </x-ui.button>
                @endif
            </div>
        @endif
    </div>

    @if ($tipe === 'grid')
        <p class="overflow-x-auto rounded-lg border border-border bg-muted/50 px-3 py-2 font-mono text-sm tracking-[0.2em]">{{ str_replace(',', ' ', $soal->teks_soal) }}</p>
        <p class="mt-2 text-xs text-muted-foreground">{{ count(explode(',', $soal->teks_soal)) }} angka per kolom</p>

    @elseif ($tipe === 'forced_choice')
        <div class="grid items-stretch gap-3 md:grid-cols-[1fr_auto_1fr]">
            @foreach ([0 => 'A', 1 => 'B'] as $i => $huruf)
                @php $o = $opsi->get($i); $dimensi = $o?->bobotOpsiDimensi->first()?->dimensi; @endphp
                @if ($i === 1)
                    <span class="self-center text-center text-xs font-medium text-muted-foreground">atau</span>
                @endif
                <div class="flex flex-col gap-2 rounded-lg border border-border p-3">
                    <p class="text-xs font-semibold text-muted-foreground">Pernyataan {{ $huruf }}</p>
                    <p class="flex-1 text-sm">{{ $o?->teks_opsi ?? '–' }}</p>
                    @if ($dimensi)
                        <x-ui.badge variant="outline" title="{{ $dimensi->nama_dimensi }}"><span class="font-mono">{{ $dimensi->kode_dimensi }}</span><span class="max-w-48 truncate text-muted-foreground">{{ $dimensi->nama_dimensi }}</span></x-ui.badge>
                    @else
                        <x-ui.badge variant="warning">Belum ada dimensi</x-ui.badge>
                    @endif
                </div>
            @endforeach
        </div>

    @else
        @if ($soal->teks_soal !== '')
            <p class="text-sm leading-relaxed">{{ $soal->teks_soal }}</p>
        @endif
        @if ($soal->gambar_soal)
            <img src="{{ asset('storage/' . $soal->gambar_soal) }}" alt="Gambar soal nomor {{ $soal->nomor }}" class="mt-3 h-28 max-w-full rounded-lg border border-border object-contain">
        @endif

        @if (in_array($tipe, ['isian_teks', 'isian_angka']))
            @php $kunciGe = json_decode((string) $soal->kunci_jawaban, true); @endphp
            <div class="mt-3 grid gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-900">
                @if (is_array($kunciGe))
                    <p><span class="text-emerald-700">2 poin:</span> {{ implode(', ', $kunciGe['2'] ?? []) ?: '–' }}</p>
                    <p><span class="text-emerald-700">1 poin:</span> {{ implode(', ', $kunciGe['1'] ?? []) ?: '–' }}</p>
                @else
                    <p><span class="text-emerald-700">Kunci:</span> <span class="font-medium">{{ $soal->kunci_jawaban ?? '–' }}</span></p>
                @endif
            </div>
        @elseif ($opsi->isNotEmpty())
            <ul class="mt-3 grid gap-2 sm:grid-cols-2 {{ $opsi->count() > 4 ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }}">
                @foreach ($opsi as $i => $o)
                    @php $benar = $kunci === chr(97 + $i); @endphp
                    <li class="flex items-center gap-2 rounded-lg border px-3 py-2 text-sm {{ $benar ? 'border-emerald-300 bg-emerald-50' : 'border-border' }}">
                        <span class="flex size-6 shrink-0 items-center justify-center rounded-full text-xs font-semibold {{ $benar ? 'bg-emerald-600 text-white' : 'bg-muted text-muted-foreground' }}">{{ chr(65 + $i) }}</span>
                        @if ($o->gambar_opsi)
                            <img src="{{ asset('storage/' . $o->gambar_opsi) }}" alt="Opsi {{ chr(65 + $i) }}" class="h-12 max-w-24 rounded border border-border object-contain">
                        @endif
                        <span class="min-w-0 flex-1 {{ $o->teks_opsi === '' ? 'italic text-muted-foreground' : '' }}">{{ $o->teks_opsi !== '' ? $o->teks_opsi : 'Tanpa teks' }}</span>
                        @if ($benar)
                            <span class="material-symbols-outlined text-[18px] text-emerald-600" title="Kunci jawaban">check_circle</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    @endif
</x-ui.card>

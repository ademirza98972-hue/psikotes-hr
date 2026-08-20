@extends('layouts.peserta', ['judulHalaman' => 'Panduan Kraepelin'])

@section('content')
@php
$adaTimerKolom = (bool) ($alatTes?->batas_waktu_per_soal_aktif);
$detikKolom    = $alatTes?->batas_waktu_per_soal_detik;
$adaTimerTotal = (bool) ($alatTes?->durasi_total_menit);

if ($adaTimerTotal && $adaTimerKolom) {
    $infoWaktu = $alatTes->durasi_total_menit . ' menit total · ' . $detikKolom . ' detik per kolom';
    $ikonWaktu = 'schedule';
} elseif ($adaTimerTotal) {
    $infoWaktu = $alatTes->durasi_total_menit . ' menit';
    $ikonWaktu = 'schedule';
} elseif ($adaTimerKolom) {
    $infoWaktu = $detikKolom . ' detik per kolom';
    $ikonWaktu = 'timer';
} else {
    $infoWaktu = 'Tidak ada batas waktu';
    $ikonWaktu = 'timer_off';
}
@endphp
<div class="max-w-2xl mx-auto pb-8 space-y-5">

    {{-- Header --}}
    <div>
        <div class="inline-flex items-center gap-2 rounded-lg px-3 py-1.5 mb-3"
             style="background:#fef3c7;color:#b45309;border:1px solid #fde68a">
            <span class="material-symbols-outlined text-[14px]">calculate</span>
            <span class="text-[11px] font-bold uppercase tracking-wider">Kraepelin — Kecepatan & Ketelitian</span>
        </div>
        <h2 class="text-[22px] font-semibold text-[#00303c]">Tes Penjumlahan Kraepelin</h2>
        <div class="flex flex-wrap items-center gap-3 mt-2">
            <span class="inline-flex items-center gap-1 text-[12px] text-[#40484b]">
                <span class="material-symbols-outlined text-[14px]">view_column</span>
                50 kolom
            </span>
            <span class="text-[#c0c8cb]">·</span>
            <span class="inline-flex items-center gap-1 text-[12px] text-[#40484b]">
                <span class="material-symbols-outlined text-[14px]">{{ $ikonWaktu }}</span>
                {{ $infoWaktu }}
            </span>
        </div>
    </div>

    {{-- Cara Pengerjaan --}}
    <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
        <div class="border-b border-[#e0e3e5] px-6 py-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Cara Pengerjaan</p>
        </div>
        <div class="px-6 py-5 space-y-3">
            <p class="text-[14px] text-[#191c1e] leading-relaxed">
                Setiap kolom berisi <strong>deretan angka</strong> (0–9). Jumlahkan dua angka yang berdekatan, dimulai dari <strong>bawah ke atas</strong>, lalu masukkan <strong>angka satuannya saja</strong> dari hasil penjumlahan tersebut.
            </p>
            <div class="rounded-lg px-4 py-3 flex items-start gap-2"
                 style="background:#fef3c7;border:1px solid #fde68a">
                <span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5" style="color:#b45309">lightbulb</span>
                <p class="text-[13px]" style="color:#b45309">
                    Jika penjumlahan menghasilkan <strong>dua digit</strong> (misal 15), tulis hanya angka <strong>satuan</strong>-nya saja (tulis 5).
                </p>
            </div>
        </div>
    </div>

    {{-- Yang Perlu Diperhatikan --}}
    <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
        <div class="border-b border-[#e0e3e5] px-6 py-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Yang Perlu Diperhatikan</p>
        </div>
        <div class="divide-y divide-[#f0f2f3]">
            @php
            $rules = [
                'Kerjakan dari <strong>bawah ke atas</strong> dalam satu kolom sebelum berpindah ke kolom berikutnya.',
                'Tulis hanya <strong>angka satuan</strong> dari hasil penjumlahan — abaikan puluhannya.',
                $adaTimerKolom
                    ? 'Setiap kolom dikerjakan dalam <strong>' . $detikKolom . ' detik</strong>. Kolom berpindah otomatis saat waktu habis.'
                    : 'Pindah kolom dilakukan <strong>secara manual</strong> saat Anda selesai.',
                'Kerjakan <strong>secepat dan seteliti</strong> mungkin — kecepatan dan ketepatan sama-sama dinilai.',
            ];
            @endphp
            @foreach($rules as $i => $rule)
            <div class="flex items-start gap-4 px-6 py-4">
                <div class="shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold text-white mt-0.5"
                     style="background:#b45309;min-width:1.5rem">{{ $i + 1 }}</div>
                <p class="text-[13px] text-[#40484b] leading-relaxed pt-0.5">{!! $rule !!}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Contoh --}}
    <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
        <div class="border-b border-[#e0e3e5] px-6 py-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Contoh</p>
        </div>
        <div class="px-6 py-5">
            <p class="text-[12px] text-[#71787c] mb-5">Pengerjaan dimulai dari pasangan angka paling <strong>bawah</strong>, naik ke atas satu per satu:</p>

            {{-- Visual kolom mirip tampilan asli --}}
            <div class="flex justify-center gap-10">
                <div class="text-center">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-[#71787c] mb-3">Tampilan Kolom</p>
                    <div class="inline-flex flex-col items-center border border-[#e0e3e5] rounded-xl overflow-hidden bg-white">
                        @php
                        // Tampilkan dari atas ke bawah, pasangan aktif (kuning) di bawah
                        $baris = [
                            ['angka' => 5, 'aktif' => false],
                            ['angka' => 3, 'aktif' => false],
                            ['angka' => 8, 'aktif' => false],
                            ['angka' => 6, 'aktif' => true],   // pasangan aktif atas
                            ['angka' => 4, 'aktif' => true],   // pasangan aktif bawah
                        ];
                        @endphp
                        @foreach($baris as $b)
                        <div class="w-14 h-12 flex items-center justify-center text-[20px] font-bold font-mono border-b border-[#f0f2f3] last:border-0
                                    {{ $b['aktif'] ? '' : 'text-[#191c1e]' }}"
                             @if($b['aktif']) style="background:#fef3c7;color:#b45309" @endif>
                            {{ $b['angka'] }}
                        </div>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-[#71787c] mt-2">↑ bawah</p>
                </div>

                <div class="flex flex-col justify-center gap-4 text-[13px] text-[#40484b]">
                    <div class="rounded-lg px-4 py-3 border border-[#e0e3e5] bg-[#f8fbfc]">
                        <p class="font-semibold text-[#191c1e] mb-1">Pasangan aktif (kuning): 6 dan 4</p>
                        <p>6 + 4 = 10 → tulis <strong class="text-[#b45309]">0</strong></p>
                    </div>
                    <div class="rounded-lg px-4 py-3 border border-[#e0e3e5] bg-[#f8fbfc]">
                        <p class="font-semibold text-[#191c1e] mb-1">Pasangan berikutnya (naik): 8 dan 6</p>
                        <p>8 + 6 = 14 → tulis <strong class="text-[#b45309]">4</strong></p>
                    </div>
                    <div class="rounded-lg px-4 py-3 border border-[#e0e3e5] bg-[#f8fbfc]">
                        <p class="font-semibold text-[#191c1e] mb-1">Terus naik: 3 dan 8</p>
                        <p>3 + 8 = 11 → tulis <strong class="text-[#b45309]">1</strong></p>
                    </div>
                </div>
            </div>

            <div class="mt-5 rounded-lg bg-[#f7f9fb] border border-[#e0e3e5] px-4 py-3 flex items-start gap-2">
                <span class="material-symbols-outlined text-[15px] text-[#2C5F6F] shrink-0 mt-0.5">info</span>
                <p class="text-[12px] text-[#40484b] leading-relaxed">Dua angka yang disorot kuning adalah pasangan yang sedang dikerjakan. Masukkan angka satuan dari jumlahnya menggunakan keypad di layar.</p>
            </div>
        </div>
    </div>

    {{-- Tombol Mulai --}}
    <form method="POST" action="{{ route('peserta.tes.panduan-kraepelin-mulai', $sesiId) }}">
        @csrf
        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3.5 text-[15px] font-semibold text-white shadow-sm transition-all active:scale-95"
                style="background:#b45309">
            <span class="material-symbols-outlined text-[18px]">play_arrow</span>
            Mulai Kraepelin
        </button>
        <p class="mt-2 text-center text-[11px] text-[#71787c]">
            Pastikan Anda siap sebelum memulai
        </p>
    </form>

</div>
@endsection

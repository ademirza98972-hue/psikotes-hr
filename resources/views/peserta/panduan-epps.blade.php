@extends('layouts.peserta', ['judulHalaman' => 'Panduan EPPS'])

@section('content')
@php
$adaTimerTotal  = (bool) ($alatTes?->durasi_total_menit);
$adaTimerPerSoal = (bool) ($alatTes?->batas_waktu_per_soal_aktif);

if ($adaTimerTotal && $adaTimerPerSoal) {
    $infoWaktu = $alatTes->durasi_total_menit . ' menit total · '
               . $alatTes->batas_waktu_per_soal_detik . ' detik per soal';
    $ikonWaktu = 'schedule';
} elseif ($adaTimerTotal) {
    $infoWaktu = $alatTes->durasi_total_menit . ' menit';
    $ikonWaktu = 'schedule';
} elseif ($adaTimerPerSoal) {
    $infoWaktu = $alatTes->batas_waktu_per_soal_detik . ' detik per soal';
    $ikonWaktu = 'timer';
} else {
    $infoWaktu = 'Tidak ada batas waktu';
    $ikonWaktu = 'timer_off';
}

$ruleWaktu = ($adaTimerTotal || $adaTimerPerSoal)
    ? 'Perhatikan <strong>batas waktu</strong> yang tertera di layar saat mengerjakan soal.'
    : 'Tidak ada batas waktu — kerjakan dengan <strong>tenang dan tidak terburu-buru</strong>.';
@endphp
<div class="max-w-2xl mx-auto pb-8 space-y-5">

    {{-- Header --}}
    <div>
        <div class="inline-flex items-center gap-2 rounded-lg px-3 py-1.5 mb-3"
             style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe">
            <span class="material-symbols-outlined text-[14px]">psychology</span>
            <span class="text-[11px] font-bold uppercase tracking-wider">EPPS — Kepribadian</span>
        </div>
        <h2 class="text-[22px] font-semibold text-[#00303c]">Edwards Personal Preference Schedule</h2>
        <div class="flex items-center gap-3 mt-2">
            <span class="inline-flex items-center gap-1 text-[12px] text-[#40484b]">
                <span class="material-symbols-outlined text-[14px]">quiz</span>
                225 soal
            </span>
            <span class="text-[#c0c8cb]">·</span>
            <span class="inline-flex items-center gap-1 text-[12px] text-[#40484b]">
                <span class="material-symbols-outlined text-[14px]">{{ $ikonWaktu }}</span>
                {{ $infoWaktu }}
            </span>
            <span class="text-[#c0c8cb]">·</span>
            <span class="inline-flex items-center gap-1 text-[12px] text-[#40484b]">
                <span class="material-symbols-outlined text-[14px]">swap_horiz</span>
                Pilih 1 dari 2 pernyataan
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
                Setiap soal menyajikan <strong>dua pernyataan</strong>. Pilih <strong>satu pernyataan</strong> yang paling menggambarkan diri Anda saat ini.
            </p>
            <div class="rounded-lg px-4 py-3 flex items-start gap-2"
                 style="background:#eff6ff;border:1px solid #bfdbfe">
                <span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5" style="color:#1d4ed8">lightbulb</span>
                <p class="text-[13px]" style="color:#1d4ed8">
                    Tidak ada jawaban benar atau salah. Jawab sesuai keadaan Anda yang <strong>sebenarnya</strong>, bukan yang Anda anggap ideal.
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
                'Jawab secara <strong>jujur dan spontan</strong> — jangan berpikir terlalu lama.',
                'Setiap soal <strong>harus dijawab</strong>, tidak ada yang boleh dilewati.',
                'Beberapa soal terasa serupa — tetap pilih yang <strong>paling mendekati</strong> kondisi Anda saat ini.',
                $ruleWaktu,
            ];
            @endphp
            @foreach($rules as $i => $rule)
            <div class="flex items-start gap-4 px-6 py-4">
                <div class="shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold text-white mt-0.5"
                     style="background:#1d4ed8;min-width:1.5rem">{{ $i + 1 }}</div>
                <p class="text-[13px] text-[#40484b] leading-relaxed pt-0.5">{!! $rule !!}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Contoh --}}
    <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
        <div class="border-b border-[#e0e3e5] px-6 py-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Contoh Soal</p>
        </div>
        <div class="px-6 py-5">
            <p class="text-[12px] text-[#71787c] mb-4">Pilih <strong>salah satu</strong> pernyataan yang paling menggambarkan Anda:</p>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 mb-4">
                <div class="flex items-start gap-3 rounded-xl p-4 border-2"
                     style="border-color:#1d4ed8;background:#eff6ff">
                    <div class="shrink-0 mt-0.5 h-4 w-4 rounded-full border-2 flex items-center justify-center"
                         style="border-color:#1d4ed8;background:#1d4ed8;min-width:1rem">
                        <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                    </div>
                    <p class="text-[13px] leading-relaxed font-medium" style="color:#1e40af">Saya suka mengerjakan tugas dengan sebaik-baiknya.</p>
                </div>
                <div class="flex items-start gap-3 rounded-xl p-4 border-2 border-[#e0e3e5]">
                    <div class="shrink-0 mt-0.5 h-4 w-4 rounded-full border-2 border-[#c0c8cb]" style="min-width:1rem"></div>
                    <p class="text-[13px] text-[#40484b] leading-relaxed">Saya suka berteman dengan banyak orang.</p>
                </div>
            </div>
            <div class="rounded-lg bg-[#f7f9fb] border border-[#e0e3e5] px-4 py-3 flex items-start gap-2">
                <span class="material-symbols-outlined text-[15px] text-[#2C5F6F] shrink-0 mt-0.5">info</span>
                <p class="text-[12px] text-[#40484b] leading-relaxed">Pernyataan pertama dipilih (ditandai biru). Pilih sesuai yang <strong>paling menggambarkan Anda</strong>.</p>
            </div>
        </div>
    </div>

    {{-- Tombol Mulai --}}
    <form method="POST" action="{{ route('peserta.tes.panduan-epps-mulai', $sesiId) }}">
        @csrf
        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3.5 text-[15px] font-semibold text-white shadow-sm transition-all active:scale-95"
                style="background:#1d4ed8">
            <span class="material-symbols-outlined text-[18px]">play_arrow</span>
            Mulai EPPS
        </button>
        <p class="mt-2 text-center text-[11px] text-[#71787c]">
            Pastikan Anda siap sebelum memulai
        </p>
    </form>

</div>
@endsection

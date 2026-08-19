@extends('layouts.peserta', ['judulHalaman' => 'Hafalan ME — IST'])

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <div class="inline-flex items-center gap-2 rounded-lg bg-[#2C5F6F]/10 px-3 py-1.5 mb-3">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">IST — Subtes ME</span>
        </div>
        <h2 class="text-[22px] font-semibold text-[#00303c]">Fase Hafalan</h2>
        <p class="mt-1 text-[13px] text-[#40484b]">Hafalkan kata-kata di bawah ini. Setelah waktu habis, Anda akan menjawab soal berdasarkan hafalan ini.</p>
    </div>

    {{-- Timer --}}
    <div class="mb-6 rounded-xl border border-[#2C5F6F]/30 bg-[#2C5F6F]/5 px-6 py-4 flex items-center justify-between">
        <div>
            <p class="text-[12px] font-semibold uppercase tracking-wider text-[#2C5F6F]">Waktu Hafalan</p>
            <p class="text-[11px] text-[#40484b] mt-0.5">Halaman akan berpindah otomatis saat waktu habis</p>
        </div>
        <div id="timer-display" class="text-[32px] font-bold tabular-nums text-[#2C5F6F]">3:00</div>
    </div>

    {{-- Daftar Hafalan --}}
    <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm overflow-hidden">
        <div class="border-b border-[#e0e3e5] px-6 py-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Daftar Kata</p>
        </div>
        <div class="divide-y divide-[#e0e3e5]">
            @php
            $daftarHafalan = [
                'BUNGA'    => ['SOKA', 'LARAT', 'FLAMBOYAN', 'YASMIN', 'DAHLIA'],
                'PERKAKAS' => ['WAJAN', 'JARUM', 'KIKIR', 'CANGKUL', 'PALU'],
                'BURUNG'   => ['ITIK', 'ELANG', 'WALET', 'TEKUKUR', 'NURI'],
                'KESENIAN' => ['QUINTET', 'ARCA', 'OPERA', 'UKIRAN', 'GAMELAN'],
                'BINATANG' => ['RUSA', 'MUSANG', 'BERUANG', 'HARIMAU', 'ZEBRA'],
            ];
            $warna = [
                'BUNGA'    => ['bg-rose-50',   'text-rose-700',   'bg-rose-100'],
                'PERKAKAS' => ['bg-amber-50',  'text-amber-700',  'bg-amber-100'],
                'BURUNG'   => ['bg-sky-50',    'text-sky-700',    'bg-sky-100'],
                'KESENIAN' => ['bg-violet-50', 'text-violet-700', 'bg-violet-100'],
                'BINATANG' => ['bg-emerald-50','text-emerald-700','bg-emerald-100'],
            ];
            @endphp
            @foreach($daftarHafalan as $kategori => $kata)
            @php [$bg, $text, $badgeBg] = $warna[$kategori]; @endphp
            <div class="flex items-start gap-4 px-6 py-4 {{ $bg }}">
                <div class="shrink-0 w-24">
                    <span class="inline-block rounded-md px-2.5 py-1 text-[11px] font-bold {{ $badgeBg }} {{ $text }}">{{ $kategori }}</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($kata as $k)
                    <span class="rounded-md border border-current/20 px-2.5 py-1 text-[13px] font-semibold {{ $text }}">{{ $k }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Form submit (dipakai JS setelah timer habis) --}}
    <form id="form-lanjut" method="POST" action="{{ route('peserta.tes.hafalan-me-selesai', $sesiId) }}" class="hidden">
        @csrf
    </form>

    {{-- Tombol manual (untuk darurat) --}}
    <div class="mt-4 text-center">
        <button id="btn-lanjut" onclick="document.getElementById('form-lanjut').submit()"
                class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-6 py-2.5 text-sm font-semibold text-white opacity-30 cursor-not-allowed transition-all"
                disabled>
            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            Lanjut ke Soal
        </button>
        <p class="mt-2 text-[11px] text-[#71787c]">Tombol aktif setelah waktu hafalan habis</p>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const DURASI = 180; // 3 menit
    let sisa = DURASI;
    const display = document.getElementById('timer-display');
    const btn = document.getElementById('btn-lanjut');

    function format(s) {
        const m = Math.floor(s / 60);
        const detik = s % 60;
        return m + ':' + String(detik).padStart(2, '0');
    }

    const interval = setInterval(() => {
        sisa--;
        display.textContent = format(sisa);

        if (sisa <= 30) {
            display.classList.add('text-rose-600');
            display.classList.remove('text-[#2C5F6F]');
        }

        if (sisa <= 0) {
            clearInterval(interval);
            display.textContent = '0:00';
            document.getElementById('form-lanjut').submit();
        }
    }, 1000);

    // Aktifkan tombol manual setelah timer habis (atau jika JS sudah jalan)
    setTimeout(() => {
        btn.disabled = false;
        btn.classList.remove('opacity-30', 'cursor-not-allowed');
        btn.classList.add('hover:bg-[#1E414C]', 'active:scale-95');
    }, DURASI * 1000);
})();
</script>
@endpush

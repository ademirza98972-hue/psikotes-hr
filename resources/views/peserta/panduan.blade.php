@extends('layouts.peserta', ['judulHalaman' => 'Panduan Tes'])

@section('content')
<div class="space-y-6 max-w-3xl">

    {{-- Header --}}
    <div class="rounded-xl p-6 text-white shadow-sm" style="background: linear-gradient(to right, #2C5F6F, #3d7a8a)">
        <h2 class="text-xl font-semibold">Panduan Pengerjaan Tes</h2>
        <p class="text-sm mt-1 text-white/70">Baca seluruh panduan sebelum memulai sesi tes Anda.</p>
    </div>

    {{-- Aturan Umum --}}
    <div class="rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm">
        <h3 class="text-base font-semibold text-slate-900 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px] text-[#2C5F6F]">gavel</span>
            Aturan Umum
        </h3>
        <ul class="space-y-3">
            @foreach ([
                ['lock', 'Kerjakan tes secara mandiri dan jujur. Dilarang meminta bantuan orang lain.'],
                ['timer', 'Setiap tes memiliki batas waktu. Jawaban tidak akan tersimpan setelah waktu habis.'],
                ['wifi', 'Pastikan koneksi internet stabil sebelum memulai tes.'],
                ['do_not_disturb_on', 'Hindari menutup tab, merefresh halaman, atau menekan tombol Back saat tes berlangsung.'],
                ['smartphone', 'Gunakan perangkat desktop atau laptop. Tes tidak dioptimalkan untuk layar kecil.'],
                ['calendar_clock', 'Kerjakan sebelum batas tanggal berakhir. Sesi yang kedaluwarsa tidak dapat dibuka kembali.'],
            ] as [$icon, $teks])
            <li class="flex items-start gap-3 text-sm text-slate-700">
                <span class="material-symbols-outlined text-[18px] text-[#2C5F6F] shrink-0 mt-0.5">{{ $icon }}</span>
                {{ $teks }}
            </li>
            @endforeach
        </ul>
    </div>

    {{-- Daftar Alat Tes --}}
    @if ($alatTes->isNotEmpty())
    <div>
        <h3 class="text-base font-semibold text-slate-900 mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px] text-[#2C5F6F]">quiz</span>
            Jenis Tes dalam Sistem
        </h3>

        <div class="space-y-3">
            @foreach ($alatTes as $alat)
            <div class="rounded-xl border border-[#e0e3e5] bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4 flex-wrap">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center rounded-md bg-[#2C5F6F]/10 px-2.5 py-0.5 text-xs font-bold text-[#2C5F6F]">{{ $alat->kode }}</span>
                            <h4 class="text-sm font-semibold text-slate-900">{{ $alat->nama }}</h4>
                        </div>
                        @if ($alat->deskripsi)
                            <p class="text-xs text-slate-500 mt-1">{{ $alat->deskripsi }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 shrink-0 text-xs text-slate-500">
                        @if ($alat->durasi_total_menit)
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-slate-400">timer</span>
                                {{ $alat->durasi_total_menit }} menit
                            </span>
                        @endif
                        @if ($alat->jumlah_soal)
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-slate-400">format_list_numbered</span>
                                {{ $alat->jumlah_soal }} soal
                            </span>
                        @endif
                        @if ($alat->format_dasar)
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-slate-400">category</span>
                                {{ $alat->format_dasar }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tips --}}
    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
        <h3 class="text-sm font-semibold text-amber-900 mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px] text-amber-600">lightbulb</span>
            Tips Sebelum Mulai
        </h3>
        <ul class="space-y-2">
            @foreach ([
                'Pastikan Anda berada di ruangan tenang dan tidak akan terganggu.',
                'Baca instruksi di setiap tes dengan seksama sebelum menjawab.',
                'Jawab dengan spontan dan jujur — tidak ada jawaban benar atau salah pada tes kepribadian.',
                'Jika mengalami kendala teknis, segera hubungi tim HR atau IT Support.',
            ] as $tips)
            <li class="flex items-start gap-2 text-xs text-amber-800">
                <span class="material-symbols-outlined text-[14px] text-amber-500 shrink-0 mt-0.5">check_circle</span>
                {{ $tips }}
            </li>
            @endforeach
        </ul>
    </div>

    {{-- Kontak --}}
    <div class="rounded-xl border border-[#e0e3e5] bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px] text-[#2C5F6F]">support_agent</span>
            Butuh Bantuan?
        </h3>
        <p class="text-xs text-slate-500">Hubungi tim HR atau IT Support jika mengalami masalah teknis atau pertanyaan seputar sesi tes Anda.</p>
    </div>

</div>
@endsection

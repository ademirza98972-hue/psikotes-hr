@extends('layouts.peserta', ['judulHalaman' => 'Pengerjaan Soal'])

@section('content')
    {{-- Header Progress Bar --}}
    <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm mb-6">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">
                    {{ $nama_alat_tes }} ({{ $kode_alat_tes }})
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    {{ $alat_tes_index + 1 }} dari {{ $total_alat_tes }} alat tes
                </p>
            </div>
            <div class="text-sm text-slate-600">
                Soal {{ $soal_nomor }} dari {{ $soal_total }}
            </div>
        </div>

        {{-- Progress Bar Visual di dalam alat tes ini --}}
        <div class="w-full bg-slate-200 rounded-full h-2">
            <div class="bg-[#2C5F6F] h-full rounded-full transition-all duration-300" style="width: {{ ($soal_nomor - 1) / max($soal_total, 1) * 100 }}%"></div>
        </div>

        {{-- Progress Global --}}
        <div class="mt-3 flex items-center gap-4 text-xs text-slate-500">
            <span>{{ $soal_posisi_global + 1 }} dari {{ $soal_total_global }} soal (keseluruhan)</span>
        </div>
    </div>

    {{-- Question Card --}}
    <form action="{{ route('peserta.tes.jawab', $sesiId) }}" method="POST">
        @csrf
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="p-8">
                {{-- Likert Format --}}
                @if($soal_data['type'] == 'likert')
                <p class="text-base text-slate-800 mb-6 font-medium leading-relaxed">
                    {{ $soal_data['pernyataan'] }}
                </p>

                {{-- Pilihan Jawaban Horizontal --}}
                <div class="flex flex-wrap gap-3 justify-center">
                    @foreach($soal_data['scale'] as $scaleOption)
                        <label class="flex items-center gap-2">
                            <input type="radio"
                                   name="choice"
                                   value="{{ $scaleOption['value'] }}"
                                   @if($saved_answer == $scaleOption['value']) checked @endif
                                   class="h-4 w-4 text-[#2C5F6F] border-slate-300 focus:ring-[#2C5F6F]" />
                            <span class="text-sm text-slate-700">{{ $scaleOption['label'] }}</span>
                        </label>
                    @endforeach
                </div>

            {{-- Forced Choice Format (EPPS) --}}
            @elseif($soal_data['type'] == 'forced_choice')
                <p class="text-base text-slate-800 mb-6 font-medium">
                    Pilih salah satu pernyataan berikut:
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Kartu A --}}
                    <label class="flex flex-col gap-3 border border-slate-200 rounded-lg p-5 hover:bg-slate-50 transition cursor-pointer">
                        <input type="radio" name="choice" value="A" @if($saved_answer == 'A') checked @endif class="h-4 w-4 text-[#2C5F6F] focus:ring-[#2C5F6F]" />
                        <p class="text-sm text-slate-800 leading-relaxed ml-8">{{ $soal_data['statement_a'] }}</p>
                    </label>

                    {{-- Kartu B --}}
                    <label class="flex flex-col gap-3 border border-slate-200 rounded-lg p-5 hover:bg-slate-50 transition cursor-pointer">
                        <input type="radio" name="choice" value="B" @if($saved_answer == 'B') checked @endif class="h-4 w-4 text-[#2C5F6F] focus:ring-[#2C5F6F]" />
                        <p class="text-sm text-slate-800 leading-relaxed ml-8">{{ $soal_data['statement_b'] }}</p>
                    </label>
                </div>

            {{-- Multiple Choice format --}}
            @elseif($soal_data['type'] == 'multiple_choice')
                <p class="text-base text-slate-800 mb-6 font-medium leading-relaxed">
                    {{ $soal_data['pernyataan'] }}
                </p>

                <div class="space-y-3">
                    @foreach($soal_data['choices'] as $choice)
                        <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-lg hover:bg-slate-50 transition cursor-pointer relative">
                            <input type="radio"
                                   name="choice"
                                   value="{{ $choice['key'] }}"
                                   @if($saved_answer == $choice['key']) checked @endif
                                   class="h-4 w-4 text-[#2C5F6F] focus:ring-[#2C5F6F]" />
                            <span class="text-sm text-slate-700 ml-2">{{ $choice['text'] }}</span>
                        </label>
                    @endforeach
                </div>

            {{-- Lainnya --}}
            @else
                <p class="text-slate-600">Format soal belum didukung.</p>
            @endif
        </div>

        {{-- Footer Navigation --}}
        <div class="px-6 py-4 bg-slate-50 rounded-b-lg flex justify-between items-center">
            {{-- Tombol Sebelumnya --}}
            @if(!$is_first_soal)
                <a href="{{ route('peserta.tes.kerjakan', $sesiId) . '?prev=1' }}"
                   class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-white hover:border-slate-400 transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Sebelumnya
                </a>
            @else
                <div class="invisible"></div>
            @endif

            {{-- Tombol Selanjutya / Selesai --}}
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2 rounded-md text-sm font-medium text-white bg-[#2C5F6F] hover:bg-[#1e4450] transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>
                    @if($is_last_soal)
                        Selesai
                    @else
                        Selanjutnya
                    @endif
                </span>
            </button>
        </div>
    </div>
</form>

    {{-- JavaScript Alpine untuk interaktivitas kartu --}}
    @vite(['resources/js/app.js'])
@endsection
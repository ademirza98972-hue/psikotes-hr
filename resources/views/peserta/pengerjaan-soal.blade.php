@extends('layouts.peserta', ['judulHalaman' => 'Pengerjaan Soal'])

@section('content')
<script>
    // 移除 main 的 max-w-5xl 限制，使页面撑满全宽
    const m = document.querySelector('main');
    if (m) { m.style.maxWidth = '100%'; m.style.paddingLeft = '24px'; m.style.paddingRight = '24px'; }
</script>
<div class="grid items-start gap-4" style="grid-template-columns: 220px 1fr;">

    {{-- SIDEBAR KIRI: Grid Nomor Soal --}}
    @php
        $grupAlat = collect($daftar_nomor_soal)->groupBy('kode_alat_tes');
    @endphp
    <div class="sticky top-6">
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-3">Navigasi Soal</p>
            <div class="space-y-3 overflow-y-auto pr-1" style="max-height: calc(100vh - 180px);">
                @foreach($grupAlat as $kode => $soalGrup)
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1.5 tracking-wider">{{ $kode }}</p>
                        <div class="grid gap-1" style="grid-template-columns: repeat(5, 1fr);">
                            @foreach($soalGrup as $item)
                                @php
                                    $bg = $item['is_current']
                                        ? 'bg-[#2C5F6F] text-white border-[#2C5F6F]'
                                        : ($item['sudah_dijawab']
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-300'
                                            : 'bg-white text-slate-500 border-slate-200 hover:border-[#2C5F6F] hover:text-[#2C5F6F]');
                                @endphp
                                <a href="{{ route('peserta.tes.kerjakan', $sesiId) }}?step={{ $item['step'] }}"
                                   class="flex items-center justify-center rounded border text-[10px] font-semibold transition aspect-square {{ $bg }}">
                                    {{ $loop->iteration }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            {{-- Legenda --}}
            <div class="mt-4 pt-3 border-t border-slate-100 space-y-1.5">
                <div class="flex items-center gap-2 text-[10px] text-slate-500">
                    <span class="w-3 h-3 rounded bg-[#2C5F6F] shrink-0"></span> Sedang dikerjakan
                </div>
                <div class="flex items-center gap-2 text-[10px] text-slate-500">
                    <span class="w-3 h-3 rounded bg-emerald-50 border border-emerald-300 shrink-0"></span> Sudah dijawab
                </div>
                <div class="flex items-center gap-2 text-[10px] text-slate-500">
                    <span class="w-3 h-3 rounded bg-white border border-slate-200 shrink-0"></span> Belum dijawab
                </div>
            </div>
        </div>
    </div>

    {{-- KONTEN KANAN: Progress + Soal --}}
    <div class="flex-1 min-w-0">

        {{-- Header Progress Bar --}}
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm mb-4">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">
                        {{ $nama_alat_tes }} ({{ $kode_alat_tes }})
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $alat_tes_index + 1 }} dari {{ $total_alat_tes }} alat tes
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    @if($sisa_waktu_sesi_detik !== null)
                    <div x-data="{
                        sisa: {{ $sisa_waktu_sesi_detik }},
                        get jam() { return String(Math.floor(this.sisa / 3600)).padStart(2, '0') },
                        get menit() { return String(Math.floor((this.sisa % 3600) / 60)).padStart(2, '0') },
                        get detik() { return String(this.sisa % 60).padStart(2, '0') },
                        init() {
                            const interval = setInterval(() => {
                                if (this.sisa > 0) {
                                    this.sisa--;
                                } else {
                                    clearInterval(interval);
                                    fetch('{{ route('peserta.tes.timeout-sesi', $sesiId) }}', {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    }).then(() => {
                                        window.location.href = '{{ route('peserta.tes.kerjakan', $sesiId) }}';
                                    });
                                }
                            }, 1000);
                        }
                    }" class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base" :class="sisa <= 300 ? 'text-red-500' : 'text-slate-400'">timer</span>
                        <span class="font-mono font-semibold text-sm tabular-nums" :class="sisa <= 300 ? 'text-red-600' : 'text-slate-700'">
                            <span x-text="jam"></span>:<span x-text="menit"></span>:<span x-text="detik"></span>
                        </span>
                    </div>
                    @endif
                    <div class="text-sm text-slate-600">
                        Soal {{ $soal_nomor }} dari {{ $soal_total }}
                    </div>
                </div>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-1.5">
                <div class="bg-[#2C5F6F] h-full rounded-full transition-all duration-300"
                     style="width: {{ ($soal_nomor - 1) / max($soal_total, 1) * 100 }}%"></div>
            </div>
            <div class="mt-2 text-xs text-slate-500">
                {{ $soal_posisi_global + 1 }} dari {{ $soal_total_global }} soal (keseluruhan)
            </div>
        </div>

        {{-- Timer Subtes IST --}}
        @if($is_ist && $sisa_waktu_detik !== null)
        <div x-data="{
            sisa: {{ $sisa_waktu_detik }},
            durasi: {{ $durasi_subtes }},
            submitted: false,
            get menit() { return String(Math.floor(this.sisa / 60)).padStart(2, '0') },
            get detik() { return String(this.sisa % 60).padStart(2, '0') },
            get persen() { return this.durasi > 0 ? (this.sisa / this.durasi) * 100 : 0 },
            get warnaTimer() {
                if (this.sisa <= 30) return 'text-red-600';
                if (this.sisa <= 60) return 'text-orange-500';
                return 'text-[#2C5F6F]';
            },
            init() {
                const interval = setInterval(() => {
                    if (this.sisa > 0) {
                        this.sisa--;
                    } else {
                        clearInterval(interval);
                        if (!this.submitted) {
                            this.submitted = true;
                            document.getElementById('form-jawaban').submit();
                        }
                    }
                }, 1000);
            }
        }" class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm mb-4 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu Subtes {{ $kode_subtes_timer }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Sisa waktu mengerjakan subtes ini</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-32 bg-slate-200 rounded-full h-2">
                    <div class="h-full rounded-full transition-all duration-1000"
                         :class="sisa <= 30 ? 'bg-red-500' : sisa <= 60 ? 'bg-orange-400' : 'bg-[#2C5F6F]'"
                         :style="'width:' + persen + '%'"></div>
                </div>
                <div class="text-2xl font-bold font-mono tabular-nums" :class="warnaTimer">
                    <span x-text="menit"></span>:<span x-text="detik"></span>
                </div>
            </div>
        </div>
        @endif

        {{-- Question Card --}}
        <form id="form-jawaban" action="{{ route('peserta.tes.jawab', $sesiId) }}" method="POST">
            @csrf
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="p-8">
                    {{-- Forced Choice Format --}}
                    @if($soal_data['tipe_format'] == 'forced_choice')
                        <p class="text-sm text-slate-800 mb-5 font-medium">
                            Pilih salah satu pernyataan berikut:
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($soal_data['opsi'] as $opsi)
                                <label class="flex items-start gap-3 border border-slate-200 rounded-lg p-4 hover:border-[#2C5F6F]/50 hover:bg-[#2C5F6F]/5 transition cursor-pointer">
                                    <input type="radio" name="opsi_id" value="{{ $opsi['id'] }}"
                                           @checked($saved_answer == $opsi['id'])
                                           class="mt-0.5 h-4 w-4 text-[#2C5F6F] focus:ring-[#2C5F6F] shrink-0" />
                                    <p class="text-sm text-slate-700 leading-relaxed">{{ $opsi['teks'] }}</p>
                                </label>
                            @endforeach
                        </div>

                    {{-- Pilihan Ganda --}}
                    @elseif(in_array($soal_data['tipe_format'], ['pilihan_ganda', 'multiple_choice', 'true_false']))
                        @php $teks_tampil = $soal_data['teks_soal'] ?? $soal_data['pernyataan'] ?? ''; @endphp
                        @if(!empty($teks_tampil))
                            <p class="text-base text-slate-800 mb-5 font-medium leading-relaxed">{{ $teks_tampil }}</p>
                        @endif
                        <div class="space-y-3">
                            @foreach($soal_data['opsi'] as $opsi)
                                <label class="flex items-center gap-3 px-4 py-3 border border-slate-200 rounded-lg hover:border-[#2C5F6F]/50 hover:bg-[#2C5F6F]/5 transition cursor-pointer">
                                    <input type="radio" name="opsi_id" value="{{ $opsi['id'] }}"
                                           @checked($saved_answer == $opsi['id'])
                                           class="h-4 w-4 text-[#2C5F6F] focus:ring-[#2C5F6F] shrink-0" />
                                    <span class="text-sm text-slate-700">{{ $opsi['teks'] }}</span>
                                </label>
                            @endforeach
                        </div>

                    {{-- Isian Teks (GE) --}}
                    @elseif($soal_data['tipe_format'] === 'isian_teks')
                        <p class="text-base text-slate-800 mb-5 font-medium leading-relaxed">{{ $soal_data['teks_soal'] }}</p>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Jawaban Anda:</label>
                            <input type="text" name="jawaban_teks" value="{{ $saved_answer_teks ?? '' }}"
                                   autocomplete="off"
                                   class="block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]"
                                   placeholder="Ketik satu kata yang mencakup kedua kata di atas">
                        </div>

                    {{-- Isian Angka (RA, ZR) --}}
                    @elseif($soal_data['tipe_format'] === 'isian_angka')
                        <p class="text-base text-slate-800 mb-5 font-medium leading-relaxed font-mono text-lg tracking-widest">{{ $soal_data['teks_soal'] }}</p>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Jawaban Anda:</label>
                            <input type="number" name="jawaban_teks" value="{{ $saved_answer_teks ?? '' }}"
                                   autocomplete="off"
                                   class="block w-48 rounded-lg border border-slate-300 px-4 py-3 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F] font-mono text-lg"
                                   placeholder="0">
                        </div>

                    {{-- Pilihan Gambar (FA, WU) --}}
                    @elseif($soal_data['tipe_format'] === 'pilihan_gambar')
                        @if(!empty($soal_data['gambar_soal']))
                            <div class="flex justify-center mb-5">
                                <img src="{{ asset('storage/' . $soal_data['gambar_soal']) }}"
                                     class="max-h-48 rounded-lg border border-slate-200 object-contain shadow-sm">
                            </div>
                        @else
                            <div class="mb-5 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-400">
                                Gambar soal belum tersedia
                            </div>
                        @endif
                        <p class="text-sm font-medium text-slate-700 mb-3">Pilih salah satu:</p>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($soal_data['opsi'] as $opsi)
                                <label class="flex flex-col items-center gap-2 border-2 rounded-lg p-2 cursor-pointer transition
                                              {{ $saved_answer == $opsi['id'] ? 'border-[#2C5F6F] bg-[#2C5F6F]/5' : 'border-slate-200 hover:border-[#2C5F6F]/50' }}">
                                    <input type="radio" name="opsi_id" value="{{ $opsi['id'] }}"
                                           @checked($saved_answer == $opsi['id'])
                                           class="sr-only">
                                    @if(!empty($opsi['gambar']))
                                        <img src="{{ asset('storage/' . $opsi['gambar']) }}"
                                             class="h-16 w-full object-contain rounded">
                                    @else
                                        <div class="h-16 w-full flex items-center justify-center text-slate-300 text-xs">—</div>
                                    @endif
                                    <span class="text-xs font-bold text-slate-500">{{ strtoupper(chr(96 + $opsi['urutan'])) }}</span>
                                </label>
                            @endforeach
                        </div>

                    {{-- Likert --}}
                    @elseif($soal_data['tipe_format'] == 'likert')
                        <p class="text-base text-slate-800 mb-5 font-medium leading-relaxed">{{ $soal_data['pernyataan'] ?? '' }}</p>
                        <div class="flex flex-wrap gap-3 justify-center">
                            @foreach($soal_data['opsi'] as $opsi)
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="opsi_id" value="{{ $opsi['id'] }}"
                                           @checked($saved_answer == $opsi['id'])
                                           class="h-4 w-4 text-[#2C5F6F] border-slate-300 focus:ring-[#2C5F6F]" />
                                    <span class="text-sm text-slate-700">{{ $opsi['teks'] }}</span>
                                </label>
                            @endforeach
                        </div>

                    @else
                        <p class="text-slate-600 text-sm">Format soal belum didukung.</p>
                    @endif
                </div>

                {{-- Footer Navigation --}}
                <div class="px-6 py-4 bg-slate-50 rounded-b-xl flex justify-between items-center border-t border-slate-100">
                    @if(!$is_first_soal)
                        <a href="{{ route('peserta.tes.kerjakan', $sesiId) }}?prev=1"
                           class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-md text-sm font-medium text-slate-700 hover:bg-white transition">
                            &larr; Sebelumnya
                        </a>
                    @else
                        <div></div>
                    @endif
                    @if($is_last_soal)
                        <div x-data="{ showModal: false, unanswered: 0,
                            checkUnanswered() {
                                const radios = document.querySelectorAll('input[type=radio]:checked');
                                const texts  = document.querySelectorAll('input[name=jawaban_teks]');
                                let answered = radios.length;
                                texts.forEach(t => { if (t.value.trim()) answered++; });
                                this.unanswered = {{ $soal_total }} - answered;
                            }
                        }">
                            <button type="button"
                                    @click="showModal = true; checkUnanswered()"
                                    class="inline-flex items-center gap-2 px-6 py-2 rounded-md text-sm font-medium text-white bg-[#2C5F6F] hover:bg-[#1e4450] transition">
                                Selesai &rarr;
                            </button>

                            {{-- Modal Konfirmasi Selesai --}}
                            <div x-show="showModal"
                                 x-transition:enter="ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                                 @click.self="showModal = false"
                                 style="display: none;">
                                <div x-transition:enter="ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                     class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6">
                                    <h3 class="text-base font-semibold text-slate-900 mb-2">Selesaikan Tes?</h3>
                                    <p class="text-sm text-slate-600 mb-1">
                                        Anda akan menyelesaikan <strong>{{ $nama_alat_tes }}</strong>. Jawaban yang sudah dikirim tidak dapat diubah.
                                    </p>
                                    <template x-if="unanswered > 0">
                                        <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mt-2">
                                            Masih ada <strong x-text="unanswered"></strong> soal yang belum dijawab.
                                        </p>
                                    </template>
                                    <div class="flex gap-3 mt-6 justify-end">
                                        <button @click="showModal = false"
                                                class="px-4 py-2 rounded-lg text-sm font-medium border border-[#2C5F6F] text-[#2C5F6F] hover:bg-slate-50 transition">
                                            Batal
                                        </button>
                                        <button @click="document.getElementById('form-jawaban').submit();"
                                                class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-[#2C5F6F] hover:bg-[#1e4450] transition">
                                            Ya, Selesaikan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2 rounded-md text-sm font-medium text-white bg-[#2C5F6F] hover:bg-[#1e4450] transition">
                            Selanjutnya &rarr;
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

@vite(['resources/js/app.js'])
<script>
    // Proteksi anti-copy-paste
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('copy', e => e.preventDefault());
    document.addEventListener('cut', e => e.preventDefault());
    document.addEventListener('paste', e => e.preventDefault());
    document.addEventListener('selectstart', e => e.preventDefault());

    document.addEventListener('keydown', e => {
        const isCtrl = e.ctrlKey || e.metaKey;
        if (isCtrl && ['c','x','v','a','u'].includes(e.key.toLowerCase())) {
            e.preventDefault();
        }
    });

    const selector = `
        .text-base.text-slate-800.mb-5,
        .text-sm.text-slate-800.mb-5,
        .text-sm.text-slate-700,
        .text-sm.font-medium.text-slate-700,
        .text-base.font-medium.text-slate-800,
        .font-mono.text-lg.tracking-widest,
        .text-sm.text-slate-800.leading-relaxed.ml-8
    `;
    document.querySelectorAll(selector).forEach(el => {
        el.style.userSelect = 'none';
        el.style.webkitUserSelect = 'none';
    });
</script>
@endsection

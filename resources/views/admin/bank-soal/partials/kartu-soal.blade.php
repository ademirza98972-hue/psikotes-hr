@php $id = 'kartu-' . $nomor; @endphp
<div id="{{ $id }}" class="bg-white border border-[#e0e3e5] rounded-xl p-5 hover:shadow-md transition-shadow relative group">

    <div class="flex justify-between items-start mb-4">
        <div class="flex items-center gap-3">
            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $warnaKode[$kodeAlat] ?? 'bg-slate-100 text-slate-700' }}">
                {{ $kodeAlat ?? '?' }}
            </span>
            <span class="text-[12px] text-[#40484b]">No. {{ str_pad($nomor, 3, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="flex gap-1">
            <a href="{{ route('admin.bank-soal.edit', $soal['id']) }}"
               class="p-1.5 rounded-lg text-[#40484b] hover:bg-[#f2f4f6] hover:text-[#2C5F6F] transition-colors"
               title="Edit soal">
                <span class="material-symbols-outlined text-[18px]">edit</span>
            </a>
            <button type="button" onclick="confirmHapus({{ $soal['id'] }}, '{{ route('admin.bank-soal.hapus', $soal['id']) }}')"
                    class="p-1.5 rounded-lg text-[#40484b] hover:bg-rose-50 hover:text-rose-600 transition-colors"
                    title="Hapus soal">
                <span class="material-symbols-outlined text-[18px]">delete</span>
            </button>
            <form id="hapus-soal-{{ $soal['id'] }}" method="POST" action="{{ route('admin.bank-soal.hapus', $soal['id']) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <script>
    function confirmHapus(soalId, url) {
        if (confirm('Yakin ingin menghapus soal ini?')) {
            document.getElementById('hapus-soal-' + soalId).submit();
        }
    }
    </script>

    @if ($format === 'Pilihan Ganda')
        <p class="text-[16px] leading-relaxed text-[#191c1e]">{{ $soal['teks_soal'] }}</p>
        <ul class="mt-4 space-y-2">
            @foreach (['A','B','C','D'] as $i => $huruf)
                @php $opsi = $soal['opsi'][$i] ?? ''; @endphp
                <li class="flex items-center gap-3 rounded-xl border {{ $soal['kunci_jawaban'] === $huruf ? 'border-[#2C5F6F] bg-[#2C5F6F]/5' : 'border-[#e0e3e5] bg-white' }} px-4 py-3 text-sm transition-colors hover:border-[#2C5F6F]/50">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full border {{ $soal['kunci_jawaban'] === $huruf ? 'border-[#2C5F6F] bg-[#2C5F6F] text-white' : 'border-[#c0c8cb] text-[#40484b]' }} text-[11px] font-bold shrink-0">
                        {{ $huruf }}
                    </span>
                    <span class="{{ $soal['kunci_jawaban'] === $huruf ? 'font-semibold text-[#191c1e]' : 'text-[#40484b]' }}">{{ $opsi }}</span>
                    @if ($soal['kunci_jawaban'] === $huruf)
                        <span class="ml-auto inline-flex items-center gap-1 text-[11px] font-semibold text-[#2C5F6F]">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                            Kunci
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>

    @elseif ($format === 'Skala Likert')
        <p class="text-[16px] leading-relaxed text-[#191c1e]">"{{ $soal['pernyataan'] }}"</p>
        <div class="mt-3">
            <span class="inline-block rounded-lg bg-indigo-50 px-2.5 py-1 text-[11px] font-semibold text-indigo-700">
                Dimensi: {{ $soal['dimensi'] }}
            </span>
        </div>
        {{-- Preview skala Likert 5 poin --}}
        <div class="mt-4">
            <div class="flex justify-between px-2 mb-2">
                @php $labelSkala = ['Sangat Tidak Setuju', 'Tidak Setuju', 'Netral', 'Setuju', 'Sangat Setuju']; @endphp
                @foreach ($labelSkala as $label)
                    <span class="text-[10px] text-[#40484b] text-center whitespace-nowrap">{{ $label }}</span>
                @endforeach
            </div>
            <div class="relative flex items-center justify-between px-8">
                <div class="absolute left-10 right-10 h-[2px] bg-[#c0c8cb] -z-10"></div>
                @for($i = 0; $i < 5; $i++)
                    <div class="w-5 h-5 rounded-full border-2 border-[#c0c8cb] bg-white hover:border-[#2C5F6F] transition-colors cursor-pointer"></div>
                @endfor
            </div>
        </div>

    @elseif ($format === 'Forced Choice')
        <p class="text-[11px] uppercase tracking-widest text-[#40484b] mb-3">Pilih satu pernyataan yang paling menggambarkan diri Anda</p>
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 p-4 rounded-xl border-2 border-[#e0e3e5] hover:border-[#2C5F6F]/50 bg-[#f7f9fb] cursor-pointer transition-all flex items-start gap-3">
                <div class="mt-0.5 w-5 h-5 rounded-full border-2 border-[#c0c8cb] flex-shrink-0"></div>
                <div>
                    <span class="font-bold text-[#00303c] block mb-1 text-sm">Pernyataan A</span>
                    <p class="text-[14px] text-[#40484b]">{{ $soal['pernyataan_a'] }}</p>
                    <span class="mt-2 inline-block rounded-lg bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700">
                        {{ $soal['dimensi_a'] }}
                    </span>
                </div>
            </div>
            <div class="flex items-center justify-center text-[#c0c8cb] italic text-sm font-semibold shrink-0">VS</div>
            <div class="flex-1 p-4 rounded-xl border-2 border-[#e0e3e5] hover:border-[#2C5F6F]/50 bg-[#f7f9fb] cursor-pointer transition-all flex items-start gap-3">
                <div class="mt-0.5 w-5 h-5 rounded-full border-2 border-[#c0c8cb] flex-shrink-0"></div>
                <div>
                    <span class="font-bold text-[#00303c] block mb-1 text-sm">Pernyataan B</span>
                    <p class="text-[14px] text-[#40484b]">{{ $soal['pernyataan_b'] }}</p>
                    <span class="mt-2 inline-block rounded-lg bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700">
                        {{ $soal['dimensi_b'] }}
                    </span>
                </div>
            </div>
        </div>
    @endif

</div>

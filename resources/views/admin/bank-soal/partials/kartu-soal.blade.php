@php $id = 'kartu-' . $nomor; @endphp
<div id="{{ $id }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

    <div class="mb-3 flex items-start justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-[#2C5F6F] text-xs font-semibold text-white">{{ $nomor }}</span>
            <span class="text-xs font-medium text-slate-500">Soal #{{ $nomor }}</span>
        </div>
        <span class="inline-block rounded-md {{ $warnaFormat[$format] ?? 'bg-slate-600' }} px-2 py-0.5 text-[11px] font-semibold text-white">
            {{ $format }}
        </span>
    </div>

    @if ($format === 'Pilihan Ganda')
        <p class="text-sm font-medium text-slate-900">{{ $soal['teks_soal'] }}</p>
        <ul class="mt-3 space-y-1.5">
            @foreach (['A','B','C','D'] as $i => $huruf)
                @php $opsi = $soal['opsi'][$i] ?? ''; @endphp
                <li class="flex items-start gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm
                    {{ $soal['kunci'] === $huruf ? 'border-emerald-300 bg-emerald-50' : 'bg-white' }}">
                    <span class="font-semibold {{ $soal['kunci'] === $huruf ? 'text-emerald-700' : 'text-slate-600' }}">{{ $huruf }}.</span>
                    <span class="{{ $soal['kunci'] === $huruf ? 'text-emerald-900 font-medium' : 'text-slate-700' }}">{{ $opsi }}</span>
                    @if ($soal['kunci'] === $huruf)
                        <span class="ml-auto text-[11px] font-semibold text-emerald-700">Kunci</span>
                    @endif
                </li>
            @endforeach
        </ul>

    @elseif ($format === 'Skala Likert')
        <p class="text-sm font-medium text-slate-900">{{ $soal['pernyataan'] }}</p>
        <div class="mt-3">
            <span class="inline-block rounded-md bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-700">
                Dimensi: {{ $soal['dimensi'] }}
            </span>
        </div>

    @elseif ($format === 'Forced Choice')
        <div class="grid gap-3 md:grid-cols-2">
            <div class="rounded-md border border-slate-200 bg-slate-50 p-3">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Pernyataan A</p>
                <p class="mt-1 text-sm font-medium text-slate-900">{{ $soal['pernyataan_a'] }}</p>
                <span class="mt-2 inline-block rounded-md bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">
                    {{ $soal['dimensi_a'] }}
                </span>
            </div>
            <div class="rounded-md border border-slate-200 bg-slate-50 p-3">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Pernyataan B</p>
                <p class="mt-1 text-sm font-medium text-slate-900">{{ $soal['pernyataan_b'] }}</p>
                <span class="mt-2 inline-block rounded-md bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">
                    {{ $soal['dimensi_b'] }}
                </span>
            </div>
        </div>
    @endif

    <div class="mt-4 flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-3">
        <button type="button" disabled title="Pratinjau belum diimplementasikan"
                class="cursor-not-allowed rounded-md bg-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-500 opacity-70">Pratinjau</button>
        <button type="button" disabled title="Duplikat belum diimplementasikan"
                class="cursor-not-allowed rounded-md bg-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-500 opacity-70">Duplikat</button>
        <button type="button" disabled title="Ubah belum diimplementasikan"
                class="cursor-not-allowed rounded-md bg-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-500 opacity-70">Ubah</button>
        <button type="button" disabled title="Hapus belum diimplementasikan"
                class="cursor-not-allowed rounded-md bg-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-500 opacity-70">Hapus</button>
    </div>
</div>
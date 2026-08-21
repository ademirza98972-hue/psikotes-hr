@extends('layouts.admin', ['judulHalaman' => "Kelola Norma — {$alatTes->nama}"])

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <nav class="text-[13px] text-[#40484b] mb-1">
                <a href="{{ route('admin.alat-tes.index') }}" class="hover:text-[#2C5F6F] transition-colors">Alat Tes</a>
                <span class="mx-1">/</span>
                <span class="text-[#00303c] font-semibold">Norma</span>
            </nav>
            <h2 class="text-[28px] leading-9 font-semibold text-[#00303c]">{{ $alatTes->nama }}</h2>
            <p class="mt-0.5 text-[14px] text-[#40484b]">Kelola data norma konversi dari CSV.</p>
        </div>
        <a href="{{ route('admin.alat-tes.index') }}"
           class="inline-flex items-center gap-2 border border-[#c0c8cb] hover:border-[#2C5F6F] text-[#40484b] hover:text-[#2C5F6F] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all active:scale-95 whitespace-nowrap">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali
        </a>
    </div>

    {{-- Section 1: Upload CSV --}}
    <div class="bg-white border border-[#e0e3e5] rounded-xl p-6">
        <h3 class="text-[16px] font-bold text-[#00303c] mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-[#2C5F6F]">upload_file</span>
            Upload Norma CSV
        </h3>

        <div class="bg-[#f2f4f6] border border-[#e0e3e5] rounded-lg p-4 mb-5">
            <p class="text-[13px] font-semibold text-[#00303c] mb-2">Format CSV yang diterima:</p>
            <code class="text-[12px] text-[#40484b] bg-white border border-[#e0e3e5] rounded px-2 py-1 block font-mono mb-2">
                dimensi_kode,kelompok_segmen,tahap,skor_mentah_min,skor_mentah_max,skor_hasil
            </code>
            <code class="text-[12px] text-[#40484b] bg-white border border-[#e0e3e5] rounded px-2 py-1 block font-mono mb-2">
                IQ,default,1,0,0,38
            </code>
            <p class="text-[12px] text-[#40484b] mt-2">Kode dimensi yang tersedia untuk alat tes ini:</p>
            <div class="flex flex-wrap gap-2 mt-2">
                @forelse($dimensi as $d)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#e8f0f2] text-[#2C5F6F] border border-[#4A8A9B]/30">
                        {{ $d->kode_dimensi }}
                    </span>
                @empty
                    <span class="text-[12px] text-[#40484b] italic">Belum ada dimensi</span>
                @endforelse
            </div>
        </div>

        @error('file_csv')
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-lg text-[13px] text-rose-700">
                {{ $message }}
            </div>
        @enderror

        <form action="{{ route('admin.alat-tes.uploadNorma', $alatTes->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[13px] font-semibold text-[#00303c] mb-1.5">File CSV</label>
                <input type="file" name="file_csv" accept=".csv" required
                       class="block w-full text-sm text-[#40484b] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#2C5F6F] file:text-white hover:file:bg-[#1E414C] transition-colors cursor-pointer">
            </div>

            <div class="flex items-start gap-2.5">
                <input type="checkbox" name="hapus_lama" id="hapus_lama" value="1"
                       class="mt-0.5 w-4 h-4 rounded border-[#c0c8cb] text-[#2C5F6F] focus:ring-[#2C5F6F]">
                <label for="hapus_lama" class="text-[13px] text-[#40484b] cursor-pointer select-none">
                    Hapus data norma lama sebelum import
                </label>
            </div>
            <p id="hapus_lama_warning" class="hidden text-[12px] text-rose-600 font-semibold bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">
                <span class="material-symbols-outlined text-[14px] align-middle">warning</span>
                Semua data norma yang ada untuk alat tes ini akan dihapus sebelum import. Tindakan ini tidak dapat dibatalkan.
            </p>

            <button type="submit"
                    class="inline-flex items-center gap-2 bg-[#2C5F6F] hover:bg-[#1E414C] text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-all active:scale-95">
                <span class="material-symbols-outlined text-[18px]">upload</span>
                Upload & Import
            </button>
        </form>
    </div>

    {{-- Section 2: Status Norma --}}
    <div class="bg-white border border-[#e0e3e5] rounded-xl p-6">
        <h3 class="text-[16px] font-bold text-[#00303c] mb-1 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-[#2C5F6F]">bar_chart</span>
            Status Norma
        </h3>
        <p class="text-[14px] text-[#40484b] mb-4">
            {{ $jumlahNorma }} baris norma tersimpan
        </p>

        @if($jumlahNorma === 0)
            <div class="text-center py-10 bg-[#f2f4f6] border border-dashed border-[#c0c8cb] rounded-lg">
                <span class="material-symbols-outlined text-[40px] text-[#c0c8cb] mb-2">analytics</span>
                <p class="text-[14px] text-[#40484b] font-semibold">Belum ada data norma</p>
                <p class="text-[12px] text-[#40484b]/70 mt-1">Upload file CSV pada form di atas untuk memulai</p>
            </div>
        @else
            {{-- Filter & per-page --}}
            <form method="GET" class="flex flex-wrap items-center gap-3 mb-3">
                <select name="dimensi" onchange="this.form.submit()"
                        class="rounded-lg border border-[#c0c8cb] bg-white px-3 py-2 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20">
                    <option value="">Semua Dimensi</option>
                    @foreach($dimensi as $d)
                        <option value="{{ $d->id }}" {{ $filterDimensi == $d->id ? 'selected' : '' }}>
                            {{ $d->kode_dimensi }} — {{ $d->nama_dimensi }}
                        </option>
                    @endforeach
                </select>
                <select name="per_page" onchange="this.form.submit()"
                        class="rounded-lg border border-[#c0c8cb] bg-white px-3 py-2 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20">
                    @foreach([25, 50, 100, 250] as $n)
                        <option value="{{ $n }}" {{ $perPage == $n ? 'selected' : '' }}>{{ $n }} baris</option>
                    @endforeach
                </select>
                <p class="ml-auto text-[12px] text-[#40484b]">
                    Menampilkan <strong>{{ $contohNorma->firstItem() }}–{{ $contohNorma->lastItem() }}</strong> dari <strong>{{ $contohNorma->total() }}</strong> baris
                </p>
            </form>

            <div class="overflow-x-auto rounded-lg border border-[#e0e3e5]">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-[#f2f4f6] border-b border-[#e0e3e5]">
                            <th class="px-4 py-2.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b]">Dimensi</th>
                            <th class="px-4 py-2.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b]">Segmen</th>
                            <th class="px-4 py-2.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b] text-center">Tahap</th>
                            <th class="px-4 py-2.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b] text-right">Skor Mentah Min</th>
                            <th class="px-4 py-2.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b] text-right">Skor Mentah Max</th>
                            <th class="px-4 py-2.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b] text-right">Skor Hasil</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e0e3e5]/60">
                        @foreach($contohNorma as $n)
                            <tr class="hover:bg-[#f2f4f6] transition-colors">
                                <td class="px-4 py-2.5">
                                    <span class="inline-block px-2 py-0.5 rounded bg-[#e8f0f2] text-[#2C5F6F] text-[11px] font-bold">
                                        {{ $n->dimensi->kode_dimensi ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-[#40484b]">{{ $n->kelompok_segmen }}</td>
                                <td class="px-4 py-2.5 text-center font-medium text-[#40484b]">{{ $n->tahap }}</td>
                                <td class="px-4 py-2.5 text-right font-mono text-[#40484b]">{{ number_format($n->skor_mentah_min, 2) }}</td>
                                <td class="px-4 py-2.5 text-right font-mono text-[#40484b]">{{ number_format($n->skor_mentah_max, 2) }}</td>
                                <td class="px-4 py-2.5 text-right font-mono font-semibold text-[#2C5F6F]">{{ number_format($n->skor_hasil, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($contohNorma->hasPages())
                <div class="mt-4">{{ $contohNorma->links() }}</div>
            @endif
        @endif
    </div>

</div>

@push('scripts')
<script>
    const checkbox = document.getElementById('hapus_lama');
    const warning = document.getElementById('hapus_lama_warning');
    if (checkbox) {
        checkbox.addEventListener('change', function() {
            warning.classList.toggle('hidden', !this.checked);
        });
    }
</script>
@endpush
@endsection

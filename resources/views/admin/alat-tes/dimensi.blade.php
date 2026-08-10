@extends('layouts.admin', ['judulHalaman' => "Kelola Dimensi — {$alatTes->nama}"])

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <nav class="text-[13px] text-[#40484b] mb-1">
                <a href="{{ route('admin.alat-tes.index') }}" class="hover:text-[#2C5F6F] transition-colors">Alat Tes</a>
                <span class="mx-1">/</span>
                <span class="text-[#00303c] font-semibold">Dimensi</span>
            </nav>
            <h2 class="text-[28px] leading-9 font-semibold text-[#00303c]">Kelola Dimensi — {{ $alatTes->nama }}</h2>
            <p class="mt-0.5 text-[14px] text-[#40484b]">Definisikan dimensi dan level skor untuk alat tes ini.</p>
        </div>
        <a href="{{ route('admin.alat-tes.index') }}"
           class="inline-flex items-center gap-2 border border-[#c0c8cb] hover:border-[#2C5F6F] text-[#40484b] hover:text-[#2C5F6F] px-4 py-2.5 rounded-xl text-sm font-semibold transition-all active:scale-95 whitespace-nowrap">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @if(session('sukses'))
        <div class="rounded-xl border border-emerald-300 bg-emerald-50
                    px-4 py-3 text-sm text-emerald-700 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('sukses') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-rose-300 bg-rose-50
                    px-4 py-3 text-sm text-rose-700">
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM TAMBAH DIMENSI --}}
    <div class="bg-white border border-[#e0e3e5] rounded-xl p-6">
        <h3 class="text-[16px] font-bold text-[#00303c] mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-[#2C5F6F]">add_circle</span>
            Tambah Dimensi
        </h3>
        <form action="{{ route('admin.alat-tes.simpanDimensi', $alatTes->id) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[13px] font-semibold text-[#00303c] mb-1.5">Kode Dimensi *</label>
                    <input type="text" name="kode_dimensi" required maxlength="50"
                           class="w-full rounded-lg border border-[#c0c8cb] px-3 py-2 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F] outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-[#00303c] mb-1.5">Nama Dimensi *</label>
                    <input type="text" name="nama_dimensi" required maxlength="150"
                           class="w-full rounded-lg border border-[#c0c8cb] px-3 py-2 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F] outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-[#00303c] mb-1.5">Tipe Kategori *</label>
                    <select name="tipe_kategori" required
                            class="w-full rounded-lg border border-[#c0c8cb] px-3 py-2 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F] outline-none transition-colors bg-white">
                        <option value="psikogram">Psikogram</option>
                        <option value="klinis">Klinis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-[#00303c] mb-1.5">Arah Skor *</label>
                    <select name="arah_skor" required
                            class="w-full rounded-lg border border-[#c0c8cb] px-3 py-2 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F] outline-none transition-colors bg-white">
                        <option value="tinggi_baik">Tinggi = Baik</option>
                        <option value="rendah_baik">Rendah = Baik</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-[#00303c] mb-1.5">Urutan</label>
                    <input type="number" name="urutan" min="0" value="0"
                           class="w-full rounded-lg border border-[#c0c8cb] px-3 py-2 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F] outline-none transition-colors">
                </div>
            </div>
            <div>
                <label class="block text-[13px] font-semibold text-[#00303c] mb-1.5">Deskripsi Aspek</label>
                <textarea name="deskripsi_aspek" rows="2"
                          class="w-full rounded-lg border border-[#c0c8cb] px-3 py-2 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F] outline-none transition-colors resize-none"></textarea>
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-[#2C5F6F] hover:bg-[#1E414C] text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-all active:scale-95">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Dimensi
            </button>
        </form>
    </div>

    {{-- DAFTAR DIMENSI --}}
    @forelse ($dimensi as $dim)
        <div class="bg-white border border-[#e0e3e5] rounded-xl p-6"
             x-data="{
                 tipe: '{{ $dim->tipe_kategori }}',
                 levels: @js($dim->levelDimensi->sortBy('urutan')->values()->map(fn($l) => [
                     'label' => $l->label,
                     'skor_min' => (float) $l->skor_min,
                     'skor_max' => (float) $l->skor_max,
                     'urutan' => (int) $l->urutan,
                 ])),
                 defaultLevels() {
                     if (this.tipe === 'psikogram') {
                         return [
                             { label: 'R', skor_min: 0, skor_max: 0, urutan: 0 },
                             { label: 'K', skor_min: 1, skor_max: 1, urutan: 1 },
                             { label: 'C', skor_min: 2, skor_max: 2, urutan: 2 },
                             { label: 'B', skor_min: 3, skor_max: 3, urutan: 3 },
                             { label: 'BS', skor_min: 4, skor_max: 4, urutan: 4 },
                         ];
                     }
                     return [
                         { label: 'Normal', skor_min: 0, skor_max: 0, urutan: 0 },
                         { label: 'Perlu Perhatian', skor_min: 1, skor_max: 1, urutan: 1 },
                         { label: 'Signifikan', skor_min: 2, skor_max: 2, urutan: 2 },
                     ];
                 },
                 applyDefaults() {
                     if (confirm('Kembalikan level ke default? Data level saat ini akan hilang.')) {
                         this.levels = this.defaultLevels();
                     }
                 },
                 addLevel() {
                     this.levels.push({ label: '', skor_min: 0, skor_max: 0, urutan: this.levels.length });
                 },
                 removeLevel(idx) {
                     if (this.levels.length <= 1) return;
                     this.levels.splice(idx, 1);
                     this.levels.forEach((l, i) => l.urutan = i);
                 }
             }">

            {{-- Header Dimensi --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5 pb-4 border-b border-[#e0e3e5]">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-block px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#e8f0f2] text-[#2C5F6F] border border-[#4A8A9B]/30">
                        {{ $dim->kode_dimensi }}
                    </span>
                    <h3 class="text-[15px] font-bold text-[#00303c]">{{ $dim->nama_dimensi }}</h3>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold
                        {{ $dim->tipe_kategori === 'psikogram'
                            ? 'bg-violet-100 text-violet-700 border border-violet-200'
                            : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                        {{ $dim->tipe_kategori === 'psikogram' ? 'Psikogram' : 'Klinis' }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold
                        {{ $dim->arah_skor === 'tinggi_baik'
                            ? 'bg-emerald-100 text-emerald-700 border border-emerald-200'
                            : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                        {{ $dim->arah_skor === 'tinggi_baik' ? 'Tinggi = Baik' : 'Rendah = Baik' }}
                    </span>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button"
                            @click="applyDefaults()"
                            class="p-1.5 rounded-lg text-[#40484b] hover:bg-[#e0e3e5] hover:text-[#2C5F6F] transition-colors"
                            title="Reset ke default">
                        <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    </button>
                    <form action="{{ route('admin.alat-tes.hapusDimensi', [$alatTes->id, $dim->id]) }}"
                          method="POST" onsubmit="return confirm('Hapus dimensi \'{{ addslashes($dim->nama_dimensi) }}\'? Semua level terkait akan ikut terhapus.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="p-1.5 rounded-lg text-[#40484b] hover:bg-rose-50 hover:text-rose-600 transition-colors"
                                title="Hapus dimensi">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </form>
                </div>
            </div>

            @if ($dim->deskripsi_aspek)
                <p class="text-[13px] text-[#40484b] mb-4">{{ $dim->deskripsi_aspek }}</p>
            @endif

            {{-- Sub-section: Level Dimensi --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-[14px] font-bold text-[#00303c] flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-[#2C5F6F]">tune</span>
                        Level Dimensi
                    </h4>
                    <button type="button"
                            @click="addLevel()"
                            class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-[#2C5F6F] hover:text-[#1E414C] transition-colors">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        Tambah Level
                    </button>
                </div>

                <form :action="`/admin/alat-tes/{{ $alatTes->id }}/dimensi/{{ $dim->id }}/level`"
                      method="POST"
                      @submit.prevent="
                          levels.forEach((l, i) => l.urutan = i);
                          let form = $el;
                          let container = document.getElementById('level-hidden-{{ $dim->id }}');
                          container.innerHTML = '';
                          levels.forEach((l, i) => {
                              ['label','skor_min','skor_max','urutan'].forEach(field => {
                                  let inp = document.createElement('input');
                                  inp.type = 'hidden';
                                  inp.name = 'levels[' + i + '][' + field + ']';
                                  inp.value = l[field];
                                  container.appendChild(inp);
                              });
                          });
                          form.submit();
                      ">
                    @csrf

                    <div id="level-hidden-{{ $dim->id }}"></div>

                    <div class="overflow-x-auto rounded-lg border border-[#e0e3e5]">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-[#f2f4f6] border-b border-[#e0e3e5]">
                                    <th class="px-4 py-2.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b] w-16">Urut</th>
                                    <th class="px-4 py-2.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b]">Label</th>
                                    <th class="px-4 py-2.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b] w-28">Skor Min</th>
                                    <th class="px-4 py-2.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b] w-28">Skor Max</th>
                                    <th class="px-4 py-2.5 text-[11px] uppercase tracking-widest font-bold text-[#40484b] w-12"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#e0e3e5]/60">
                                <template x-for="(level, idx) in levels" :key="idx">
                                    <tr class="hover:bg-[#f2f4f6] transition-colors">
                                        <td class="px-4 py-2.5 text-center text-[#40484b] font-medium" x-text="idx + 1"></td>
                                        <td class="px-4 py-2.5">
                                            <input type="text"
                                                   :name="`levels[${idx}][label]`"
                                                   x-model="level.label"
                                                   required
                                                   maxlength="50"
                                                   class="w-full rounded-lg border border-[#c0c8cb] px-3 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F] outline-none transition-colors">
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <input type="number" step="0.01"
                                                   :name="`levels[${idx}][skor_min]`"
                                                   x-model.number="level.skor_min"
                                                   required
                                                   class="w-full rounded-lg border border-[#c0c8cb] px-3 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F] outline-none transition-colors text-right font-mono">
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <input type="number" step="0.01"
                                                   :name="`levels[${idx}][skor_max]`"
                                                   x-model.number="level.skor_max"
                                                   required
                                                   class="w-full rounded-lg border border-[#c0c8cb] px-3 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F] outline-none transition-colors text-right font-mono">
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            <button type="button"
                                                    @click="removeLevel(idx)"
                                                    class="p-1 rounded text-[#40484b] hover:bg-rose-50 hover:text-rose-600 transition-colors"
                                                    :disabled="levels.length <= 1"
                                                    :class="{ 'opacity-30 cursor-not-allowed': levels.length <= 1 }">
                                                <span class="material-symbols-outlined text-[18px]">close</span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-end gap-2 mt-3">
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-[#2C5F6F] hover:bg-[#1E414C] text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            Simpan Level
                        </button>
                    </div>
                </form>
            </div>

        </div>
    @empty
        <div class="bg-white border border-[#e0e3e5] rounded-xl p-10 text-center">
            <span class="material-symbols-outlined text-[40px] text-[#c0c8cb] mb-3">psychology</span>
            <p class="text-[14px] text-[#40484b] font-semibold">Belum ada dimensi</p>
            <p class="text-[12px] text-[#40484b]/70 mt-1">Tambahkan dimensi pertama menggunakan form di atas.</p>
        </div>
    @endforelse

</div>

@endsection

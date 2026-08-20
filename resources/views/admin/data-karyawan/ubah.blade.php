@extends('layouts.admin', ['judulHalaman' => 'Ubah Data Karyawan'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-[24px] font-semibold text-[#00303c]">Ubah Data Karyawan</h2>
            <p class="mt-0.5 text-[13px] text-[#40484b]">Perbarui informasi data karyawan.</p>
        </div>
        <a href="{{ route('admin.data-karyawan.index') }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @php $readonlyNik = $data->status === 'sudah_terpakai'; @endphp

    <form method="POST" action="{{ route('admin.data-karyawan.perbarui', $data->id) }}">
        @csrf
        @method('PUT')

        {{-- SECTION: Data Karyawan --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Data Karyawan</p>
            </div>
            <div class="space-y-5 px-6 py-5">
                <div>
                    <label for="nik_karyawan" class="block text-[13px] font-medium text-[#191c1e] mb-1">
                        NIK
                        @if ($readonlyNik)
                            <span class="text-[11px] font-normal text-[#40484b]">(sudah terpakai, tidak dapat diubah)</span>
                        @endif
                    </label>
                    <input id="nik_karyawan" name="nik_karyawan" type="text" value="{{ old('nik_karyawan', $data->nik_karyawan) }}" required maxlength="30"
                           @if ($readonlyNik) disabled @endif
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm font-mono text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('nik_karyawan') border-rose-400 @enderror disabled:cursor-not-allowed disabled:bg-[#f2f4f6] disabled:text-[#40484b]">
                </div>
                <div>
                    <label for="nama_karyawan" class="block text-[13px] font-medium text-[#191c1e] mb-1">Nama Karyawan</label>
                    <input id="nama_karyawan" name="nama_karyawan" type="text" value="{{ old('nama_karyawan', $data->nama_karyawan) }}" required maxlength="255"
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('nama_karyawan') border-rose-400 @enderror">
                </div>
                <div>
                    <label for="jenis_kelamin" class="block text-[13px] font-medium text-[#191c1e] mb-1">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required
                            class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 appearance-none cursor-pointer">
                        <option value="">-- Pilih --</option>
                        <option value="L" @if(old('jenis_kelamin', $data->jenis_kelamin) == 'L') selected @endif>Laki-laki</option>
                        <option value="P" @if(old('jenis_kelamin', $data->jenis_kelamin) == 'P') selected @endif>Perempuan</option>
                    </select>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="departemen" class="block text-[13px] font-medium text-[#191c1e] mb-1">Departemen</label>
                        <select id="departemen" name="departemen_id" required
                                class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 appearance-none cursor-pointer">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemen as $item)
                                <option value="{{ $item->id }}" @if(old('departemen_id', $data->departemen_id) == $item->id) selected @endif>{{ $item->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="posisi" class="block text-[13px] font-medium text-[#191c1e] mb-1">
                            Jabatan <span class="text-[#40484b] font-normal">(opsional)</span>
                        </label>
                        <select id="posisi" name="posisi_id"
                                class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 appearance-none cursor-pointer">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($posisi as $item)
                                <option value="{{ $item->id }}" @if(old('posisi_id', $data->posisi_id) == $item->id) selected @endif>{{ $item->nama_posisi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if ($readonlyNik)
            <input type="hidden" name="nik_karyawan" value="{{ $data->nik_karyawan }}">
        @endif

        {{-- Legacy fields for backward compatibility --}}
        <input type="hidden" name="departemen" id="hidden_departemen" value="{{ old('departemen', $data->departemen) }}">
        <input type="hidden" name="jabatan" id="hidden_jabatan" value="{{ old('jabatan', $data->jabatan) }}">

        {{-- ACTION BAR --}}
        <div class="sticky bottom-0 mt-4 flex items-center justify-end gap-3 rounded-xl border border-[#e0e3e5] bg-white/95 px-6 py-4 shadow-[0_-2px_12px_rgba(0,0,0,0.06)] backdrop-blur">
            <a href="{{ route('admin.data-karyawan.index') }}"
               class="rounded-xl border border-[#c0c8cb] px-5 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</a>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Perbarui
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const departemenSelect = document.getElementById('departemen');
    const posisiSelect = document.getElementById('posisi');
    const hiddenDepartemen = document.getElementById('hidden_departemen');
    const hiddenJabatan = document.getElementById('hidden_jabatan');

    const posisiUrl = '{{ str_replace("__DEPT_ID__", "{departemenId}", route("api.posisi.daftar", ["departemen" => "__DEPT_ID__"])) }}';

    function syncHiddenFields() {
        const deptText = departemenSelect.options[departemenSelect.selectedIndex]?.text || '';
        const posisiText = posisiSelect.options[posisiSelect.selectedIndex]?.text || '';
        hiddenDepartemen.value = deptText;
        hiddenJabatan.value = posisiText;
    }

    departemenSelect.addEventListener('change', function () {
        const deptId = this.value;
        posisiSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
        if (!deptId) { syncHiddenFields(); return; }

        fetch(posisiUrl.replace('{departemenId}', deptId))
            .then(r => r.json())
            .then(data => {
                data.forEach(function (p) {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.nama_posisi;
                    posisiSelect.appendChild(opt);
                });
                syncHiddenFields();
            });
    });

    posisiSelect.addEventListener('change', syncHiddenFields);

    syncHiddenFields();
});
</script>
@endsection

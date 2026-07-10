@extends('layouts.admin', ['judulHalaman' => 'Ubah Data Karyawan'])

@section('content')
<div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-900">Ubah Data Karyawan</h2>
        <a href="{{ route('admin.data-karyawan.index') }}" class="text-sm text-slate-600 hover:text-slate-800">&larr; Kembali</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-md border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php $readonlyNik = $data->status === 'sudah_terpakai'; @endphp

    <form method="POST" action="{{ route('admin.data-karyawan.perbarui', $data->id) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="nik_karyawan" class="block text-sm font-medium text-slate-700">
                NIK
                @if ($readonlyNik)
                    <span class="text-xs text-slate-400">(sudah terpakai, tidak dapat diubah)</span>
                @endif
            </label>
            <input id="nik_karyawan" name="nik_karyawan" type="text" value="{{ old('nik_karyawan', $data->nik_karyawan) }}" required maxlength="30"
                   @if ($readonlyNik) disabled @endif
                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F] disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500">
        </div>

        <div>
            <label for="nama_karyawan" class="block text-sm font-medium text-slate-700">Nama Karyawan</label>
            <input id="nama_karyawan" name="nama_karyawan" type="text" value="{{ old('nama_karyawan', $data->nama_karyawan) }}" required maxlength="255"
                   class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="departemen" class="block text-sm font-medium text-slate-700">Departemen</label>
                <select id="departemen" name="departemen_id" required
                        class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <option value="">-- Pilih Departemen --</option>
                    @foreach($departemen as $item)
                        <option value="{{ $item->id }}" @if(old('departemen_id', $data->departemen_id) == $item->id) selected @endif>{{ $item->nama_departemen }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="posisi" class="block text-sm font-medium text-slate-700">Jabatan <span class="text-slate-400">(opsional)</span></label>
                <select id="posisi" name="posisi_id"
                        class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($posisi as $item)
                        <option value="{{ $item->id }}" @if(old('posisi_id', $data->posisi_id) == $item->id) selected @endif>{{ $item->nama_posisi }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if ($readonlyNik)
            <input type="hidden" name="nik_karyawan" value="{{ $data->nik_karyawan }}">
        @endif

        {{-- Legacy fields for backward compatibility --}}
        <input type="hidden" name="departemen" id="hidden_departemen" value="{{ old('departemen', $data->departemen) }}">
        <input type="hidden" name="jabatan" id="hidden_jabatan" value="{{ old('jabatan', $data->jabatan) }}">

        <div class="sticky bottom-0 -mx-6 mt-4 flex justify-end gap-2 border-t border-slate-200 bg-white px-6 py-3 shadow-[0_-2px_4px_rgba(0,0,0,0.04)]">
            <a href="{{ route('admin.data-karyawan.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</a>
            <button type="submit" class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">Perbarui</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const departemenSelect = document.getElementById('departemen');
    const posisiSelect = document.getElementById('posisi');
    const hiddenDepartemen = document.getElementById('hidden_departemen');
    const hiddenJabatan = document.getElementById('hidden_jabatan');

    const posisiUrl = '{{ route("admin.data-karyawan.posisi.api.daftar", ":deptId") }}'.replace(':deptId', '{departemenId}');

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
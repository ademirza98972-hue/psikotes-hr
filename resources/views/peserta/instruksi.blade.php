@extends('layouts.peserta', ['judulHalaman' => 'Instruksi Pengerjaan Tes'])

@section('content')
<div class="rounded-lg border border-slate-200 bg-white p-8 shadow-sm"
     x-data="{ persetujuan: false }">
    <h1 class="text-2xl font-bold text-slate-900 mb-2">Instruksi Pengerjaan Tes</h1>
    <p class="text-slate-600 mb-6">{{ $nama_sesi }}</p>

    <!-- Daftar Alat Tes -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-slate-900 mb-4 border-b border-slate-200 pb-2">Alat Tes yang Akan Dikerjakan</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-4 py-3 text-left text-slate-700 font-medium">#</th>
                        <th class="px-4 py-3 text-left text-slate-700 font-medium">Nama Alat Tes</th>
                        <th class="px-4 py-3 text-left text-slate-700 font-medium">Format Soal</th>
                        <th class="px-4 py-3 text-left text-slate-700 font-medium">Jumlah Soal</th>
                        <th class="px-4 py-3 text-left text-slate-700 font-medium">Estimasi Durasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($daftarAlatTesDenganDetail as $index => $alat)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-600 font-medium">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-900">{{ $alat['nama_alat_tes'] }}</span>
                                <span class="text-xs text-slate-500 ml-2">({{ $alat['kode_alat_tes'] }})</span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $alat['format_dasar'] }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $alat['jumlah_soal'] }} soal</td>
                            <td class="px-4 py-3 text-slate-700">{{ $alat['durasi_menit'] }} menit</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Peraturan Umum -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4 border-b border-slate-200 pb-2">Peraturan Umum</h2>
        <ul class="space-y-2 text-sm text-slate-700">
            <li class="flex items-start">
                <span class="mr-2 mt-0.5 text-teal-600 font-semibold">•</span>
                <span>Soal dan pilihan jawaban akan diacak berbeda untuk setiap peserta</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2 mt-0.5 text-teal-600 font-semibold">•</span>
                <span>Jawaban tersimpan otomatis, tidak akan hilang jika koneksi terputus</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2 mt-0.5 text-teal-600 font-semibold">•</span>
                <span>Untuk soal dengan batas waktu per soal, Anda TIDAK DAPAT kembali ke soal sebelumnya</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2 mt-0.5 text-teal-600 font-semibold">•</span>
                <span>Untuk soal dengan batas waktu keseluruhan sesi, Anda DAPAT kembali ke soal sebelumnya selama waktu masih tersisa</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2 mt-0.5 text-teal-600 font-semibold">•</span>
                <span>Sistem akan mendeteksi if Anda berpindah tab/window selama tes berlangsung</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2 mt-0.5 text-teal-600 font-semibold">•</span>
                <span>Salin-tempel (copy-paste) dinonaktifkan selama pengerjaan tes</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2 mt-0.5 text-teal-600 font-semibold">•</span>
                <span>Tes akan otomatis disubmit jika waktu habis</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2 mt-0.5 text-teal-600 font-semibold">•</span>
                <span>Setelah disubmit, jawaban tidak dapat diubah kembali</span>
            </li>
        </ul>
    </div>

    <!-- Checkbox Persetujuan -->
    <div class="mb-6 flex items-start">
        <input type="checkbox" id="persetujuan" x-model="persetujuan"
            class="mt-1 h-4 w-4 text-[#2C5F6F] rounded focus:ring-[#2C5F6F]" />
        <label for="persetujuan" class="ml-3 text-sm text-slate-700">
            Saya telah membaca dan memahami peraturan di atas
        </label>
    </div>

    <!-- Tombol Aksi -->
    <div class="flex gap-3">
        <a href="{{ route('peserta.tes.kerjakan', $sesiId) }}"
           :class="{
               'bg-[#2C5F6F] text-white hover:bg-[#1e4450] transition': persetujuan,
               'bg-slate-200 text-slate-500 cursor-not-allowed pointer-events-none': !persetujuan
           }"
           class="inline-flex items-center rounded-md px-4 py-2 text-sm font-medium">
            Mulai Mengerjakan
        </a>

        <a href="{{ route('peserta.dashboard') }}"
           class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-slate-300 hover:bg-slate-50 transition">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
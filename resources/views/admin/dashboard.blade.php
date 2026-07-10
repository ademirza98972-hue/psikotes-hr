@extends('layouts.admin', ['judulHalaman' => 'Dashboard'])

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>

    <div class="w-full px-4 py-4 space-y-6">

        {{-- 1. AKSI CEPAT --}}
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi Cepat</p>
            <div class="flex flex-wrap gap-3">
                @auth
                    @if(auth()->user()->hasIzin('pengguna.tambah'))
                        <a href="{{ route('admin.akun-karyawan.tambah') }}"
                           class="inline-flex items-center gap-2 rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:opacity-90">
                            <span class="text-base leading-none">+</span> Tambah Karyawan
                        </a>
                    @endif

                    @if(auth()->user()->hasIzin('pengguna.tambah'))
                        <a href="{{ route('admin.data-kandidat.tambah') }}"
                           class="inline-flex items-center gap-2 rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:opacity-90">
                            <span class="text-base leading-none">+</span> Tambah Kandidat
                        </a>
                    @endif

                    @if(auth()->user()->hasIzin('peran.kelola'))
                        <a href="{{ route('admin.peran.tambah') }}"
                           class="inline-flex items-center gap-2 rounded-md bg-slate-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:opacity-90">
                            <span class="text-base leading-none">+</span> Tambah Peran
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        {{-- 2. STAT CARDS --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

            {{-- Total Karyawan --}}
            <div class="relative flex flex-col rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <div class="absolute top-4 right-4 flex h-10 w-10 items-center justify-center rounded-full bg-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <p class="text-xs uppercase tracking-wide text-slate-500">Total Karyawan</p>
                <p class="mt-2 text-4xl font-bold text-blue-700">{{ number_format($totalKaryawan) }}</p>
                <p class="mt-1 text-xs text-slate-400">Karyawan terdaftar</p>
            </div>

            {{-- Total Kandidat --}}
            <div class="relative flex flex-col rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <div class="absolute top-4 right-4 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m9 11 3 3L22 4"/></svg>
                </div>
                <p class="text-xs uppercase tracking-wide text-slate-500">Total Kandidat</p>
                <p class="mt-2 text-4xl font-bold text-emerald-700">{{ number_format($totalKandidat) }}</p>
                <div class="mt-2 flex items-center gap-3 text-xs">
                    <span class="text-amber-600 font-medium">{{ $kandidatMenunggu }}</span> menunggu
                    <span class="text-emerald-600 font-medium">{{ $kandidatAktif }}</span> aktif
                    <span class="text-rose-600 font-medium">{{ $kandidatDitolak }}</span> ditolak
                </div>
            </div>

            {{-- Admin & Staff --}}
            <div class="relative flex flex-col rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <div class="absolute top-4 right-4 flex h-10 w-10 items-center justify-center rounded-full bg-violet-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                </div>
                <p class="text-xs uppercase tracking-wide text-slate-500">Admin &amp; Staff</p>
                <p class="mt-2 text-4xl font-bold text-violet-700">{{ number_format($totalAdminStaff) }}</p>
                <p class="mt-1 text-xs text-slate-400">Admin/staff sistem</p>
            </div>

            {{-- NIK Belum Terpakai --}}
            <div class="relative flex flex-col rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <div class="absolute top-4 right-4 flex h-10 w-10 items-center justify-center rounded-full bg-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="9" cy="12" r="2"/><path d="M15 8h4M15 12h4M15 16h4"/><path d="M11 17a3 3 0 0 0-6 0"/></svg>
                </div>
                <p class="text-xs uppercase tracking-wide text-slate-500">NIK Belum Terpakai</p>
                <p class="mt-2 text-4xl font-bold text-amber-700">{{ number_format($nikBelumTerpakai) }}</p>
                <p class="mt-1 text-xs text-slate-400">NIK siap digunakan</p>
            </div>

            {{-- Total Peran --}}
            <div class="relative flex flex-col rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <div class="absolute top-4 right-4 flex h-10 w-10 items-center justify-center rounded-full bg-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="5.5"/><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                </div>
                <p class="text-xs uppercase tracking-wide text-slate-500">Total Peran</p>
                <p class="mt-2 text-4xl font-bold text-slate-700">{{ number_format($totalPeran) }}</p>
                <p class="mt-1 text-xs text-slate-400">Peran yang tersedia</p>
            </div>

            {{-- Total Data Karyawan --}}
            <div class="relative flex flex-col rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <div class="absolute top-4 right-4 flex h-10 w-10 items-center justify-center rounded-full bg-cyan-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0 0 18 0V5"/><path d="M3 12a9 3 0 0 0 18 0"/></svg>
                </div>
                <p class="text-xs uppercase tracking-wide text-slate-500">Total Data Karyawan</p>
                <p class="mt-2 text-4xl font-bold text-cyan-700">{{ number_format($totalDataKaryawan) }}</p>
                <p class="mt-1 text-xs text-slate-400">Seluruh data karyawan (semua status)</p>
            </div>

        </div>

        {{-- 3. KANDIDAT MENUNGGU VERIFIKASI (compact, single row) --}}
        @if($kandidatMenunggu > 0)
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-3 flex items-center gap-3">
                    <span class="inline-block h-5 w-1 rounded-full bg-amber-500"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                    <h2 class="text-base font-semibold text-slate-900">Kandidat Menunggu Verifikasi</h2>
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">{{ $kandidatMenunggu }} menunggu</span>
                </div>

                <div>
                    @foreach($kandidatMenungguList as $k)
                        @php
                            $lama = $k->created_at->diffForHumans(null, true);
                            $hari = $k->created_at->diffInDays(now());
                            $terlambat = $hari > 2;
                            $profil = $k->profilKandidat;
                            $posisi = $profil ? $profil->posisi_dilamar : '-';
                        @endphp
                        <div class="flex items-center justify-between {{ !$loop->last ? 'border-b border-slate-100' : '' }} py-2">
                            <div class="flex min-w-0 flex-1 items-center gap-2">
                                <span class="truncate text-sm font-medium text-slate-900">{{ $k->name }}</span>
                                <span class="shrink-0 text-xs text-slate-500">&middot;</span>
                                <span class="shrink-0 truncate text-xs text-slate-500">{{ $posisi }}</span>
                                <span class="shrink-0 text-xs text-slate-500">&middot;</span>
                                <span class="shrink-0 inline-flex items-center rounded px-1.5 py-0.5 text-[11px] font-medium {{ $terlambat ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">{{ $lama }}</span>
                            </div>
                            <div class="ml-2 shrink-0 flex items-center gap-1.5">
                                <form action="{{ route('admin.data-kandidat.approve', $k->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="rounded bg-emerald-600 px-2 py-0.5 text-[11px] font-semibold text-white hover:bg-emerald-700 transition">Aktifkan</button>
                                </form>
                                <form action="{{ route('admin.data-kandidat.tolak', $k->id) }}" method="POST" onsubmit="return confirm('Tolak kandidat {{ $k->name }}?')">
                                    @csrf
                                    <button type="submit" class="rounded bg-rose-600 px-2 py-0.5 text-[11px] font-semibold text-white hover:bg-rose-700 transition">Tolak</button>
                                </form>
                            </div>
                        </div>
                    @endforeach

                    @if($kandidatMenunggu > 2)
                        <div class="mt-2 pt-2 border-t border-slate-100 text-center">
                            <a href="{{ route('admin.data-kandidat.index') }}"
                               class="inline-flex items-center gap-2 rounded-md border-2 border-amber-400 bg-amber-50 px-5 py-2 text-sm font-semibold text-amber-700 hover:border-amber-500 hover:bg-amber-100 transition">
                                Lihat {{ $kandidatMenunggu - 2 }} lainnya
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- 4. GRAFIK PENDAFTARAN 7 HARI TERAKHIR (full-width) --}}
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center gap-2">
                <span class="inline-block h-5 w-1 rounded-full bg-blue-500"></span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                <h2 class="text-base font-semibold text-slate-900">Pendaftaran 7 Hari Terakhir</h2>
            </div>
            <div class="h-64">
                <canvas id="barPendaftaran"></canvas>
            </div>
        </div>

    </div>

    <script>
        (function() {
            const ctx = document.getElementById('barPendaftaran');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labelTanggal),
                    datasets: [{
                        label: 'Pendaftaran',
                        data: @json($pendaftaran7Hari),
                        backgroundColor: 'rgba(44, 95, 111, 0.8)',
                        hoverBackgroundColor: 'rgba(44, 95, 111, 1)',
                        borderRadius: 6,
                        barPercentage: 0.6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#fff',
                            bodyColor: '#cbd5e1',
                            padding: 10,
                            cornerRadius: 6,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' pendaftaran';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#94a3b8', font: { size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 11 },
                                stepSize: 1,
                            },
                            grid: { color: '#f1f5f9' }
                        }
                    }
                }
            });
        })();
    </script>
@endsection

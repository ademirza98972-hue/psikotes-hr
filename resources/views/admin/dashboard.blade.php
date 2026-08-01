@extends('layouts.admin', ['judulHalaman' => 'Dashboard'])

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>

    <div class="max-w-[1440px] mx-auto space-y-5">

        {{-- 1. AKSI CEPAT --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm">
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-[#40484b]">Aksi Cepat</p>
            <div class="flex flex-wrap gap-3">
                @auth
                    @if(auth()->user()->hasIzin('pengguna.tambah'))
                        <a href="{{ route('admin.akun-karyawan.tambah') }}"
                           class="inline-flex items-center gap-2 rounded-lg bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:opacity-90">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            Tambah Karyawan
                        </a>
                    @endif

                    @if(auth()->user()->hasIzin('pengguna.tambah'))
                        <a href="{{ route('admin.data-kandidat.tambah') }}"
                           class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:opacity-90">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            Tambah Kandidat
                        </a>
                    @endif

                    @if(auth()->user()->hasIzin('peran.kelola'))
                        <a href="{{ route('admin.peran.tambah') }}"
                           class="inline-flex items-center gap-2 rounded-lg bg-[#232b3f] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:opacity-90">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            Tambah Peran
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        {{-- 2. STAT CARDS --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

            {{-- Total Karyawan --}}
            <div class="relative flex flex-col rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-[#2C5F6F]/10 text-[#2C5F6F]">
                    <span class="material-symbols-outlined text-[28px]">group</span>
                </div>
                <p class="text-[12px] text-[#40484b]">Total Karyawan Terdaftar</p>
                <h3 class="mt-1 text-[32px] font-bold text-[#191c1e]">{{ number_format($totalKaryawan) }}</h3>
            </div>

            {{-- Total Kandidat --}}
            <div class="relative flex flex-col rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-[#d4e3ff]/40 text-[#505f76]">
                    <span class="material-symbols-outlined text-[28px]">person_search</span>
                </div>
                <p class="text-[12px] text-[#40484b]">Total Kandidat</p>
                <h3 class="mt-1 text-[32px] font-bold text-[#191c1e]">{{ number_format($totalKandidat) }}</h3>
                <div class="mt-3 flex items-center gap-3 text-[12px]">
                    <span class="text-amber-600 font-semibold">{{ $kandidatMenunggu }}</span> menunggu
                    <span class="text-emerald-600 font-semibold">{{ $kandidatAktif }}</span> aktif
                    <span class="text-rose-600 font-semibold">{{ $kandidatDitolak }}</span> ditolak
                </div>
            </div>

            {{-- Admin & Staff --}}
            <div class="relative flex flex-col rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-[#dae2fd]/50 text-[#232b3f]">
                    <span class="material-symbols-outlined text-[28px]">admin_panel_settings</span>
                </div>
                <p class="text-[12px] text-[#40484b]">Admin & Staff</p>
                <h3 class="mt-1 text-[32px] font-bold text-[#191c1e]">{{ number_format($totalAdminStaff) }}</h3>
                <p class="mt-1 text-[11px] text-[#40484b]">Admin/staff sistem</p>
            </div>

            {{-- NIK Belum Terpakai --}}
            <div class="relative flex flex-col rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <span class="material-symbols-outlined text-[28px]">badge</span>
                </div>
                <p class="text-[12px] text-[#40484b]">NIK Belum Terpakai</p>
                <h3 class="mt-1 text-[32px] font-bold text-[#191c1e]">{{ number_format($nikBelumTerpakai) }}</h3>
                <p class="mt-1 text-[11px] text-[#40484b]">NIK siap digunakan</p>
            </div>

            {{-- Total Peran --}}
            <div class="relative flex flex-col rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-[#e0e3e5]/60 text-[#191c1e]">
                    <span class="material-symbols-outlined text-[28px]">key</span>
                </div>
                <p class="text-[12px] text-[#40484b]">Total Peran</p>
                <h3 class="mt-1 text-[32px] font-bold text-[#191c1e]">{{ number_format($totalPeran) }}</h3>
                <p class="mt-1 text-[11px] text-[#40484b]">Peran yang tersedia</p>
            </div>

            {{-- Total Data Karyawan --}}
            <div class="relative flex flex-col rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm transition-shadow duration-200 hover:shadow-md">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-50 text-cyan-700">
                    <span class="material-symbols-outlined text-[28px]">database</span>
                </div>
                <p class="text-[12px] text-[#40484b]">Total Data Karyawan</p>
                <h3 class="mt-1 text-[32px] font-bold text-[#191c1e]">{{ number_format($totalDataKaryawan) }}</h3>
                <p class="mt-1 text-[11px] text-[#40484b]">Seluruh data karyawan (semua status)</p>
            </div>

        </div>

        {{-- 3. KANDIDAT MENUNGGU VERIFIKASI --}}
        @if($kandidatMenunggu > 0)
            <div class="rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50">
                        <span class="material-symbols-outlined text-[24px] text-amber-600">pending_actions</span>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-[#191c1e]">Kandidat Menunggu Verifikasi</h2>
                        <p class="text-[12px] text-[#40484b]">{{ $kandidatMenunggu }} kandidat menunggu persetujuan</p>
                    </div>
                    <span class="ml-auto inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">{{ $kandidatMenunggu }} menunggu</span>
                </div>

                <div class="space-y-0">
                    @foreach($kandidatMenungguList as $k)
                        @php
                            $lama = $k->created_at->diffForHumans(null, true);
                            $hari = $k->created_at->diffInDays(now());
                            $terlambat = $hari > 2;
                            $profil = $k->profilKandidat;
                            $posisi = $profil ? $profil->posisi_dilamar : '-';
                            $inisial = mb_substr($k->name, 0, 1, 'UTF-8');
                        @endphp
                        <div class="flex items-center justify-between {{ !$loop->last ? 'border-b border-[#f2f4f6]' : '' }} py-3">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#2C5F6F]/10 text-[#2C5F6F] text-xs font-bold">
                                    {{ $inisial }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-[#191c1e]">{{ $k->name }}</p>
                                    <p class="text-[11px] text-[#40484b]">{{ $posisi }}</p>
                                </div>
                                <span class="shrink-0 text-[11px] text-[#40484b]">{{ $lama }}</span>
                            </div>
                            <div class="ml-3 shrink-0 flex items-center gap-2">
                                <form action="{{ route('admin.data-kandidat.approve', $k->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-[12px] font-semibold text-white hover:bg-emerald-700 transition">
                                        <span class="material-symbols-outlined text-[16px]">check</span>
                                        Aktifkan
                                    </button>
                                </form>
                                <form action="{{ route('admin.data-kandidat.tolak', $k->id) }}" method="POST" onsubmit="return confirm('Tolak kandidat {{ $k->name }}?')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-rose-600 px-3 py-1.5 text-[12px] font-semibold text-white hover:bg-rose-700 transition">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($kandidatMenunggu > 2)
                    <div class="mt-4 pt-3 border-t border-[#e0e3e5] text-center">
                        <a href="{{ route('admin.data-kandidat.index') }}"
                           class="inline-flex items-center gap-2 rounded-lg border border-[#e0e3e5] bg-[#f2f4f6] px-4 py-2 text-sm font-semibold text-[#2C5F6F] hover:bg-[#e8f0f2] transition">
                            Lihat {{ $kandidatMenunggu - 2 }} lainnya
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </a>
                    </div>
                @endif
            </div>
        @endif

        {{-- 4. GRAFIK PENDAFTARAN 7 HARI TERAKHIR --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#2C5F6F]/10">
                        <span class="material-symbols-outlined text-[24px] text-[#2C5F6F]">bar_chart</span>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-[#191c1e]">Pendaftaran 7 Hari Terakhir</h2>
                        <p class="text-[12px] text-[#40484b]">Grafik rekapitulasi pendaftaran harian</p>
                    </div>
                </div>
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

            const chartLabels = @json($labelTanggal);
            const chartData = @json($pendaftaran7Hari);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Pendaftaran',
                        data: chartData,
                        backgroundColor: 'rgba(44, 95, 111, 0.85)',
                        hoverBackgroundColor: 'rgba(44, 95, 111, 1)',
                        borderRadius: 8,
                        borderSkipped: false,
                        barPercentage: 0.55,
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
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
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
                            ticks: { color: '#94a3b8', font: { size: 12 } },
                            border: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 12 },
                                stepSize: 1,
                            },
                            grid: { color: '#f2f4f6' },
                            border: { display: false }
                        }
                    }
                }
            });
        })();
    </script>
@endsection

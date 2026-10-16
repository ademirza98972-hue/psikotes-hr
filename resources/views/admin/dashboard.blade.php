@extends('layouts.admin', ['judulHalaman' => 'Dashboard'])

@section('content')
@php
    $u = auth()->user();
    $bisaKelolaTes = $u->hasIzin('soal.lihat') || $u->hasIzin('kategori_tes.kelola');
    $bisaLihatHasil = $u->hasIzin('hasil_tes.lihat');
    $bisaVerifikasi = $u->hasIzin('pengguna.verifikasi');
    $labelKejadian = ['pindah_tab' => 'Pindah tab', 'keluar_fullscreen' => 'Keluar layar penuh', 'halaman_ditutup' => 'Halaman ditutup'];
    $persen = fn ($n) => $pesertaTotal > 0 ? round($n / $pesertaTotal * 100) : 0;
    $angka = fn ($n) => number_format($n, 0, ',', '.');

    // Urutan warna KPI mengikuti gradasi wordmark logo: indigo -> biru -> teal -> hijau
    $kpi = [
        ['Sesi Berjalan', $jumlahSesiBerjalan, 'event_available', "{$jumlahSesiMendatang} sesi mendatang", 'text-brand-indigo bg-brand-indigo/10', 'bg-brand-indigo'],
        ['Total Peserta', $pesertaTotal, 'groups', 'Di semua sesi yang diterbitkan', 'text-brand-blue bg-brand-blue/10', 'bg-brand-blue'],
        ['Sedang Mengerjakan', $pesertaSedang, 'pending_actions', "{$pesertaBelum} lainnya belum mulai", 'text-brand-teal bg-brand-teal/10', 'bg-brand-teal'],
        ['Tes Selesai', $pesertaSelesai, 'task_alt', "{$tingkatPenyelesaian}% tingkat penyelesaian", 'text-brand-green-deep bg-brand-green/10', 'bg-brand-green'],
    ];

    $statusPengerjaan = [
        ['Selesai', $pesertaSelesai, 'bg-brand-green', 'Tes sudah dikumpulkan'],
        ['Sedang mengerjakan', $pesertaSedang, 'bg-brand-blue', 'Sesi masih berjalan'],
        ['Belum mulai', $pesertaBelum, 'bg-zinc-300', 'Sesi berjalan atau mendatang'],
        ['Tidak selesai', $pesertaTidakSelesai, 'bg-rose-400', 'Sesi berakhir sebelum tes selesai'],
    ];

    $kelilingCincin = 2 * M_PI * 44;
@endphp

<div class="mx-auto max-w-7xl space-y-6">

    {{-- HERO: ringkasan hari ini --}}
    <section class="relative overflow-hidden rounded-2xl bg-brand-ink text-white shadow-sm">
        <div class="bg-brand-gradient h-1"></div>
        {{-- motif lengkung, gema swoosh logo --}}
        <svg class="pointer-events-none absolute -right-24 -top-28 size-[26rem] text-brand-green opacity-25 sm:-right-10" viewBox="0 0 400 400" fill="none" aria-hidden="true">
            <circle cx="200" cy="200" r="190" stroke="currentColor" stroke-width="2" stroke-dasharray="820 400" transform="rotate(-30 200 200)" />
            <circle cx="200" cy="200" r="165" stroke="currentColor" stroke-width="6" stroke-dasharray="640 400" transform="rotate(10 200 200)" />
            <circle cx="200" cy="200" r="138" stroke="currentColor" stroke-width="3" stroke-dasharray="520 400" transform="rotate(50 200 200)" />
            <circle cx="200" cy="200" r="112" stroke="currentColor" stroke-width="8" stroke-dasharray="400 400" transform="rotate(95 200 200)" />
            <circle cx="200" cy="200" r="86" stroke="currentColor" stroke-width="2" stroke-dasharray="300 400" transform="rotate(140 200 200)" />
        </svg>

        <div class="relative flex flex-col gap-6 p-5 sm:p-7 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-2xl space-y-3">
                <p class="flex items-center gap-2 text-xs font-medium uppercase tracking-[0.12em] text-white/60">
                    <span class="material-symbols-outlined text-[16px]">calendar_today</span>{{ $tanggalHariIni }}
                </p>
                <h1 class="text-2xl font-semibold tracking-tight sm:text-3xl">{{ $sapaan }}, {{ $namaPengguna }}</h1>
                <p class="text-sm leading-relaxed text-white/70">
                    Hari ini ada <span class="font-semibold text-white">{{ $angka($jumlahSesiBerjalan) }} sesi berjalan</span>,
                    <span class="font-semibold text-white">{{ $angka($kandidatMenunggu) }} kandidat</span> menunggu verifikasi, dan
                    <span class="font-semibold text-white">{{ $angka($peringatan30Hari) }} peringatan integritas</span> dalam 30 hari terakhir.
                </p>
                <div class="flex flex-wrap gap-2 pt-1">
                    @if ($bisaKelolaTes)
                        <a href="{{ route('admin.penjadwalan-tes.tambah') }}" class="inline-flex h-9 items-center gap-2 rounded-md bg-brand-green px-4 text-sm font-medium text-white shadow-xs transition-colors hover:bg-brand-green-deep focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            <span class="material-symbols-outlined text-[18px]">add</span>Jadwalkan Tes
                        </a>
                    @endif
                    @if ($u->hasIzin('pengguna.tambah'))
                        <a href="{{ route('admin.data-kandidat.tambah') }}" class="inline-flex h-9 items-center gap-2 rounded-md border border-white/20 bg-white/5 px-4 text-sm font-medium text-white transition-colors hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            <span class="material-symbols-outlined text-[18px]">person_add</span>Tambah Kandidat
                        </a>
                    @endif
                </div>
            </div>

            {{-- cincin tingkat penyelesaian --}}
            <div class="flex items-center gap-4 rounded-xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm lg:shrink-0">
                <div class="relative size-24 shrink-0">
                    <svg viewBox="0 0 100 100" class="size-full -rotate-90" aria-hidden="true">
                        <circle cx="50" cy="50" r="44" fill="none" stroke="currentColor" stroke-width="8" class="text-white/10" />
                        <circle cx="50" cy="50" r="44" fill="none" stroke="var(--brand-green)" stroke-width="8" stroke-linecap="round"
                                stroke-dasharray="{{ $kelilingCincin }}" stroke-dashoffset="{{ $kelilingCincin * (1 - $tingkatPenyelesaian / 100) }}" />
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-xl font-semibold tabular-nums">{{ $tingkatPenyelesaian }}%</span>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-medium">Tingkat penyelesaian</p>
                    <p class="text-xs leading-relaxed text-white/60">{{ $angka($pesertaSelesai) }} dari {{ $angka($pesertaTotal) }} peserta<br>telah menyelesaikan tes</p>
                </div>
            </div>
        </div>
    </section>

    {{-- KPI --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
        @foreach ($kpi as [$judul, $nilai, $ikon, $sub, $warnaIkon, $warnaGaris])
            <x-ui.card class="relative overflow-hidden p-4 sm:p-5">
                <span class="absolute inset-y-0 left-0 w-1 {{ $warnaGaris }}" aria-hidden="true"></span>
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium text-muted-foreground">{{ $judul }}</p>
                    <span class="flex size-8 shrink-0 items-center justify-center rounded-lg sm:size-9 {{ $warnaIkon }}">
                        <span class="material-symbols-outlined text-[20px]">{{ $ikon }}</span>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-semibold tabular-nums tracking-tight sm:text-3xl">{{ $angka($nilai) }}</p>
                <p class="mt-1 text-xs text-muted-foreground">{{ $sub }}</p>
            </x-ui.card>
        @endforeach
    </div>

    {{-- RINGKASAN PENGGUNA --}}
    <x-ui.card class="overflow-hidden">
        <div class="flex items-center gap-2 border-b border-border px-5 py-3">
            <span class="material-symbols-outlined text-[18px] text-brand-teal">group</span>
            <h2 class="text-sm font-semibold">Ringkasan Pengguna</h2>
            <span class="hidden text-sm text-muted-foreground sm:inline">· Akun yang terdaftar di sistem</span>
        </div>
        <div class="grid grid-cols-2 gap-px bg-border lg:grid-cols-4">
            @foreach ([
                ['Akun Karyawan', $totalKaryawan, 'badge', "+{$karyawanBaru7Hari} minggu ini · {$karyawanWajibGantiPassword} belum ganti password", 'admin.akun-karyawan.index', 'pengguna.lihat'],
                ['Akun Kandidat', $totalKandidat, 'person_search', "{$kandidatAktif} aktif · {$kandidatDitolak} ditolak", 'admin.data-kandidat.index', 'pengguna.lihat'],
                ['Karyawan Nonaktif', $karyawanNonaktif, 'person_off', 'Belum bisa login sampai diaktifkan', 'admin.akun-karyawan.index', 'pengguna.lihat'],
                ['Admin & Staff', $totalAdminStaff, 'manage_accounts', "{$totalPeran} peran tersedia", 'admin.pengguna-admin.index', 'pengguna_admin.kelola'],
            ] as [$judul, $nilai, $ikon, $sub, $rute, $izin])
                @php $tag = $u->hasIzin($izin) ? 'a' : 'div'; @endphp
                <{{ $tag }} @if ($tag === 'a') href="{{ route($rute) }}" @endif
                    class="group flex items-start gap-3 bg-card px-4 py-4 transition-colors sm:px-5 {{ $tag === 'a' ? 'hover:bg-muted/40' : '' }}">
                    <span class="hidden size-9 shrink-0 items-center justify-center rounded-lg bg-muted text-muted-foreground transition-colors sm:flex {{ $tag === 'a' ? 'group-hover:bg-brand-teal/10 group-hover:text-brand-teal' : '' }}">
                        <span class="material-symbols-outlined text-[20px]">{{ $ikon }}</span>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-muted-foreground">{{ $judul }}</p>
                        <p class="mt-0.5 text-xl font-semibold tabular-nums">{{ $angka($nilai) }}</p>
                        <p class="mt-0.5 text-xs text-muted-foreground">{{ $sub }}</p>
                    </div>
                </{{ $tag }}>
            @endforeach
        </div>
    </x-ui.card>

    {{-- TREN + STATUS PENGERJAAN --}}
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <x-ui.card class="min-w-0 py-6 xl:col-span-2"
                   x-data="grafikTren({{ Js::from($tren) }})" x-init="mulai()">
            <x-ui.card-header>
                <x-slot:title>
                    <span x-text="seri === 'selesai' ? 'Tes Diselesaikan' : 'Pendaftaran Akun'"></span>
                </x-slot:title>
                <x-slot:description>
                    <span x-text="(seri === 'selesai' ? 'Peserta yang menyelesaikan tes' : 'Akun baru semua tipe') + ', ' + rentang + ' hari terakhir'"></span>
                </x-slot:description>
                <p class="text-right">
                    <span class="block text-2xl font-semibold tabular-nums" x-text="total"></span>
                    <span class="text-xs text-muted-foreground">total periode</span>
                </p>
            </x-ui.card-header>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-2 px-6">
                <x-ui.tabs model="seri" :items="['selesai' => 'Tes selesai', 'pendaftaran' => 'Pendaftaran']" />
                <x-ui.tabs model="rentang" :items="['7' => '7 hari', '30' => '30 hari', '90' => '90 hari']" />
            </div>
            <div class="mt-4 h-64 px-4">
                <canvas x-ref="kanvas" role="img" aria-label="Grafik batang jumlah per hari"></canvas>
            </div>
        </x-ui.card>

        <x-ui.card class="flex flex-col gap-5 py-6">
            <x-ui.card-header title="Status Pengerjaan" description="Peserta di semua sesi yang diterbitkan" />
            <div class="px-6">
                <div class="flex h-3 w-full overflow-hidden rounded-full bg-muted" role="img"
                     aria-label="Komposisi status pengerjaan peserta">
                    @foreach ($statusPengerjaan as [$label, $jumlah, $warna])
                        @if ($jumlah > 0)
                            <div class="h-full {{ $warna }} not-last:border-r-2 not-last:border-card" style="width: {{ $pesertaTotal > 0 ? $jumlah / $pesertaTotal * 100 : 0 }}%"></div>
                        @endif
                    @endforeach
                </div>
            </div>
            <ul class="grid gap-1 px-3">
                @foreach ($statusPengerjaan as [$label, $jumlah, $warna, $keterangan])
                    <li class="flex items-center gap-3 rounded-md px-3 py-2">
                        <span class="size-2.5 shrink-0 rounded-sm {{ $warna }}"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium">{{ $label }}</p>
                            <p class="truncate text-xs text-muted-foreground">{{ $keterangan }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold tabular-nums">{{ $angka($jumlah) }}</p>
                            <p class="text-xs tabular-nums text-muted-foreground">{{ $persen($jumlah) }}%</p>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-auto grid grid-cols-2 border-t border-border">
                <div class="border-r border-border px-6 pt-4">
                    <p class="text-xs text-muted-foreground">Kandidat menunggu</p>
                    <p class="mt-0.5 text-lg font-semibold tabular-nums">{{ $angka($kandidatMenunggu) }}</p>
                </div>
                <div class="px-6 pt-4">
                    <p class="text-xs text-muted-foreground">Peringatan 30 hari</p>
                    <p class="mt-0.5 text-lg font-semibold tabular-nums">{{ $angka($peringatan30Hari) }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- SESI AKTIF + VERIFIKASI --}}
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <x-ui.card class="flex min-w-0 flex-col gap-4 py-6 xl:col-span-2">
            <x-ui.card-header title="Sesi Tes Aktif" description="Sesi yang sedang berjalan atau akan datang">
                @if ($bisaKelolaTes)
                    <x-ui.button variant="outline" size="sm" :href="route('admin.penjadwalan-tes.index')">Lihat semua</x-ui.button>
                @endif
            </x-ui.card-header>
            @if ($daftarSesi->isEmpty())
                <x-ui.empty icon="event_busy" title="Tidak ada sesi aktif" description="Jadwalkan sesi tes baru untuk mulai mengundang peserta." />
            @else
                <div>
                <div class="hidden grid-cols-[minmax(0,1fr)_7rem_12rem] gap-4 border-b border-border px-6 pb-2 text-xs font-medium text-muted-foreground md:grid">
                    <span>Sesi</span><span>Status</span><span>Progres</span>
                </div>
                <ul class="divide-y divide-border border-t border-border md:border-t-0">
                    @foreach ($daftarSesi as $sesi)
                        @php
                            $mulai = \Illuminate\Support\Carbon::parse($sesi->tanggal_mulai);
                            $selesai = \Illuminate\Support\Carbon::parse($sesi->tanggal_selesai);
                            $berjalan = $mulai->lte(today());
                            $progres = $sesi->total_peserta > 0 ? round($sesi->total_selesai / $sesi->total_peserta * 100) : 0;
                            $sisaHari = (int) today()->diffInDays($selesai, false);
                        @endphp
                        <li class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-x-4 gap-y-3 px-6 py-3.5 transition-colors hover:bg-muted/50 md:grid-cols-[minmax(0,1fr)_7rem_12rem]">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded-lg {{ $berjalan ? 'bg-brand-green/10 text-brand-green-deep' : 'bg-muted text-muted-foreground' }}">
                                    <span class="material-symbols-outlined text-[20px]">{{ $berjalan ? 'play_circle' : 'schedule' }}</span>
                                </span>
                                <div class="min-w-0">
                                    @if ($bisaKelolaTes)
                                        <a href="{{ route('admin.penjadwalan-tes.detail', $sesi->id) }}" class="block truncate text-sm font-medium hover:underline">{{ $sesi->nama_sesi }}</a>
                                    @else
                                        <p class="truncate text-sm font-medium">{{ $sesi->nama_sesi }}</p>
                                    @endif
                                    <p class="truncate text-xs text-muted-foreground">
                                        {{ $mulai->translatedFormat('j M') }} – {{ $selesai->translatedFormat('j M Y') }}
                                        @if ($berjalan) · {{ $sisaHari === 0 ? 'Berakhir hari ini' : "Sisa {$sisaHari} hari" }}@endif
                                    </p>
                                </div>
                            </div>
                            <div>
                                <x-ui.badge :variant="$berjalan ? 'success' : 'secondary'">{{ $berjalan ? 'Berjalan' : 'Mendatang' }}</x-ui.badge>
                            </div>
                            <div class="col-span-2 flex items-center gap-3 md:col-span-1">
                                <x-ui.progress :value="$progres" />
                                <span class="w-12 shrink-0 text-right text-xs tabular-nums text-muted-foreground">{{ $sesi->total_selesai }}/{{ $sesi->total_peserta }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
                </div>
            @endif
        </x-ui.card>

        <x-ui.card class="flex flex-col gap-4 py-6">
            <x-ui.card-header title="Menunggu Verifikasi" description="Kandidat perlu persetujuan">
                @if ($kandidatMenunggu > 0)
                    <x-ui.badge variant="warning" class="tabular-nums">{{ $kandidatMenunggu }}</x-ui.badge>
                @endif
            </x-ui.card-header>
            @if ($kandidatMenunggu === 0)
                <x-ui.empty icon="how_to_reg" title="Semua sudah diverifikasi" description="Tidak ada kandidat yang menunggu." />
            @else
                <div class="flex flex-col gap-1 px-3">
                    @foreach ($kandidatMenungguList as $k)
                        <div class="flex items-center gap-3 rounded-md px-3 py-2 hover:bg-muted/50">
                            <x-ui.avatar :name="$k->name" size="size-9" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium">{{ $k->name }}</p>
                                <p class="truncate text-xs text-muted-foreground">
                                    {{ $k->profilKandidat?->posisi?->nama_posisi ?? '-' }} · {{ $k->created_at->diffForHumans(null, true) }}
                                </p>
                            </div>
                            @if ($bisaVerifikasi)
                            <form action="{{ route('admin.data-kandidat.approve', $k->id) }}" method="POST">
                                @csrf
                                <x-ui.button type="submit" variant="outline" size="icon-sm" title="Aktifkan" class="text-brand-green-deep hover:text-brand-green-deep">
                                    <span class="material-symbols-outlined">check</span>
                                    <span class="sr-only">Aktifkan {{ $k->name }}</span>
                                </x-ui.button>
                            </form>
                            <form action="{{ route('admin.data-kandidat.tolak', $k->id) }}" method="POST" onsubmit="return confirm('Tolak kandidat {{ $k->name }}?')">
                                @csrf
                                <x-ui.button type="submit" variant="outline" size="icon-sm" title="Tolak" class="text-destructive hover:text-destructive">
                                    <span class="material-symbols-outlined">close</span>
                                    <span class="sr-only">Tolak {{ $k->name }}</span>
                                </x-ui.button>
                            </form>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if ($kandidatMenunggu > $kandidatMenungguList->count() && $u->hasIzin('pengguna.lihat'))
                    <div class="mt-auto px-6">
                        <x-ui.button variant="ghost" size="sm" class="w-full" :href="route('admin.data-kandidat.index', ['status' => 'menunggu_verifikasi'])">
                            Lihat {{ $kandidatMenunggu - $kandidatMenungguList->count() }} kandidat lainnya
                            <span class="material-symbols-outlined">chevron_right</span>
                        </x-ui.button>
                    </div>
                @endif
            @endif
        </x-ui.card>
    </div>

    {{-- HASIL TERBARU + INTEGRITAS --}}
    @if ($bisaLihatHasil)
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <x-ui.card class="flex min-w-0 flex-col gap-4 py-6 xl:col-span-2">
            <x-ui.card-header title="Hasil Tes Terbaru" description="Peserta yang baru menyelesaikan tes">
                <x-ui.button variant="outline" size="sm" :href="route('admin.hasil-tes.index')">Lihat semua</x-ui.button>
            </x-ui.card-header>
            @if ($hasilTerbaru->isEmpty())
                <x-ui.empty icon="assignment" title="Belum ada hasil" description="Hasil akan muncul setelah peserta menyelesaikan tes." />
            @else
                <ul class="divide-y divide-border border-t border-border">
                    @foreach ($hasilTerbaru as $h)
                        <li>
                            <a href="{{ route('admin.hasil-tes.detail', [$h->sesi_tes_id, $h->user_id]) }}"
                               class="group grid grid-cols-[minmax(0,1fr)_auto] items-center gap-x-4 gap-y-2 px-6 py-3.5 transition-colors hover:bg-muted/50 md:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)_7rem_4rem]">
                                <div class="flex min-w-0 items-center gap-3">
                                    <x-ui.avatar :src="$h->user->foto_profil ? asset('storage/' . $h->user->foto_profil) : null" :name="$h->user->name" size="size-9" />
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium">{{ $h->user->name }}</p>
                                        <p class="truncate text-xs capitalize text-muted-foreground">{{ $h->user->tipe_akun }} · {{ $h->tanggal_pengerjaan?->translatedFormat('j M Y') ?? '-' }}</p>
                                    </div>
                                </div>
                                <p class="hidden truncate text-sm text-muted-foreground md:block">{{ $h->sesiTes->nama_sesi }}</p>
                                <div class="col-start-1 row-start-2 flex flex-wrap gap-1 pl-12 md:col-start-auto md:row-start-auto md:pl-0">
                                    @foreach ($h->alatTes as $at)
                                        <x-ui.badge variant="outline" class="font-mono">{{ $at->kode }}</x-ui.badge>
                                    @endforeach
                                </div>
                                <span class="row-span-2 flex items-center justify-end gap-1 text-sm font-medium text-muted-foreground transition-colors group-hover:text-foreground md:row-span-1">
                                    <span class="hidden sm:inline">Lihat</span>
                                    <span class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-0.5">arrow_forward</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

        <x-ui.card class="flex flex-col gap-4 py-6">
            <x-ui.card-header title="Peringatan Integritas" description="Aktivitas mencurigakan saat tes berlangsung" />
            @if ($pesertaMencurigakan->isEmpty())
                <x-ui.empty icon="verified_user" title="Tidak ada peringatan" description="Belum ada aktivitas mencurigakan yang tercatat." />
            @else
                <div class="flex flex-col gap-1 px-3">
                    @foreach ($pesertaMencurigakan as $p)
                        <a href="{{ route('admin.hasil-tes.detail', [$p->sesi_tes_id, $p->user_id]) }}" class="flex items-start gap-3 rounded-md px-3 py-2 transition-colors hover:bg-muted/50">
                            <x-ui.avatar :src="$p->user->foto_profil ? asset('storage/' . $p->user->foto_profil) : null" :name="$p->user->name" size="size-9" />
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate text-sm font-medium">{{ $p->user->name }}</p>
                                    <x-ui.badge variant="danger" class="tabular-nums">
                                        <span class="material-symbols-outlined text-[14px]">flag</span>{{ $p->jumlah_peringatan }}
                                    </x-ui.badge>
                                </div>
                                <p class="truncate text-xs text-muted-foreground">{{ $p->sesiTes->nama_sesi }} · {{ \Illuminate\Support\Carbon::parse($p->peringatan_terakhir)->diffForHumans() }}</p>
                                <p class="mt-1 truncate text-xs text-muted-foreground">
                                    {{ ($rincianPeringatan[$p->id] ?? collect())->map(fn ($r) => ($labelKejadian[$r->tipe_kejadian] ?? $r->tipe_kejadian) . ' ' . $r->total)->join(' · ') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>
    @endif
</div>

<script>
function grafikTren(data) {
    let chart = null;
    const css = getComputedStyle(document.documentElement);
    const warna = { selesai: css.getPropertyValue('--brand-green-deep').trim(), pendaftaran: css.getPropertyValue('--brand-blue').trim() };
    return {
        seri: 'selesai',
        rentang: '90',
        get potong() { return -Number(this.rentang); },
        get total() { return data[this.seri].slice(this.potong).reduce((a, b) => a + b, 0).toLocaleString('id-ID'); },
        async mulai() {
            const Chart = await window.muatChart();
            Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
            chart = new Chart(this.$refs.kanvas, {
                type: 'bar',
                data: { labels: [], datasets: [{ data: [], borderRadius: { topLeft: 4, topRight: 4 }, borderSkipped: 'bottom', maxBarThickness: 32, barPercentage: 0.8 }] },
                options: {
                    responsive: true, maintainAspectRatio: false, animation: { duration: 250 },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#171A4D', titleColor: '#ffffff', bodyColor: 'rgba(255,255,255,.75)',
                            padding: 10, cornerRadius: 8, displayColors: false,
                            callbacks: { label: c => c.parsed.y + (this.seri === 'selesai' ? ' tes selesai' : ' pendaftaran') },
                        },
                    },
                    scales: {
                        x: { grid: { display: false }, border: { display: false }, ticks: { color: '#737373', font: { size: 11 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                        y: { beginAtZero: true, border: { display: false }, grid: { color: '#f0f0f0' }, ticks: { color: '#737373', font: { size: 11 }, precision: 0 } },
                    },
                },
            });
            this.perbarui();
            this.$watch('seri', () => this.perbarui());
            this.$watch('rentang', () => this.perbarui());
        },
        perbarui() {
            const ds = chart.data.datasets[0];
            chart.data.labels = data.label.slice(this.potong);
            ds.data = data[this.seri].slice(this.potong);
            ds.backgroundColor = warna[this.seri];
            ds.hoverBackgroundColor = warna[this.seri] + 'cc';
            chart.update();
        },
    };
}
</script>
@endsection

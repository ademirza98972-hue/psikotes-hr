@extends('layouts.peserta', ['judulHalaman' => 'Panduan IST — ' . $kodeSubtes])

@php
$info = [
    'SE' => [
        'judul'   => 'SE — Melengkapi Kalimat',
        'durasi'  => '7 menit',
        'jumlah'  => '20 soal',
        'icon'    => 'format_quote',
        'warna'   => 'blue',
        'instruksi' => 'Setiap soal berupa kalimat yang belum selesai. Satu kata hilang di bagian akhir. Pilih <strong>satu kata</strong> dari 5 pilihan yang paling tepat melengkapi kalimat itu.',
        'catatan' => 'Pilih kata yang membuat kalimat paling logis dan bermakna.',
        'contoh'  => [
            [
                'soal'    => 'Seekor kuda mempunyai kesamaan terbanyak dengan seekor …',
                'pilihan' => ['kucing', 'bajing', 'keledai', 'lembu', 'anjing'],
                'kunci'   => 2,
                'alasan'  => 'Keledai paling mirip dengan kuda — sama-sama hewan tunggangan berbadan besar.',
            ],
            [
                'soal'    => 'Lawannya "harapan" ialah …',
                'pilihan' => ['duka', 'putus asa', 'sengsara', 'cinta', 'benci'],
                'kunci'   => 1,
                'alasan'  => 'Putus asa adalah lawan dari harapan.',
            ],
        ],
    ],
    'WA' => [
        'judul'   => 'WA — Pilihan Kata',
        'durasi'  => '8 menit',
        'jumlah'  => '20 soal',
        'icon'    => 'search',
        'warna'   => 'violet',
        'instruksi' => 'Diberikan 5 kata. Empat kata memiliki kesamaan tertentu, sedangkan satu kata <strong>tidak memiliki kesamaan</strong> dengan keempat lainnya. Temukan kata yang berbeda itu.',
        'catatan' => 'Fokus pada sifat atau kategori yang dimiliki 4 kata — yang tidak masuk kategori itu adalah jawabannya.',
        'contoh'  => [
            [
                'soal'    => '',
                'pilihan' => ['meja', 'kursi', 'burung', 'lemari', 'tempat tidur'],
                'kunci'   => 2,
                'alasan'  => 'Meja, kursi, lemari, tempat tidur adalah perabot rumah. Burung bukan perabot.',
            ],
            [
                'soal'    => '',
                'pilihan' => ['duduk', 'berbaring', 'berdiri', 'berjalan', 'berjongkok'],
                'kunci'   => 3,
                'alasan'  => 'Duduk, berbaring, berdiri, berjongkok adalah posisi diam. Berjalan adalah bergerak.',
            ],
        ],
    ],
    'AN' => [
        'judul'   => 'AN — Analogi Kata',
        'durasi'  => '7 menit',
        'jumlah'  => '20 soal',
        'icon'    => 'compare_arrows',
        'warna'   => 'teal',
        'instruksi' => 'Diberikan tiga kata dengan pola <strong>A : B = C : ?</strong>. Kata pertama dan kedua memiliki hubungan tertentu. Temukan kata keempat yang memiliki hubungan <strong>sama</strong> dengan kata ketiga.',
        'catatan' => 'Tentukan dulu jenis hubungan antara kata pertama dan kedua, lalu terapkan pada kata ketiga.',
        'contoh'  => [
            [
                'soal'    => 'Hutan : pohon = Tembok : ?',
                'pilihan' => ['batu bata', 'rumah', 'semen', 'putih', 'dinding'],
                'kunci'   => 0,
                'alasan'  => 'Hutan terdiri dari pohon, maka tembok terdiri dari batu bata.',
            ],
            [
                'soal'    => 'Gelap : terang = Basah : ?',
                'pilihan' => ['hujan', 'hari', 'lembab', 'angin', 'kering'],
                'kunci'   => 4,
                'alasan'  => 'Gelap adalah lawan terang, maka basah lawannya kering.',
            ],
        ],
    ],
    'GE' => [
        'judul'   => 'GE — Persamaan',
        'durasi'  => '8 menit',
        'jumlah'  => '16 soal',
        'icon'    => 'join',
        'warna'   => 'amber',
        'instruksi' => 'Diberikan dua kata. Ketik <strong>satu kata</strong> yang mencakup pengertian kedua kata tersebut.',
        'catatan' => 'Cari kata yang paling umum (superordinat) yang mencakup keduanya. Jawab singkat — satu kata.',
        'contoh'  => [
            [
                'soal'    => 'Ayam — Itik',
                'pilihan' => [],
                'kunci'   => -1,
                'alasan'  => 'Jawaban: <strong>Burung</strong> — ayam dan itik keduanya termasuk burung (unggas).',
            ],
            [
                'soal'    => 'Gaun — Celana',
                'pilihan' => [],
                'kunci'   => -1,
                'alasan'  => 'Jawaban: <strong>Pakaian</strong> — gaun dan celana keduanya adalah pakaian.',
            ],
        ],
    ],
    'RA' => [
        'judul'   => 'RA — Soal Hitung',
        'durasi'  => '10 menit',
        'jumlah'  => '20 soal',
        'icon'    => 'calculate',
        'warna'   => 'orange',
        'instruksi' => 'Soal matematika berbentuk cerita. Hitung jawabannya, lalu masukkan <strong>digit-digit angka</strong> yang ada dalam jawaban itu.',
        'catatan' => 'Urutan digit tidak perlu diperhatikan — cukup centang semua digit yang muncul dalam jawaban.',
        'contoh'  => [
            [
                'soal'    => 'Sebatang pensil harganya Rp 25. Berapakah harga 3 batang?',
                'pilihan' => [],
                'kunci'   => -1,
                'alasan'  => 'Jawaban: <strong>75</strong> — centang digit 7 dan 5.',
            ],
            [
                'soal'    => 'Husin bersepeda 15 km dalam 1 jam. Berapa km dalam 4 jam?',
                'pilihan' => [],
                'kunci'   => -1,
                'alasan'  => 'Jawaban: <strong>60</strong> — centang digit 6 dan 0.',
            ],
        ],
    ],
    'ZR' => [
        'judul'   => 'ZR — Deret Angka',
        'durasi'  => '10 menit',
        'jumlah'  => '20 soal',
        'icon'    => 'trending_up',
        'warna'   => 'indigo',
        'instruksi' => 'Setiap soal berupa deret angka yang mengikuti pola tertentu. Temukan <strong>angka berikutnya</strong> yang melanjutkan pola deret.',
        'catatan' => 'Pola bisa berupa penjumlahan, pengurangan, perkalian, pembagian, atau kombinasinya.',
        'contoh'  => [
            [
                'soal'    => '2  4  6  8  10  12  14  ?',
                'pilihan' => [],
                'kunci'   => -1,
                'alasan'  => 'Jawaban: <strong>16</strong> — setiap angka ditambah 2.',
            ],
            [
                'soal'    => '9  7  10  8  11  9  12  ?',
                'pilihan' => [],
                'kunci'   => -1,
                'alasan'  => 'Jawaban: <strong>10</strong> — bergantian dikurangi 2 lalu ditambah 3.',
            ],
        ],
    ],
    'FA' => [
        'judul'   => 'FA — Pilihan Gambar',
        'durasi'  => '7 menit',
        'jumlah'  => '20 soal',
        'icon'    => 'shapes',
        'warna'   => 'rose',
        'instruksi' => 'Setiap soal menampilkan beberapa <strong>potongan bentuk</strong>. Dari 5 pilihan, temukan bentuk utuh yang bisa dibentuk dengan menyusun semua potongan itu — tanpa sisa dan tanpa ruang kosong.',
        'catatan' => 'Potongan boleh diputar, tapi tidak boleh dibalik (mirror). Bayangkan potongan disusun dalam pikiran.',
        'contoh'  => [],
    ],
    'WU' => [
        'judul'   => 'WU — Soal Kubus',
        'durasi'  => '9 menit',
        'jumlah'  => '20 soal',
        'icon'    => 'view_in_ar',
        'warna'   => 'emerald',
        'instruksi' => 'Diberikan 5 kubus (a–e) dengan tanda berbeda di setiap sisi. Setiap soal menampilkan <strong>salah satu kubus</strong> dalam posisi berbeda. Tentukan kubus mana yang dimaksud.',
        'catatan' => 'Kubus bisa diputar atau digulingkan ke segala arah — perhatikan kombinasi tanda yang terlihat pada 3 sisi yang tampak.',
        'contoh'  => [],
    ],
];

$d     = $info[$kodeSubtes];
$warna = $d['warna'];
// Gunakan inline hex agar Tailwind JIT tidak perlu compile class dinamis
$warnaCss = match($warna) {
    'blue'   => ['accent' => '#1d4ed8', 'bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#bfdbfe'],
    'violet' => ['accent' => '#6d28d9', 'bg' => '#f5f3ff', 'text' => '#6d28d9', 'border' => '#ddd6fe'],
    'teal'   => ['accent' => '#0f766e', 'bg' => '#f0fdfa', 'text' => '#0f766e', 'border' => '#99f6e4'],
    'amber'  => ['accent' => '#b45309', 'bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#fde68a'],
    'orange' => ['accent' => '#c2410c', 'bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#fed7aa'],
    'indigo' => ['accent' => '#3730a3', 'bg' => '#eef2ff', 'text' => '#3730a3', 'border' => '#c7d2fe'],
    'rose'   => ['accent' => '#be123c', 'bg' => '#fff1f2', 'text' => '#be123c', 'border' => '#fecdd3'],
    'emerald'=> ['accent' => '#065f46', 'bg' => '#ecfdf5', 'text' => '#065f46', 'border' => '#a7f3d0'],
    default  => ['accent' => '#374151', 'bg' => '#f9fafb', 'text' => '#374151', 'border' => '#e5e7eb'],
};
@endphp

@section('content')
@php
$accent = $warnaCss['accent'];
$accentBg = $warnaCss['bg'];
$accentText = $warnaCss['text'];
$accentBorder = $warnaCss['border'];
@endphp
<div class="max-w-2xl mx-auto pb-8 space-y-5">

    {{-- Header --}}
    <div>
        <div class="inline-flex items-center gap-2 rounded-lg px-3 py-1.5 mb-3"
             style="background:{{ $accentBg }};color:{{ $accentText }};border:1px solid {{ $accentBorder }}">
            <span class="material-symbols-outlined text-[14px]">{{ $d['icon'] }}</span>
            <span class="text-[11px] font-bold uppercase tracking-wider">IST — Subtes {{ $kodeSubtes }}</span>
        </div>
        <h2 class="text-[22px] font-semibold text-[#00303c]">{{ $d['judul'] }}</h2>
        <div class="flex items-center gap-3 mt-2">
            <span class="inline-flex items-center gap-1 text-[12px] text-[#40484b]">
                <span class="material-symbols-outlined text-[14px]">schedule</span>
                {{ $d['durasi'] }}
            </span>
            <span class="text-[#c0c8cb]">·</span>
            <span class="inline-flex items-center gap-1 text-[12px] text-[#40484b]">
                <span class="material-symbols-outlined text-[14px]">quiz</span>
                {{ $d['jumlah'] }}
            </span>
        </div>
    </div>

    {{-- Instruksi --}}
    <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
        <div class="border-b border-[#e0e3e5] px-6 py-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Cara Pengerjaan</p>
        </div>
        <div class="px-6 py-5 space-y-3">
            <p class="text-[14px] text-[#191c1e] leading-relaxed">{!! $d['instruksi'] !!}</p>
            @if($d['catatan'])
            <div class="rounded-lg px-4 py-3 flex items-start gap-2"
                 style="background:{{ $accentBg }};border:1px solid {{ $accentBorder }}">
                <span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5"
                      style="color:{{ $accentText }}">lightbulb</span>
                <p class="text-[13px]" style="color:{{ $accentText }}">{{ $d['catatan'] }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Contoh --}}
    @if(count($d['contoh']) > 0)
    <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
        <div class="border-b border-[#e0e3e5] px-6 py-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Contoh</p>
        </div>
        <div class="divide-y divide-[#e0e3e5]">
            @foreach($d['contoh'] as $i => $contoh)
            <div class="px-6 py-5">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-[#71787c] mb-3">Contoh {{ $i + 1 }}</p>

                @if($contoh['soal'])
                <p class="text-[15px] font-medium text-[#191c1e] mb-4">{{ $contoh['soal'] }}</p>
                @endif

                @if(count($contoh['pilihan']) > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($contoh['pilihan'] as $j => $pil)
                    @if($j === $contoh['kunci'])
                    <span class="rounded-lg border px-3 py-1.5 text-sm font-semibold inline-flex items-center gap-1"
                          style="background:{{ $accentBg }};color:{{ $accentText }};border-color:{{ $accentBorder }}">
                        {{ chr(97 + $j) }}) {{ $pil }}
                        <span class="material-symbols-outlined text-[12px]">check</span>
                    </span>
                    @else
                    <span class="rounded-lg border border-[#e0e3e5] text-[#40484b] px-3 py-1.5 text-sm">
                        {{ chr(97 + $j) }}) {{ $pil }}
                    </span>
                    @endif
                    @endforeach
                </div>
                @endif

                <div class="rounded-lg bg-[#f7f9fb] border border-[#e0e3e5] px-4 py-3 flex items-start gap-2">
                    <span class="material-symbols-outlined text-[15px] text-[#2C5F6F] shrink-0 mt-0.5">info</span>
                    <p class="text-[13px] text-[#40484b] leading-relaxed">{!! $contoh['alasan'] !!}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tombol Mulai --}}
    <form method="POST" action="{{ route('peserta.tes.panduan-ist-mulai', [$sesiId, $kodeSubtes]) }}">
        @csrf
        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3.5 text-[15px] font-semibold text-white shadow-sm transition-all active:scale-95"
                style="background:{{ $accent }}">
            <span class="material-symbols-outlined text-[18px]">play_arrow</span>
            Mulai Subtes {{ $kodeSubtes }}
        </button>
        <p class="mt-2 text-center text-[11px] text-[#71787c]">
            Timer {{ $d['durasi'] }} dimulai setelah tombol ini ditekan
        </p>
    </form>

</div>
@endsection

{{--
    Ringkasan evaluasi (disusun otomatis dari skor peserta), kesimpulan HR, dan tanda tangan.
    Berbasis tabel agar tampil sama di layar, cetak browser, dan dompdf.
    Butuh: $hasilTes (ringkasan, catatan_hr, pemeriksa, tanggal_cetak), $tglId.
--}}
@php
    $_kotak = 'border:1px solid #000;padding:8px 10px;font-size:10.5px;line-height:1.65;text-align:justify;';
    $_judul = 'font-weight:700;font-size:11.5px;margin:16px 0 6px;letter-spacing:0.5px;text-transform:uppercase;';
@endphp

<div style="{{ $_judul }}">Ringkasan Evaluasi Psikologis</div>
<table style="width:100%;border-collapse:collapse;">
    <tr>
        <td style="{{ $_kotak }}">
            @forelse ($hasilTes['ringkasan'] ?? [] as $_kalimat)
                <div style="margin-bottom:4px;">&bull; {{ $_kalimat }}</div>
            @empty
                <span style="color:#888;">Belum ada skor yang dapat diringkas.</span>
            @endforelse
            <div style="margin-top:6px;font-size:8.5px;color:#777;font-style:italic;">
                Ringkasan disusun otomatis dari skor tes. Kesimpulan akhir ditetapkan oleh psikolog / HR.
            </div>
        </td>
    </tr>
</table>

<div style="{{ $_judul }}">Kesimpulan dan Rekomendasi HR</div>
<table style="width:100%;border-collapse:collapse;">
    <tr>
        <td style="{{ $_kotak }}min-height:50px;">
            @if (!empty($hasilTes['catatan_hr']))
                {!! nl2br(e($hasilTes['catatan_hr'])) !!}
            @else
                <span style="color:#888;">Belum diisi. Tulis kesimpulan di bagian <em>Catatan &amp; Rekomendasi HR</em> pada halaman hasil tes.</span>
            @endif
        </td>
    </tr>
</table>

<table style="width:100%;border-collapse:collapse;margin-top:22px;font-size:11px;">
    <tr>
        <td style="border:none;width:50%;padding:0;vertical-align:top;">
            Diperiksa oleh,
            <div style="height:48px;"></div>
            <div style="border-top:1px solid #000;width:170px;padding-top:2px;">{{ $hasilTes['pemeriksa'] ?? '' }}</div>
        </td>
        <td style="border:none;padding:0;vertical-align:top;text-align:right;">
            <div style="display:inline-block;text-align:left;">
                Banjarbaru, {{ $tglId($hasilTes['tanggal_cetak'] ?? now()->toDateString()) }}<br>Mengetahui,
                <div style="height:34px;"></div>
                <div style="border-top:1px solid #000;width:170px;padding-top:2px;">Psikolog / HR Manager</div>
            </div>
        </td>
    </tr>
</table>

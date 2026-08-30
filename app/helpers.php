<?php

if (! function_exists('kode_badge_class')) {
    /**
     * Deterministic badge colour for an alat tes kode.
     * Same kode always gets the same colour; new kodes get one automatically.
     *
     * $style = 'default'  → bg-X-100 text-X-700           (alat-tes, bank-soal)
     * $style = 'soft'     → bg-X-50 text-X-700 border border-X-200  (penjadwalan)
     */
    function kode_badge_class(string $kode, string $style = 'default'): string
    {
        $default = [
            'bg-emerald-100 text-emerald-700',
            'bg-blue-100 text-blue-700',
            'bg-violet-100 text-violet-700',
            'bg-rose-100 text-rose-700',
            'bg-amber-100 text-amber-700',
            'bg-cyan-100 text-cyan-700',
            'bg-indigo-100 text-indigo-700',
            'bg-orange-100 text-orange-700',
            'bg-pink-100 text-pink-700',
            'bg-teal-100 text-teal-700',
        ];

        $soft = [
            'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'bg-blue-50 text-blue-700 border border-blue-200',
            'bg-violet-50 text-violet-700 border border-violet-200',
            'bg-rose-50 text-rose-700 border border-rose-200',
            'bg-amber-50 text-amber-700 border border-amber-200',
            'bg-cyan-50 text-cyan-700 border border-cyan-200',
            'bg-indigo-50 text-indigo-700 border border-indigo-200',
            'bg-orange-50 text-orange-700 border border-orange-200',
            'bg-pink-50 text-pink-700 border border-pink-200',
            'bg-teal-50 text-teal-700 border border-teal-200',
        ];

        $palette = $style === 'soft' ? $soft : $default;
        return $palette[abs(crc32($kode)) % count($palette)];
    }
}

@php
    $menu = [
        'Menu' => [
            ['peserta.dashboard', 'peserta.dashboard', 'dashboard', 'Dashboard'],
            ['peserta.panduan', 'peserta.panduan', 'menu_book', 'Panduan Tes'],
        ],
        'Akun' => [
            ['profil.index', 'profil.*', 'manage_accounts', 'Profil Saya'],
        ],
    ];
@endphp

@extends('layouts.panel', ['menu' => $menu, 'beranda' => 'peserta.dashboard', 'subjudul' => 'Portal Peserta'])

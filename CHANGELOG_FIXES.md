# CHANGELOG FIXES

Terbaru → terlama. Hanya tulis setelah terverifikasi.

---

### Fix #22 — Laporan PAPI: grafik radar JavaScript tidak ikut PDF, tanpa kontrol kelengkapan, 3 salinan kode

| Field | Detail |
|---|---|
| Tanggal | 2026-10-16 |
| File | partial baru `admin/hasil-tes/partials/papi.blade.php`, `app/helpers.php` (`faktor_papi()`), `app/Http/Controllers/Admin/HasilTesController.php` (hapus `buildPapScores` yang tak terpakai), view `admin/hasil-tes/{detail,print,pdf}` |
| Masalah | Grafik radar PAPI memakai Chart.js (JavaScript) → tidak muncul di PDF dompdf; laporan tidak memeriksa kelengkapan jawaban sehingga sesi rusak (mis. 82/90, 18/90) tetap tampil seolah normal; data faktor (nama, kode DB, kelompok) disalin di 3 view (layar 163 baris, cetak 144, pdf 34 + array global) |
| Akar | Tampilan dibuat per view tanpa komponen bersama; kontrol "Total Atas/Bawah harus 45" dari Papi Kostick.xls tidak diterapkan |
| Fix | Partial bersama: (A) tabel 20 faktor per aspek (Arah Kerja … Ketaatan) dengan skor, batang 0–9, interpretasi kolom Analisis Excel; (B) roda profil SVG 20 jari-jari skala 0–9 (tampil di layar, cetak, dan dompdf); (C) kontrol total atas (G L I T V S R D C E) & bawah (N A P X B O Z K F W) masing-masing 45 dengan peringatan bila tidak lengkap. Satu sumber data faktor `faktor_papi()` |
| Verifikasi | Sesi 4 user 10: 45/45 "Lengkap", Z=6 → "Mudah menyesuaikan diri", K=7 → "Agresi pribadi…" sesuai ambang Excel; skor faktor cocok dengan hitung ulang Python dari kunci Excel (Fix #17). 30 render (10 sesi × layar/cetak/pdf) tanpa error; teks PDF memuat tabel, label roda, dan "45 / 45". `php artisan test` 17/17 |
| Pelajaran | Kontrol kelengkapan yang ada di patokan (total 210 EPPS, 45/45 PAPI) wajib ikut tampil di laporan agar data rusak tidak terbaca sebagai hasil sah |
| Log Keyword | PAPI, roda profil, radar, Chart.js, dompdf, total atas bawah 45, faktor_papi, laporan |
| Deploy | `php artisan view:clear` |

**Revisi Fix #22 (2026-10-16, laporan user dari screenshot sesi 20 user 18):** roda profil terlihat seperti jarum tanpa struktur, dan faktor E = 10 (mustahil, maksimal 9; data rusak, lihat Fix #17) membuat batang keluar kolom dan titik menembus lingkaran. Roda dibuat ulang: 7 sektor aspek berwarna (isian tipis + pita tepi + nama aspek), cincin 1–9 (5 & 9 tegas), huruf faktor di tepi, nilai di setiap titik. Skor > 9 dibatasi di cincin terluar, ditandai merah, dan Kontrol Jawaban menyebut "Skor melebihi 9 pada faktor E = 10 — data jawaban tidak valid". Verifikasi: screenshot sesi 20/18 (data rusak) & 4/10 (45/45, layar + cetak); teks PDF memuat nama aspek dan pesan kontrol; 33 render tanpa error; test 17/17.

---

### Fix #21 — Laporan IST tanpa skor RW/SW & profil, subtes WU salah label "Kecerdasan Umum"

| Field | Detail |
|---|---|
| Tanggal | 2026-10-16 |
| File | partial baru `admin/hasil-tes/partials/ist.blade.php`, `app/helpers.php` (`subtes_ist()`), `app/Http/Controllers/Admin/HasilTesController.php` (`kelompok_norma`, nama aspek ringkasan), view `admin/hasil-tes/{detail,print,pdf}` |
| Masalah | Lampiran IST hanya centang SR/R/C/T/ST; RW/SW tersembunyi di bagian "Lihat skor detail" (layar) dan tidak ada di cetak/PDF; tidak ada profil subtes; norma usia yang dipakai tidak disebut. Label aspek buatan: WU (Würfelaufgaben = soal kubus/spasial) disebut "Kecerdasan Umum", FA disebut "Daya Kreativitas", ZR "Berpikir Praktis". Kesimpulan IST ganda dengan Ringkasan Evaluasi |
| Akar | Psikogram IST meniru template perusahaan lama tanpa acuan nama subtes Amthauer; tiga view punya salinan blok IST sendiri (layar 156 baris, cetak 100, pdf 74) |
| Fix | Partial bersama: (A) tabel 9 subtes — kode + nama Jerman/Indonesia, aspek yang diukur + kelompok (Verbal/Numerik/Figural/Ingatan), RW, SW, kategori (patokan IST_Norma_Usia.xls), baris Jumlah RW → IQ → klasifikasi; (B) profil SW SVG (pita Sedang 95–104, garis rata-rata 100, titik berwarna per kategori); (C) tabel klasifikasi IQ dengan baris peserta disorot. Norma usia (mis. "usia 21–25 tahun") diambil dari `ScoringEngineService::kelompokSegmen()` pada tanggal tes. Nama aspek ringkasan diselaraskan (WU = kemampuan ruang) |
| Verifikasi | Sesi 1 user 7 (norma 21–25): SE RW3→SW76, WA 3→74, AN 3→83, GE 1→72, RA 11→105, ZR 13→108, FA 4→81, WU 5→86, ME 4→84, jumlah RW 47 → SW 82 → IQ 73 Borderline — semua cocok dengan tabel "USIA 21 - 25" & "Norma IQ" Sofware IST.xlsx. 27 render (9 sesi × layar/cetak/pdf) tanpa error, screenshot layar & cetak dicek, teks PDF memuat ketiga bagian. `php artisan test` 17/17 |
| Pelajaran | Nama & arti subtes psikotes harus mengikuti manual alat tesnya; label "versi perusahaan" boleh ditambahkan, bukan menggantikan |
| Log Keyword | IST, RW, SW, profil IST, taraf kecerdasan, klasifikasi IQ, WU kubus, norma usia, laporan |
| Deploy | `php artisan view:clear` |

---

### Fix #20 — Laporan Kraepelin mentah ala Excel (7 desimal, tanpa interpretasi), grafik tidak ikut PDF, regresi beda dengan skor

| Field | Detail |
|---|---|
| Tanggal | 2026-10-16 |
| File | `app/Http/Controllers/Admin/HasilTesController.php` (`buildGridExtra`, ringkasan Kraepelin), partial baru `admin/hasil-tes/partials/kraepelin.blade.php`, view `admin/hasil-tes/{detail,print,pdf}` |
| Masalah | Uji Kraepelin lengkap via browser (kandidat1, sesi 69, 50 kolom; kolom 1 dibiarkan habis waktu). Laporan menampilkan istilah Excel mentah ("speed factor", "ausdeur factor"), y(0) = 13.8987755, kategori tanpa penjelasan; grafik Chart.js (JavaScript) sehingga tidak muncul di PDF dompdf. `buildGridExtra` menghitung regresi hanya atas kolom tersimpan, berbeda dengan skor KETAHANAN (N = 50, kolom kosong = 0) bila ada kolom tak tersimpan |
| Akar | View meniru lembar Excel apa adanya; tiga view punya blok Grid sendiri-sendiri (detail 149 baris, print 120, pdf 88) |
| Fix | Partial bersama: (A) rekap skoring sesuai kotak SCORING lembar jawab (Mean/Panker, Range + kolom tertinggi/terendah, Av. Deviation/Janker, Sum of errors, Sum of skippeds, Tianker, Hanker = y(50) − y(0) + tren), angka 2 desimal format Indonesia; (B) grafik kerja SVG (titik per kolom + garis tren regresi) di `<img>` data-URI → tampil di layar, cetak, dan dompdf; (C) tabel interpretasi 4 faktor dengan teks per kategori. `buildGridExtra` memakai N = jumlah kolom soal & kolom kosong = 0 (sama dengan `scoreGrid`), tren menurun hanya bila selisih < 0 (Kraepelin.xls). Ringkasan: satuan jelas + peringatan jumlah kolom tanpa jawaban benar |
| Verifikasi | Sesi 69: 50 kolom, 755 benar, 43 salah, 0 terlampaui; 69 input "0" (63 benar — dulu dihitung terlewat, lihat Fix #17). Hitung ulang Python dari y per kolom: Panker 15,1 · Janker 1,22 · Hanker 2,36 · Range 18 = sama persis dengan laporan. 27 render (9 sesi × detail/print/pdf) tanpa error; teks PDF memuat "Kolom ke-" (SVG ter-render dompdf). Screenshot layar & cetak dicek. `php artisan test` 17/17 |
| Pelajaran | Grafik untuk dokumen cetak/PDF jangan bergantung JavaScript — SVG statis tampil sama di semua keluaran. Rumus tampilan harus memanggil definisi yang sama dengan rumus skor (N kolom) |
| Log Keyword | Kraepelin, Panker, Janker, Hanker, Tianker, grafik kerja, SVG, dompdf, regresi, buildGridExtra, laporan |
| Deploy | `php artisan view:clear` |

---

### Fix #19 — Laporan hasil tes berisi teks contoh & EPPS tanpa skor: format laporan standar

| Field | Detail |
|---|---|
| Tanggal | 2026-10-16 |
| File | `app/Http/Controllers/Admin/HasilTesController.php` (`susunRingkasan`, identitas), `app/Services/FormatHasilEppsService.php` (`NAMA_KEBUTUHAN`, `INTERPRETASI`), partial baru `admin/hasil-tes/partials/{epps,ringkasan}.blade.php`, view `admin/hasil-tes/{detail,print,pdf}` |
| Masalah | Uji EPPS lengkap via browser (kandidat1, sesi 58): laporan menulis "Rekrutmen Staff Finance Batch 1", tanggal TTD "28 Juli 2026", dan **Ringkasan Evaluasi yang sama untuk semua peserta** ("taraf kecerdasan umum baik…") padahal peserta hanya tes EPPS. Posisi/Departemen selalu "—". Tabel EPPS hanya centang tanpa RS/persentil, nama kebutuhan asli tidak tampil, tanpa grafik profil. Seksi PSIKOGRAM kosong tetap tampil. Di halaman cetak, seksi EPPS **tidak pernah muncul** (`nama_alat_tes === 'EPPS'`, padahal namanya "EPPS (Edwards…)"). `catatan_hr` dicetak dengan `<br />` mentah |
| Akar | View dibuat dari mockup dengan teks statis; tiga view (layar/cetak/pdf) menyalin array & markup sendiri-sendiri sehingga berbeda isi |
| Fix | Identitas dari data (posisi & departemen dari profil, tujuan tes per jenis peserta, pemeriksa = admin login, tanggal cetak = hari ini). Ringkasan disusun dari skor tiap alat tes (EPPS: kebutuhan tinggi/rendah + konsistensi; IST: IQ, klasifikasi, aspek kuat/lemah; Kraepelin: 4 faktor; PAPI: 3 faktor tertinggi) + kontrol kelengkapan dari Excel (EPPS total 210, PAPI total 90). Partial EPPS dipakai 3 view: tabel No/Aspek (nama asli)/RS/Persentil/Kategori/Interpretasi, grafik profil persentil dengan pita Sedang 17–84, kotak validitas (konsistensi x/15 + kontrol 210). Partial ringkasan: ringkasan, kesimpulan dari Catatan HR, tanda tangan. PSIKOGRAM hanya tampil bila ada isinya |
| Verifikasi | 18 render (6 peserta × detail/print/pdf) tanpa error. Sesi 58: "EPPS: kebutuhan menonjol pada Kreativitas… Konsistensi 7 dari 15 (Sangat Rendah)", tabel 15 baris, kontrol "210 dari 210". Sesi 10 user 31 (4 jawaban): "hanya 4 dari 210 soal terskor — hasil belum dapat ditafsirkan". Sesi 22 user 18 (data rusak Fix #17): "PAPI hanya 82 dari 90". Screenshot layar & cetak dicek; teks PDF dompdf memuat semua seksi. `php artisan test` 17/17 |
| Pelajaran | Teks laporan psikologi tidak boleh statis — narasi yang sama untuk semua peserta menyesatkan. Satu komponen dipakai bersama oleh layar/cetak/pdf agar tidak berbeda |
| Log Keyword | laporan hasil tes, ringkasan evaluasi, teks contoh, EPPS persentil, grafik profil, validitas, print EPPS tidak muncul, partial |
| Deploy | `php artisan view:clear` |

---

### Fix #18 — Peserta bisa membuka Hasil Tes, Alat Tes, Bank Soal & Penjadwalan admin

| Field | Detail |
|---|---|
| Tanggal | 2026-10-16 |
| File | `routes/web.php`, `app/Http/Middleware/CekIzin.php`, `tests/Feature/SmokeHalamanTest.php` |
| Masalah | Login sebagai kandidat (kandidat1@test.com) lalu buka `/admin/hasil-tes/1/7` → laporan hasil psikotes peserta lain tampil penuh. Sama untuk `/admin/alat-tes`, `/admin/bank-soal` (kunci jawaban!), `/admin/penjadwalan-tes`, `/admin/aktivitas` |
| Akar | Grup rute tersebut hanya dibungkus `auth`, tanpa middleware `izin:` — padahal kode izin `hasil_tes.lihat` & `soal.lihat` sudah ada dan dipakai menu sidebar (menu tersembunyi, URL tetap terbuka) |
| Fix | `izin:hasil_tes.lihat` di grup hasil-tes; `izin:soal.lihat,kategori_tes.kelola` di alat-tes/bank-soal/penjadwalan; `izin:dashboard.lihat` di aktivitas. `CekIzin` menerima beberapa izin (cukup salah satu, sama dengan aturan sidebar) |
| Verifikasi | Browser: kandidat → `/admin/hasil-tes/1/7` & `/admin/penjadwalan-tes` = 403 "Anda tidak punya akses"; super admin tetap bisa buka hasil sesi 58. Test baru `test_peserta_ditolak_dari_semua_halaman_admin` (semua GET `admin/*` + 3 URL berparameter); `php artisan test` 17/17 |
| Pelajaran | Menyembunyikan menu ≠ mengamankan halaman. Setiap grup rute admin wajib punya middleware izin; test smoke peserta-vs-admin menjaga ini |
| Log Keyword | 403, izin, CekIzin, hasil_tes.lihat, soal.lihat, kandidat buka admin, akses tanpa izin |
| Deploy | `php artisan route:clear` (bila route di-cache) |

---

### Fix #17 — Skoring tidak sesuai patokan "4 ALAT TES.zip": kunci EPPS buatan, IQ IST belum dikonversi, Kraepelin "0" = terlewat, jawaban tersimpan ke soal salah

| Field | Detail |
|---|---|
| Tanggal | 2026-10-16 |
| File | `app/Services/ScoringEngineService.php`, `app/Http/Controllers/PengerjaanTesController.php`, `app/helpers.php`, `routes/console.php` (cmd `psikotes:hitung-ulang`), `database/migrations/2026_10_16_000001_koreksi_skoring_sesuai_patokan.php`, seeder `EppsSoalSeeder`/`EppsSeeder`/`EppsNormaSeeder`/`EppsLevelSeeder`/`ISTSeeder`/`KraepelinLevelSeeder`, `data/norma_epps_full.csv`, view `admin/hasil-tes/{detail,print,pdf}`, `peserta/pengerjaan-soal`, hapus `EppsKonsistensiSoalSeeder` & `EppsSimulasiKonsistensiSeeder`, test `SkoringPatokanTest` |
| Masalah | **EPPS**: 221/225 item dipetakan ke kebutuhan yang salah (soal 1 "menolong teman" dihitung *ach*); skor konsistensi tidak pernah dihitung; kategori 0-10/11-30/31-70/71-90/91-100; soal 7–12 punya 4 opsi (2 asli + "Pilihan A/B" tanpa bobot); kebutuhan skor 0 tidak tersimpan; nama int/suc salah (Introjection/Success). **IST**: dimensi IQ berisi SW total (mis. 132) bukan IQ (148); kunci 18/93/96 salah; 6 kunci GE menyimpang dari "Kunci Soal 4"; 3 sel norma salah ketik & WA usia 51–60 RW 19–20 kosong → SW 0; klasifikasi IQ & kategori subtes beda dari patokan. **Kraepelin**: jawaban "0" (4+6) dihitung terlewat & tidak dihitung salah; pita level kecepatan berlubang (8.5 → tanpa kategori); pembagi = jumlah kolom tersimpan, bukan 50. **PAPI**: interpretasi cetak memakai skala R/K/C/B/T & teks karangan; data radar di print tidak sesuai urutan label. **Simpan jawaban**: 90/90 jawaban PAPI user 18 sesi 21 (dan 11 lain) tersimpan dengan opsi milik soal lain |
| Akar | Kunci EPPS di seeder dibuat mekanis (loop i×j), bukan dari EPPS.xls; `dispatch()` tidak memanggil `scoreConsistency`; seeder konsistensi lama menimpa soal 7–12; tahap "Norma IQ" (SW→IQ) di Sofware IST.xlsx tidak diimplementasikan; query `jawaban_peserta = 0` dianggap kosong; `jawab()` memakai soal dari `current_step` session tanpa memastikan opsi yang dikirim milik soal itu (klik ganda/back/2 tab menggeser langkah) |
| Fix | Kunci EPPS = EPPS.xls (`EppsSoalSeeder::KUNCI`, 15 soal diagonal tak diskor, total 210) + 15 pasangan konsistensi (`duplikat_dari_soal_id`), con dihitung di `dispatch`; kategori persentil ≤3/≤16/≤84/≤96; typo het 544→54. IST: `normaTerdekat()` 2 tahap (JUMLAH per usia → "Norma IQ"), kunci 18=e, 93=24, 96=3 (diselesaikan dari isi soal), `ISTSeeder::KUNCI_GE` sesuai Kunci Soal 4 + variasi ejaan IST_Norma_Usia.xls, typo norma AN/GE diperbaiki, WA 51–60 RW19–20=135; helper `klasifikasi_iq()`/`kategori_subtes_ist()`. Kraepelin: rumus Kraepelin.xls (N=50, kolom kosong y=0), salah = `is_benar=0`, terlampaui = `MAX(baris)-COUNT`. PAPI: `interpretasi_papi()` dari kolom Analisis Papi Kostick.xls. `jawab()` menolak `soal_id`/opsi yang tidak sesuai soal aktif |
| Verifikasi | Skrip Python menghitung ulang semua hasil langsung dari rumus/tabel Excel: EPPS 80/80, IST 30/30, Kraepelin 28/28 cocok; PAPI 167/200 (33 sisanya = 3 sesi user 18 yang jawabannya sudah rusak tersimpan, tidak bisa dipulihkan). Kunci di DB vs Excel: EPPS 0 selisih, PAPI 0 selisih. `psikotes:hitung-ulang`: 172 kolom Kraepelin direkap, 25 hasil dihitung ulang, 0 gagal; berubah EPPS 45/80, PAPI 45/270, KRP 21/28, IST 20/30. `php artisan test` 16/16. Halaman detail/print/pdf dirender tanpa error |
| Pelajaran | Kunci & norma psikotes wajib diambil dari file patokan lalu dicek otomatis (tes `SkoringPatokanTest`), jangan digenerate. Bila sumber patokan saling beda, pilih yang terbukti benar dari isi soal. Server tidak boleh percaya halaman yang dikirim klien sinkron dengan session |
| Log Keyword | EPPS kunci, konsistensi con, persentil, IST IQ, Norma IQ, SW, GE, Kraepelin Panker Janker Hanker Tianker, terlampaui, PAPI interpretasi, opsi soal lain, current_step, hitung-ulang |
| Deploy | Backup DB → `php artisan migrate` → `php artisan psikotes:hitung-ulang` → `php artisan view:clear`. Backup lokal: `storage/app/backup_sebelum_koreksi_skoring_2026-10-16.sql` |

---

### Fix #16 — Portal peserta: sidebar gelap beda sendiri & tidak responsif di HP

| Field | Detail |
|---|---|
| Tanggal | 2026-10-12 |
| File | `layouts/panel.blade.php` (baru), `layouts/admin.blade.php`, `layouts/peserta.blade.php`, `peserta/dashboard.blade.php`, `tests/Feature/SmokeHalamanTest.php` |
| Masalah | (1) Sidebar peserta gelap (CSS kustom ~150 baris) beda dari admin. (2) Di HP sidebar 280px selalu tampil dan memakan layar, tanpa tombol buka/tutup. (3) SweetAlert dimuat dari CDN. (4) 3 kartu statistik dashboard peserta terpotong di 375px ("Total Ditugas…") |
| Akar | Dua layout ditulis terpisah; admin sudah pindah ke shadcn (Fix #8–#11), peserta tidak ikut. Grid `grid-cols-3` tanpa breakpoint |
| Fix | Kerangka bersama `layouts.panel` (sidebar, topbar, notifikasi, flash); `admin`/`peserta` hanya menyusun menu lalu `@extends('layouts.panel')`. Peserta dapat drawer mobile + collapse desktop; SweetAlert CDN diganti `x-ui.notifikasi` (shim `window.Swal` menangani 4 dialog di halaman pengerjaan tes). Mode fokus (halaman tes) tetap tanpa sidebar, topbar menampilkan logo + judul. Grid kartu `grid-cols-1 sm:grid-cols-3` |
| Verifikasi | `php artisan test` 14/14 (tes baru: dashboard/panduan/profil peserta render dengan `sb-item`). Browser: 1440px sidebar identik admin, collapse tersimpan; 375px sidebar tersembunyi, drawer terbuka via tombol, scrollWidth 375 = innerWidth; `Swal.fire(...)` buka dialog "Oke" → `isConfirmed: true`, 0 script SweetAlert; dashboard admin tetap sama (badge Data Terhapus 11) |
| Pelajaran | Layout yang mirip harus berbagi satu kerangka — kalau tidak, satu sisi tertinggal saat redesain |
| Log Keyword | layout peserta, sidebar, panel, mobile, drawer, SweetAlert, Swal shim, modeFokus |
| Deploy | `npm run build && php artisan view:clear` |

---

### Fix #15 — Dashboard admin: status peserta menyesatkan, tombol verifikasi 403 untuk HR Viewer, warna teal sisa

| Field | Detail |
|---|---|
| Tanggal | 2026-10-12 |
| File | `DashboardController.php`, `admin/dashboard.blade.php`, `AppServiceProvider.php`, `resources/js/app.js`, `package.json`, `resources/css/app.css`, 59 view (recolor), `tests/Feature/DashboardAdminTest.php` |
| Masalah | (1) Saat 0 sesi berjalan, dashboard menampilkan "Sedang Mengerjakan 3", "Belum mulai 26 (48%)", deskripsi "sesi tes aktif"; peserta sesi Draft ikut terhitung; grafik (25) ≠ KPI. (2) HR Viewer melihat tombol Setujui/Tolak & "Lihat kandidat lainnya" → klik 403. (3) Kartu "Data Karyawan Tanpa Akun" selalu 0. (4) 674 kode teal `#2C5F6F` di 42+ file tidak ikut tema hijau |
| Akar | (1) Status dihitung hanya dari `waktu_mulai`, tanpa melihat apakah sesinya masih berjalan; filter Draft tidak ada. (2) View tidak cek `pengguna.verifikasi`/`pengguna.lihat`. (3) Sejak Fix #1 akun karyawan dibuat otomatis → konsep usang. (4) Warna di-hardcode per halaman |
| Fix | (1) Status per sesi: berjalan = `status='Aktif' AND tanggal_selesai >= hari ini` → Sedang/Belum mulai; selain itu → **Tidak selesai** (kategori baru); Draft dikecualikan di KPI, grafik, hasil terbaru. (2) Tombol digating izin. (3) Diganti "Karyawan Nonaktif" (akun buatan HR, belum bisa login) + jumlah belum ganti password. (4) Skrip recolor: class `-[#2C5F6F]`→`-primary`, hover gelap→`-primary/90`, header tabel gelap→token `muted`. Juga: Chart.js CDN → npm (chunk lazy 196KB, offline-safe), `puppeteer` (tak terpakai) dihapus, locale Carbon `id` dipindah ke `AppServiceProvider` (global), N+1 kandidat menunggu, kartu tanpa izin bukan link `#` |
| Verifikasi | `DashboardAdminTest`: kode lama 0/2, baru 2/2. Total `php artisan test` 13/13. Data nyata: Selesai 23 · Sedang 0 · Belum 0 · Tidak selesai 16 (dulu 25/3/26), grafik 23 = KPI 23. Recolor 921 penggantian, sisa `#2C5F6F` = 0, smoke semua halaman admin tanpa 5xx |
| Pelajaran | Status "belum/sedang" tidak bermakna tanpa konteks periode sesi. Sembunyikan aksi yang tidak diizinkan, jangan biarkan berakhir 403. Penulisan file via Python di Windows bisa gagal `Errno 22` saat file dikunci server — cek isi file sesudahnya |
| Log Keyword | dashboard, status pengerjaan, tidak selesai, Draft, HR Viewer, pengguna.verifikasi, recolor, #2C5F6F, Chart.js, puppeteer, setLocale |
| Deploy | `npm install && npm run build && php artisan view:clear` |

---

### Fix #14 — Redesain halaman auth (hijau Jhonlin): `php artisan test` mati & pesan validasi register berbahasa Inggris

| Field | Detail |
|---|---|
| Tanggal | 2026-10-12 |
| File | `phpunit.xml`, `app/Http/Requests/RegisterRequest.php`, `resources/views/layouts/auth.blade.php`, `resources/views/auth/*.blade.php`, `components/ui/{input,select,alert,input-password}.blade.php`, `resources/css/app.css`, `admin/dashboard.blade.php` |
| Masalah | (1) `php artisan test` berhenti: `Test directory "tests/Unit" not found`. (2) Register NIK salah → "The nik KTP field must be 16 digits." (Inggris), pesan lain diawali huruf kecil |
| Akar | (1) **Regresi Fix #13**: `tests/Unit` dihapus tapi testsuite `Unit` di `phpunit.xml` tidak ikut dihapus. (2) `RegisterRequest::messages()` tidak punya terjemahan `digits/integer/exists/date` (APP_LOCALE=en) dan memakai `:attribute`, bukan `:Attribute` |
| Fix | (1) Hapus testsuite `Unit`. (2) Tambah 4 pesan + pakai `:Attribute`. Sekaligus: `--primary` shadcn → hijau Jhonlin `#00843D` (logo `#00A651` digelapkan, kontras putih 4.8:1 AA); 5 halaman auth ditulis ulang pakai `x-ui.*`; error field tampil inline (`aria-invalid`); grafik dashboard ambil warna dari `--primary` |
| Verifikasi | `php artisan test` 11/11 lulus. Browser 1440px & 375px: 5 halaman auth 200, tanpa scroll horizontal (scrollWidth 375 = innerWidth); submit register invalid → 8 pesan Indonesia, baris grid tetap sejajar (`items-start`); toggle password jalan; login admin akun uji → dashboard hijau |
| Pelajaran | Saat menghapus folder test, cek `phpunit.xml`. Atribut Alpine `:type` di tag `<x-ui.*>` dievaluasi Blade sebagai PHP — pakai `x-bind:type` |
| Log Keyword | phpunit, tests/Unit, digits, :Attribute, auth, login, register, hijau, primary |
| Deploy | `npm run build && php artisan view:clear` |

---

### Fix #13 — Audit kebersihan: seeder rusak, kolom redundan kandidat, kode & relasi mati

| Field | Detail |
|---|---|
| Tanggal | 2026-10-12 |
| File | `database/seeders/DummyDataSeeder.php`, `app/Models/DataKaryawan.php`, `app/Models/ProfilKandidat.php`, `DataKandidatController.php`, `HasilTesController.php`, `admin/data-kandidat/index.blade.php`, 8 model (relasi mati), `AlatTesController.php`, `DashboardController.php`, `PengerjaanTesController.php`, migration `2026_10_12_000001_bersihkan_kolom_dan_tabel_tidak_terpakai.php`, `tests/Feature/SmokeHalamanTest.php`, `tests/Feature/SeederCekTest.php` |
| Masalah | (1) `php artisan db:seed` gagal: `Unknown column 'nama_karyawan' in 'field list'`. (2) `profil_kandidat.departemen` (string nama) NULL untuk 9 dari 17 kandidat (semua yang daftar sendiri) → modal Ubah kandidat tidak memilih departemen; basi bila departemen di-rename. (3) Banyak kode tak terpakai |
| Akar | (1) Fix #1 memindahkan nama/departemen/jabatan ke `users`/FK, tapi `DummyDataSeeder` tetap menulis kolom lama; commit e9a6c32 malah menambah kolom hantu itu ke `$fillable` sehingga error pindah ke SQL. (2) Departemen disimpan dua kali (string + `posisi.departemen_id`), hanya alur admin yang mengisi string. (3) Sisa refactor |
| Fix | (1) Seeder buat karyawan seperti alur HR (`users` + `data_karyawan.user_id`, login via `id_karyawan`); `$fillable` dibersihkan. (2) Departemen kandidat diturunkan dari `profilKandidat.posisi.departemen`; kolom di-drop via migration. (3) Hapus method mati `AlatTesController::restore` (tanpa route; restore lewat Data Terhapus), `DashboardController::mulaiTes`, `PengerjaanTesController::getSesiPeserta`, 21 relasi Eloquent tak dipakai; drop tabel kosong `interpretasi_teks`, `standar_kompetensi_posisi` |
| Verifikasi | `php artisan test`: 12/12 lulus (baseline 10/10). `SeederCekTest` tanpa fix seeder 1/2, dengan fix 2/2 (dalam transaksi, DB asli tidak berubah — dicek 0 baris sisa). Smoke semua route GET `admin/*` tanpa 5xx; `php artisan view:cache` sukses. Relasi semua model divalidasi terhadap skema DB (kolom FK/pivot ada) |
| Pelajaran | Setelah ubah skema, jalankan `migrate:fresh --seed` — seeder ikut rusak diam-diam. Jangan tambah kolom ke `$fillable` untuk membungkam error. Cek pemakaian relasi lewat nama relasi (`with('x.y')`), bukan hanya nama kelas: `bidang_laporan` tampak tak terpakai padahal dipakai psikogram |
| Log Keyword | seeder, nama_karyawan, fillable, profil_kandidat.departemen, redundan, relasi mati, dead code |
| Deploy | `php artisan migrate` (drop kolom `profil_kandidat.departemen` + 2 tabel kosong) |

---

### Fix #12 — Login super admin membuka halaman peserta: precedence OR di query login peserta

| Field | Detail |
|---|---|
| Tanggal | 2026-10-11 |
| File | `app/Http/Controllers/AutentikasiController.php`, `tests/Feature/LoginPortalTest.php` (baru) |
| Masalah | Super admin login lewat `/login` (portal peserta) berhasil masuk dan diarahkan ke `/peserta/dashboard` |
| Akar | (1) `loginPeserta`: `where(email)->orWhere(id_karyawan)->whereIn(tipe_akun)` jadi SQL `email = ? OR (id_karyawan = ? AND tipe_akun IN …)` → filter tipe akun tidak berlaku untuk pencocokan email, akun `custom` (admin) lolos. (2) `loginAdmin`: `redirect()->intended()` memakai `url.intended` sisa akses `/peserta/...` sebelum login → admin dibuang ke halaman peserta |
| Fix | (1) Bungkus email/id_karyawan dalam closure `where(fn ($q) => …)`. (2) Buang `url.intended` sebelum redirect admin bila path bukan `/admin` |
| Verifikasi | `php artisan test --filter=LoginPortalTest`: tanpa fix 0/2 lulus (tidak ada error session; redirect `/peserta/dashboard`), dengan fix 2/2 lulus. Tes pakai `DatabaseTransactions` (password super admin di-rollback) |
| Pelajaran | Kombinasi `orWhere` + filter lain WAJIB dibungkus closure. `intended()` lintas portal harus difilter per prefix |
| Log Keyword | login, super admin, peserta, orWhere, precedence, intended, tipe_akun |
| Deploy | Tidak perlu build; cukup deploy file PHP |

---

### Fix #11 — Dashboard/notifikasi shadcn: konten keluar kartu & tombol Batal nyangkut

| Field | Detail |
|---|---|
| Tanggal | 2026-10-07 |
| File | `resources/views/admin/dashboard.blade.php`, `resources/views/components/ui/notifikasi.blade.php` (branch `ui-shadcn`) |
| Masalah | (1) Baris "Peringatan Integritas" melebar keluar kartu di desktop; di layar 390px seluruh halaman scroll horizontal (scrollWidth 551 > 390). (2) Dialog pemberitahuan (tanpa batal) yang dibuka tepat setelah dialog konfirmasi masih menampilkan tombol "Batal" |
| Akar | (1) Container `grid` tanpa `grid-cols-*` → track `auto` ikut lebar konten `truncate`/tabel `whitespace-nowrap`. (2) `x-show` pada tombol tidak sempat diterapkan saat dialog ditutup & dibuka di tick yang sama |
| Fix | (1) List → `flex flex-col`; grid responsif → `grid grid-cols-1 … xl:grid-cols-3` (minmax(0,1fr)). (2) Ganti `x-show` → `:hidden` (preflight Tailwind v4 `[hidden]{display:none!important}`) |
| Verifikasi | 0 elemen melewati batas kartu di 1440px; main 390/390 di mobile, tabel scroll di dalam kartu; urutan konfirmasi→info→konfirmasi: tombol `[Batal, Ya, Hapus]` → `[Mengerti]` → `[Batal, Ya]`; 18 halaman admin GET + 6 halaman detail render 200 di layout baru |
| Pelajaran | Setiap `grid` yang anaknya berisi teks panjang/tabel wajib `grid-cols-1` (atau flex-col). Untuk tampil/sembunyi yang dibuka-tutup cepat, pakai `:hidden` |
| Log Keyword | overflow, grid-cols-1, truncate, x-show, Batal, konfirmasi |
| Deploy | `npm run build && php artisan view:clear` |

---

### Fix #10 — Label grafik dashboard berbahasa Inggris

| Field | Detail |
|---|---|
| Tanggal | 2026-10-06 |
| File | `app/Http/Controllers/DashboardController.php` |
| Masalah | Sumbu grafik "Pendaftaran 7 Hari" tampil "Wed, 30 Sep … Tue, 06 Oct", padahal tanggal header sudah "Selasa, 6 Oktober 2026" |
| Akar | `Carbon::setLocale('id')` dipanggil setelah loop label (`translatedFormat('D, d M')`); `APP_LOCALE=en` jadi label memakai locale default |
| Fix | Pindahkan `Carbon::setLocale('id')` ke sebelum loop label |
| Verifikasi | Render /admin/dashboard: labels `["Rab, 30 Sep",…,"Sel, 06 Okt"]`, header tetap "Selamat malam, Super Admin" + tanggal Indonesia |
| Pelajaran | `setLocale` di tengah method hanya berlaku untuk baris sesudahnya — taruh di awal |
| Log Keyword | `translatedFormat`, `setLocale`, label grafik |
| Deploy | — |

---

### Fix #9 — Komponen UI shadcn: tombol Edit mati & override class tidak berlaku

| Field | Detail |
|---|---|
| Tanggal | 2026-10-06 |
| File | `resources/views/admin/alat-tes/index.blade.php`, `resources/views/components/ui/*` (branch `ui-shadcn`) |
| Masalah | (1) Klik Edit di tabel Alat Tes tidak membuka dialog. (2) `class="w-36"` / `whitespace-normal` yang dikirim ke `<x-ui.input>` / `<x-ui.td>` tidak berpengaruh → input tetap lebar penuh, tabel scroll horizontal (scrollWidth 1367 > 1110) |
| Akar | (1) `@js(...)` di dalam atribut komponen Blade (`<x-ui.button @click="...@js(..)">`) **tidak dikompilasi** — terkirim mentah ke browser → JS error. (2) `$attributes->class()` hanya menggabung class; `w-full` bawaan vs `w-36` → yang menang ditentukan urutan CSS, bukan urutan atribut |
| Fix | (1) Di atribut komponen pakai `{{ Js::from($x) }}`; `@js` hanya boleh di elemen HTML biasa. (2) Override class bawaan komponen pakai suffix important Tailwind v4: `w-36!`, `whitespace-normal!` |
| Verifikasi | Dialog Edit terbuka & terisi (kode IST, format Mixed, durasi 90, action `/admin/alat-tes/5`); tabel muat tanpa scroll di 1280px; Escape menutup dialog |
| Pelajaran | Saat migrasi halaman ke `<x-ui.*>`: grep `@js` di dalam tag `<x-` dan pakai `!` untuk menimpa class bawaan komponen |
| Log Keyword | `@js`, `x-ui`, Js::from, class override, `!` |
| Deploy | `npm run build && php artisan view:clear` |

---

### Fix #8 — Rollback migrasi admin Filament → kembali ke Tailwind

| Field | Detail |
|---|---|
| Tanggal | 2026-10-06 |
| File | Semua perubahan sejak commit `f96adf8` (app/Filament, composer.json/lock, routes/web.php, User.php, views filament, dll.) |
| Masalah | Migrasi panel admin ke Filament v3 memunculkan banyak regresi (Kelola Dimensi level kosong, Alpine tidak jalan, redirect ke `/livewire/update` 405, layout grid tidak ter-compile, login admin nonaktif lolos) dan UI tidak sesuai sistem lama |
| Akar | Fitur admin lama (Alpine + Blade custom) tidak bisa dipetakan 1:1 ke komponen Filament; tiap halaman custom perlu ditulis ulang |
| Fix | `git stash push -u` (stash: "backup: migrasi Filament") → working tree kembali ke `f96adf8`; `composer install` (vendor/filament terhapus); `.env` APP_LOCALE=en; `optimize:clear` |
| Verifikasi | `vendor/filament` tidak ada; 80 route `admin.*` kembali; `/login` 200, `/admin/login` 200 ("Login Admin"), halaman admin tanpa sesi → 302 ke `/login` |
| Pelajaran | Migrasi UI besar dikerjakan di branch terpisah dan di-commit bertahap, bukan langsung di `main` tanpa commit |
| Log Keyword | Filament, rollback, stash |
| Deploy | `composer install && php artisan optimize:clear`. Pulihkan Filament: `git stash apply stash@{0}` lalu `composer install` |

---

### Fix #7 — Randomisasi opsi jawaban A-E dihapus, hanya urutan soal yang diacak

| Field | Detail |
|---|---|
| Tanggal | 2026-09-02 |
| File | `PengerjaanTesController.php` |
| Masalah | Opsi jawaban A-E ikut diacak per peserta, padahal soal standar psikotes memiliki kunci jawaban tetap per posisi opsi |
| Akar | `getOrCreateOpsiOrder()` + blok shuffle opsi salah diimplementasikan dari awal |
| Fix | Hapus method `getOrCreateOpsiOrder()` dan seluruh blok randomisasi opsi; urutan soal tetap diacak |
| Verifikasi | Soal ditampilkan A-E sesuai urutan DB, urutan soal antar peserta berbeda |
| Pelajaran | Randomisasi soal ≠ randomisasi opsi; untuk tes psikologi terstandar, posisi opsi tidak boleh berubah |

---

### Fix #6 — Timer hafalan-ME reset ke 3:00 saat refresh halaman

| Field | Detail |
|---|---|
| Tanggal | 2026-09-02 |
| File | `resources/views/peserta/hafalan-me.blade.php` |
| Masalah | Timer selalu mulai dari 3:00 saat peserta refresh, seolah waktu hafalan belum berjalan |
| Akar | Timer hanya memakai `DURASI * 1000` setTimeout tanpa menyimpan kapan mulai; setiap load halaman dianggap fresh start |
| Fix | Simpan timestamp mulai ke `sessionStorage` dengan key `hafalan_me_start_{sesiId}`; sisa waktu dihitung dari `Date.now() - startTs`; jika waktu sudah habis saat load, langsung submit |
| Verifikasi | Refresh saat timer 1:30 → halaman reload, timer lanjut dari ~1:30 |
| Pelajaran | Timer berbasis `setTimeout` tidak survive reload; selalu pakai timestamp epoch + `sessionStorage` untuk timer yang harus persist |

---

### Fix #5 — Tombol "Lanjut" hafalan-ME tidak aktif + error 405 saat timer habis

| Field | Detail |
|---|---|
| Tanggal | 2026-09-02 |
| File | `resources/views/peserta/hafalan-me.blade.php`, `resources/views/peserta/pengerjaan-soal.blade.php` |
| Masalah | (1) Timer menunjukkan 0:00 tapi tombol "Lanjut ke Soal" tetap disabled dan tidak bisa diklik. (2) Setelah timer habis muncul error 405 Method Not Allowed |
| Akar | (1) `form.submit()` tidak memicu event `submit` → `_monitor_tes` AJAX handler tidak intercept → browser submit native → halaman navigasi keluar fullscreen. `setTimeout` untuk enable tombol terpicu bersamaan dengan timer habis sehingga racing condition. (2) `swapContent` di `navigatePost` tidak ada guard `!res.ok`; saat server return non-redirect (misal CSRF 419), `window.location.href` di-set ke POST URL yang sama → browser GET ke URL POST → 405 |
| Fix | Ganti `form.submit()` → `form.requestSubmit()` agar event submit terpicu dan `_monitor_tes` bisa intercept. Tambah guard `if (!res.ok) { window.location.reload(); return; }` di `navigatePost`. Tambah `data-panduan-form` ke form hafalan-ME agar `swapContent` mengenalinya sebagai panduan page |
| Verifikasi | Timer habis → form submit via AJAX → body swap tanpa keluar fullscreen. Tombol enable setelah waktu habis |
| Pelajaran | `form.submit()` dan `form.requestSubmit()` berbeda: yang pertama bypass event listener. Selalu pakai `requestSubmit()` jika ada handler di event `submit` |

---

### Fix #4 — Fullscreen keluar saat transisi subtes 8→9 (IST hafalan-ME)

| Field | Detail |
|---|---|
| Tanggal | 2026-09-02 |
| File | `resources/views/peserta/hafalan-me.blade.php`, `resources/views/peserta/pengerjaan-soal.blade.php` |
| Masalah | Berpindah dari subtes 8 (WU) ke subtes 9 (ME) menyebabkan browser keluar dari fullscreen |
| Akar | `swapContent` mendeteksi "panduan page" via atribut `data-panduan-form` pada form. `hafalan-me.blade.php` tidak memiliki atribut ini, sehingga `swapContent` tidak AJAX-swap melainkan navigasi biasa (`window.location.href`) yang me-reload halaman dan mengakhiri sesi fullscreen |
| Fix | Tambahkan `data-panduan-form` ke `<form id="form-lanjut">` di `hafalan-me.blade.php` |
| Verifikasi | Transisi WU → ME tetap dalam fullscreen; body swap via AJAX berjalan normal |
| Pelajaran | Setiap halaman interstitial (panduan/hafalan) yang diakses via AJAX swap harus memiliki `data-panduan-form` pada form submit-nya |

---

### Fix #3 — Sisa referensi `profilKaryawan` setelah refactor: 500 error di Hasil Tes & Pengerjaan Tes

| Field | Detail |
|---|---|
| Tanggal | 2026-08-29 |
| File | `HasilTesController.php`, `PengerjaanTesController.php`, `DummyHasilTesSeeder.php` |
| Masalah | `RelationNotFoundException: profilKaryawan` saat buka halaman Hasil Tes dan saat peserta mengerjakan tes IST |
| Akar | Relasi `profilKaryawan` (ke tabel `profil_karyawan`) sudah dihapus di Fix #1, tapi tiga file tidak ikut diupdate |
| Fix | Ganti semua `->profilKaryawan` → `->dataKaryawan`, update eager-load di HasilTesController |
| Verifikasi | Halaman `/admin/hasil-tes` terbuka, departemen & posisi peserta tampil benar |
| Pelajaran | Setelah hapus relasi Eloquent, wajib scan seluruh codebase (`grep profilKaryawan`) — jangan hanya update file yang sudah diketahui |
| Log Keyword | `RelationNotFoundException`, `profilKaryawan` |

---

### Fix #2 — Refactor `posisi_dilamar` varchar → FK `posisi_id` di profil_kandidat

| Field | Detail |
|---|---|
| Tanggal | 2026-08-29 |
| File | Migration, `ProfilKandidat.php`, semua view data-kandidat, `DataKandidatController`, `AutentikasiController`, `HasilTesController`, `DummyDataSeeder` |
| Masalah | Posisi kandidat disimpan sebagai teks (`posisi_dilamar = "Staff Keuangan"`), tidak konsisten dengan tabel `posisi` |
| Akar | Desain awal pakai string, bukan FK — rentan inkonsistensi kalau nama posisi berubah |
| Fix | Migration: tambah `posisi_id` FK, copy data via JOIN, hapus `posisi_dilamar`. Update semua controller & view untuk pakai relasi `posisi()` |
| Verifikasi | Filter posisi kandidat by ID bekerja, modal ubah auto-select posisi yang benar, display via `->posisi->nama_posisi` OK |
| Pelajaran | Hindari simpan nama entitas sebagai string kalau ada tabel master-nya — pakai FK sejak awal |
| Log Keyword | `posisi_dilamar`, `posisi_id` |

---

### Fix #1 — Refactor Double Database Karyawan: Hapus profil_karyawan, gabung Data + Akun Karyawan

| Field | Detail |
|---|---|
| **Tanggal** | 2026-08-29 |
| **Files** | migration `2026_08_29_000001_refactor_karyawan_schema.php`, `User.php`, `DataKaryawan.php`, `DataKaryawanController.php`, `AutentikasiController.php`, `ProfilController.php`, `DataTerhapusController.php`, `DataKaryawanImport.php`, `SimpanDataKaryawanRequest.php`, `PerbaruiDataKaryawanRequest.php`, `LoginPesertaRequest.php`, `PaksaGantiPassword.php` (new), `bootstrap/app.php`, `routes/web.php`, views: `admin/akun-karyawan/index.blade.php`, `profil/index.blade.php`, `auth/login.blade.php`, `admin/dashboard.blade.php`, `layouts/admin.blade.php` |
| **Masalah** | Pembimbing menemukan double database: `profil_karyawan` duplikasi data dari `data_karyawan`. Plus `data_karyawan` punya kolom string lama (departemen, jabatan) sekaligus FK baru (departemen_id, posisi_id). Menu "Data Karyawan" dan "Akun Karyawan" terpisah padahal satu entitas. |
| **Akar** | Arsitektur dua tahap (HR input data dulu, lalu karyawan daftar sendiri) menyebabkan profil_karyawan dibuat sebagai jembatan, padahal data_karyawan sudah ada. |
| **Fix** | Drop `profil_karyawan`. Tambah `user_id` FK ke `data_karyawan`. Tambah `id_karyawan` (format: tahun+urut, contoh 2026001) dan `must_change_password` ke `users`. Email jadi nullable. Login karyawan pakai `id_karyawan` bukan email. Auto-create user saat HR tambah karyawan. Hapus menu "Data Karyawan" di sidebar, gabung ke "Akun Karyawan". Password default = id_karyawan, wajib ganti saat login pertama. |
| **Verifikasi** | Migration berhasil (3 detik). `php artisan tinker` render view OK. Halaman `/admin/akun-karyawan` tampil dengan tabel ID Karyawan, NIK, nama, departemen/posisi, status. |
| **Pelajaran** | `orderByHas()` tidak ada di Laravel 13 — pakai subquery `orderBy(Model::select(...)->whereColumn(...)->limit(1))`. `tanggal_lahir` perlu cast `'date'` di model agar bisa `->format()`. Route lama yang direferensi di view lain (dashboard, sidebar) harus dicek setelah route group dihapus. |
| **Log Keyword** | `double database`, `profil_karyawan`, `id_karyawan`, `must_change_password`, `orderByHas` |
| **Deploy** | Local only, belum push |

---

### Fix #2 — Scoring IST salah: berbasis pendidikan, kunci jawaban keliru, GE tidak 0/1/2, norma tidak per usia

| Field | Detail |
|---|---|
| Tanggal | 2026-08-20 |
| File | `database/seeders/ISTSeeder.php`, `app/Services/ScoringEngineService.php`, `app/Http/Controllers/PengerjaanTesController.php`, `app/Models/ProfilKaryawan.php`, `app/Models/ProfilKandidat.php`, `database/migrations/2026_08_20_100000_add_tanggal_lahir_to_profil_tables.php` |
| Masalah | IST menggunakan norma berbasis pendidikan (SLTP/SLTA/SARJ) bukan usia. Kunci jawaban salah di 21+ soal. GE tidak menggunakan scoring 0/1/2 + NILAI KONVERSI GE. FA/WU tidak punya kunci jawaban. |
| Akar | File Excel referensi baru ditemukan dengan norma per usia (USIA_21_25 s.d. USIA_51_60) dan kunci jawaban yang berbeda dari implementasi lama. |
| Fix | (1) Migration tambah `tanggal_lahir` ke profil_karyawan dan profil_kandidat. (2) Norma seeder diganti sepenuhnya dengan 6 kelompok usia × 9 subtes + JUMLAH. (3) Kunci jawaban 21 soal diperbaiki dari Kunci Soal 1-3,7-9. (4) GE soal 61-76 diganti ke `isian_teks` dengan kunci JSON `{"2":[...],"1":[...]}`. (5) FA (117-136) dan WU (137-156) kunci jawaban ditambahkan. (6) `scoreIST` diperbarui: GE scoring 0/1/2 + `convertGeRawToEquivalent()`, IQ lookup langsung per usia. (7) `PengerjaanTesController` hitung usia dari `tanggal_lahir`, mapping ke kelompok segmen. |
| Verifikasi | `php artisan db:seed --class=ISTSeeder` sukses. Tinker check: soal 15=c, 91=120, 157=d, GE 61 tipe=isian_teks, JUMLAH norma USIA_21_25 SW=132. Norma records: 1240. |
| Pelajaran | IST norma berbasis usia, bukan pendidikan. GE subtes perlu scoring khusus 0/1/2 dengan NILAI KONVERSI GE (raw 0-32 → equivalent 1-20) sebelum lookup norma. |
| Log Keyword | IST, norma, usia, GE scoring, kunci jawaban |

---

### Fix #1 — Status "Belum Terpakai" tidak sinkron setelah restore akun karyawan

| Field | Detail |
|---|---|
| **Tanggal** | 2026-08-18 |
| **File** | `app/Http/Controllers/Admin/DataTerhapusController.php`, `app/Http/Controllers/Admin/PenggunaController.php` |
| **Gejala** | NIK yang sudah punya akun di Akun Karyawan tetap tampil "Belum Terpakai" di Data Karyawan |
| **Akar** | `DataTerhapusController::pulihkan()` hanya memanggil `$item->restore()` pada User, tanpa mengembalikan `DataKaryawan.status` ke `sudah_terpakai`. Status di-reset ke `belum_terpakai` saat hapus akun, tapi tidak dikembalikan saat restore. Bug sekunder: `PenggunaController::simpan()` tidak menyertakan `data_karyawan_id` saat `ProfilKaryawan::create()`. |
| **Fix** | Setelah restore karyawan, query ProfilKaryawan via user_id lalu update DataKaryawan.status ke sudah_terpakai via nik_karyawan. Tambah data_karyawan_id di ProfilKaryawan::create() pada admin flow. |
| **Verifikasi** | PENDING — perlu cek alur: buat akun → hapus akun → restore dari Data Terhapus → cek status di Data Karyawan |
| **Pelajaran** | Setiap operasi restore harus mempertimbangkan side-effect dari operasi hapus. Hapus akun mereset status, restore harus mengembalikannya. |
| **Log Keyword** | status belum_terpakai, restore karyawan, data_karyawan_id null |

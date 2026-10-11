# CHANGELOG FIXES

Terbaru → terlama. Hanya tulis setelah terverifikasi.

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

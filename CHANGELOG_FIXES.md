# CHANGELOG FIXES

Terbaru → terlama. Hanya tulis setelah terverifikasi.

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

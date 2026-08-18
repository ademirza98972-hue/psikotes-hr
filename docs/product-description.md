# Product Description — Sistem Psikotes HR Online

**Produk**: Sistem Psikotes HR Online  
**Organisasi**: PT Jhonlin Group — Departemen HR  
**Tech Stack**: Laravel · MySQL · Blade · Tailwind CSS · Alpine.js  
**Versi Dokumen**: 1.0 — 18 Agustus 2026

---

## Ringkasan Produk

Sistem Psikotes HR Online adalah aplikasi web internal yang memungkinkan departemen HR menyelenggarakan, mengelola, dan mengevaluasi proses psikotes secara digital — menggantikan metode manual maupun ketergantungan pada tools pihak ketiga. Sistem ini melayani dua jenis peserta: **kandidat** (pelamar kerja) dan **karyawan** (untuk evaluasi berkala atau seleksi promosi).

Seluruh siklus psikotes — dari penjadwalan sesi, pengerjaan tes oleh peserta, penskoran otomatis, hingga pelaporan hasil berbasis psikogram — ditangani dalam satu platform. Admin HR tidak perlu berpindah-pindah tools; semua data terpusat dan dapat diakses melalui satu dashboard.

---

## Masalah yang Diselesaikan

| Masalah Lama | Solusi dalam Sistem Ini |
|---|---|
| Psikotes masih manual atau tersebar di tools berbeda | Satu platform terpusat untuk seluruh siklus tes |
| Penskoran dilakukan secara manual oleh psikolog/HR | Scoring engine otomatis per alat tes |
| Hasil tes tidak terarsip dengan baik | Database terstruktur; hasil dapat diakses kapan saja |
| Tidak ada kontrol akses yang granular | Role-based permission yang dapat dikonfigurasi admin |
| Laporan hasil sulit dibagikan | Ekspor PDF psikogram per peserta |

---

## Pengguna (User Roles)

Sistem menggunakan role berbasis izin (permission-based), bukan role hardcode. Role dapat ditambah dan dikonfigurasi oleh Super Admin.

### Role Bawaan

| Role | Deskripsi |
|---|---|
| **Super Admin** | Akses penuh — termasuk kelola role, izin, dan seluruh data sistem |
| **Admin HR** | Kelola pengguna, soal, penjadwalan tes, review hasil |
| **HR Viewer** | Hanya bisa melihat dashboard dan hasil tes; tidak bisa mengubah data |
| **Kandidat** | Mendaftar, melengkapi profil, mengerjakan tes yang ditugaskan |
| **Karyawan** | Sama seperti Kandidat, namun terhubung ke data kepegawaian internal |

### Izin (Permissions) yang Tersedia

Setiap role dapat dikombinasikan dari izin-izin berikut:

- `dashboard.lihat` — Akses dashboard admin
- `pengguna.lihat / tambah / edit / hapus` — Manajemen akun pengguna
- `pengguna.verifikasi` — Verifikasi dan approval kandidat
- `pengguna_admin.kelola` — Kelola akun Admin/Staff internal
- `peran.kelola` — Manajemen role dan konfigurasi izin
- `izin.kelola` — Kelola daftar izin sistem
- `data_karyawan.kelola` — Manajemen master data karyawan
- `master_data.kelola` — Kelola departemen dan posisi
- `kategori_tes.kelola` — Kelola alat tes dan bank soal
- `soal.lihat / tambah / edit / hapus` — Manajemen bank soal
- `hasil_tes.lihat` — Melihat hasil tes peserta
- `hasil_tes.review` — Memberi catatan HR pada hasil tes
- `hasil_tes.lihat_sensitif` — Akses hasil tes dengan konten sensitif (mis. MMPI-2)
- `data_terhapus.kelola` — Kelola dan pulihkan data yang di-soft delete

---

## Fitur Utama

### 1. Manajemen Alat Tes (Bank Soal)

Admin dapat mendefinisikan berbagai alat tes psikologi dengan konfigurasi lengkap:

- **Format Dasar**: Pilihan Ganda, Skala Likert, Forced Choice, Grid (Kraepelin)
- **Pola Skoring**: dikonfigurasi per alat tes
- **Durasi**: total durasi dan/atau batas waktu per soal
- **Flag Sensitif**: menandai alat tes yang hanya bisa dilihat oleh pengguna dengan izin khusus (mis. MMPI-2)
- **Dimensi & Level**: setiap alat tes punya dimensi dengan ambang kategori (Rendah / Kurang / Cukup / Baik / Baik Sekali, atau Normal / Perlu Perhatian / Signifikan untuk tes klinis)
- **Norma Konversi**: upload norma untuk konversi skor mentah ke skor skala/persentil
- **Bank Soal**: soal bisa dilengkapi gambar, mendukung format IST, tipe subjektif, dll.

**Alat tes yang didukung antara lain**: IST, EPPS, PAPIKOSTIK, Kraepelin (Grid), dan alat tes berbasis Pilihan Ganda atau Forced Choice lainnya.

---

### 2. Penjadwalan Sesi Tes

Admin dapat membuat **Sesi Tes** yang mengelompokkan peserta dan alat tes:

- Nama sesi, tanggal mulai & selesai
- Satu sesi bisa memuat beberapa alat tes
- Satu sesi bisa memuat beberapa peserta (karyawan maupun kandidat)
- Status sesi otomatis: **Draft → Belum Dimulai → Aktif → Kedaluwarsa → Selesai**
- Filter hasil berdasarkan sesi aktif di halaman hasil tes

---

### 3. Pengerjaan Tes oleh Peserta

Setelah login, peserta mengerjakan tes yang sudah ditugaskan:

- Halaman instruksi sebelum memulai tiap alat tes
- Timer countdown per soal (jika batas waktu per soal aktif)
- **Format Pilihan Ganda / Forced Choice**: soal satu per satu, jawaban tersimpan real-time
- **Format Grid (Kraepelin)**: grid angka yang dikerjakan per kolom, masing-masing kolom punya batas waktu; sistem mencatat jumlah benar, salah, dan kelewat per kolom
- Timeout otomatis mengakhiri sesi jika waktu habis
- Setelah semua alat tes selesai, peserta diarahkan ke halaman konfirmasi selesai

---

### 4. Scoring Engine Otomatis

Setelah peserta submit, sistem menjalankan scoring engine:

- **Pola berbasis bobot dimensi** — setiap opsi jawaban punya bobot kontribusi ke tiap dimensi
- **Norma konversi** — skor mentah dikonversi ke skor skala menggunakan tabel norma yang diupload admin
- **Hanker Analysis** untuk tes Grid — regresi linier sederhana untuk menghitung tren produktivitas (meningkat / stabil / menurun)
- **Skor EPPS** dihitung dengan format khusus menggunakan `FormatHasilEppsService`
- Hasil skor disimpan per dimensi per peserta per sesi

---

### 5. Laporan & Psikogram

Admin HR dapat mengakses laporan hasil tes yang komprehensif:

- **Halaman Hasil Tes**: daftar semua peserta per sesi dengan status pengerjaan
- **Detail Hasil per Peserta**: skor per dimensi, kategori (R/K/C/B/BS atau klinis), ringkasan Grid
- **Psikogram**: visualisasi profil psikologis yang dikelompokkan ke bidang:
  - Intelektual
  - Sikap Kerja
  - Kepribadian
  - Potensi Kerja
  - Sensitif (hanya bisa dilihat oleh pengguna dengan izin `hasil_tes.lihat_sensitif`)
- **Catatan HR**: admin bisa menambahkan catatan narasi per peserta
- **Ekspor PDF**: psikogram per peserta bisa didownload sebagai PDF (A4 portrait)
- **Print View**: tampilan print-friendly dengan skor PAP (PAPIKOSTIK) dan psikogram

---

### 6. Manajemen Pengguna

Admin dapat mengelola seluruh akun dalam sistem:

- **Akun Karyawan**: daftarkan karyawan, hubungkan ke data kepegawaian (departemen, posisi, NIK)
- **Akun Kandidat**: daftarkan pelamar; admin bisa approve atau tolak kandidat
- **Akun Admin/Staff**: kelola akun internal HR dengan role yang dapat dikonfigurasi
- Toggle status aktif/non-aktif per akun
- Soft delete: akun yang dihapus masuk ke Data Terhapus dan bisa dipulihkan

---

### 7. Master Data Organisasi

- **Departemen**: daftar departemen perusahaan
- **Posisi**: posisi pekerjaan, terorganisir per departemen
- **Standar Kompetensi Posisi**: kompetensi yang dibutuhkan per posisi (dasar untuk evaluasi hasil tes)

---

### 8. Data Terhapus (Soft Delete & Recovery)

Semua entitas utama menggunakan soft delete. Admin dengan izin `data_terhapus.kelola` dapat:

- Melihat 7 kategori data terhapus: Akun Karyawan, Akun Kandidat, Akun Admin/Staff, Data Karyawan, Departemen, Posisi, Peran
- **Pulihkan** data yang terhapus (restore)
- **Hapus Permanen** jika memang tidak diperlukan lagi

---

### 9. Manajemen Role & Izin

Super Admin dapat mengkonfigurasi role dan izin secara dinamis:

- Buat, ubah, dan hapus role
- Assign kombinasi izin ke tiap role
- Tidak ada izin yang hardcode — seluruhnya berbasis database
- Sistem cek izin real-time di setiap controller dan view

---

### 10. Profil Pengguna

Semua pengguna (admin maupun peserta) dapat:

- Mengedit profil diri (nama, nomor HP, jenis kelamin)
- Upload foto profil
- Ubah password

---

### 11. Autentikasi

Sistem memiliki dua jalur login terpisah:

- **Login Peserta** (`/login`) — untuk kandidat dan karyawan
- **Login Admin** (`/admin/login`) — untuk admin, staff, dan super admin
- Registrasi diri (`/daftar`) — khusus untuk calon kandidat; status pending sampai diverifikasi HR
- Lupa password dengan reset via email

---

## Arsitektur Teknis

```
Laravel (PHP) — MVC dengan Blade Templates
├── resources/views/
│   ├── admin/          — seluruh UI panel admin
│   └── peserta/        — UI pengerjaan tes peserta
├── app/Http/Controllers/
│   ├── Admin/          — controller panel admin (12 controller)
│   ├── AutentikasiController.php
│   ├── DashboardController.php
│   └── PengerjaanTesController.php
├── app/Models/          — 27 model Eloquent
├── app/Services/
│   ├── ScoringEngineService.php
│   └── FormatHasilEppsService.php
└── database/migrations/ — 30+ migration
```

**Database**: MySQL dengan soft delete di semua entitas utama  
**Frontend**: Tailwind CSS + Alpine.js (interaktivitas ringan: timer, modal, toggle)  
**PDF**: DomPDF via `barryvdh/laravel-dompdf`  
**Testing**: PHPUnit (Feature Tests)

---

## Alur Penggunaan Utama

### Alur Admin — Setup Tes Baru

```
1. Buat Alat Tes → definisikan soal, dimensi, level, norma
2. Buat Sesi Tes → pilih tanggal, departemen, alat tes yang digunakan
3. Tambahkan Peserta ke Sesi
4. Peserta mengerjakan tes → sistem skor otomatis
5. Admin review hasil → tambah catatan HR → ekspor PDF
```

### Alur Peserta — Mengerjakan Tes

```
1. Login → Dashboard peserta (daftar sesi yang ditugaskan)
2. Buka sesi → Instruksi tiap alat tes
3. Kerjakan soal (dengan timer jika aktif)
4. Submit → halaman selesai
```

---

## Status Saat Ini (per Agustus 2026)

- Seluruh fitur inti sudah live dan berjalan
- 7 automated Feature Tests berjalan (100% pass)
- Mendukung alat tes: IST, EPPS, PAPIKOSTIK, Kraepelin (Grid), Pilihan Ganda umum
- Psikogram dan ekspor PDF sudah berfungsi
- Integrasi ke sistem HR tracking lain masih dalam roadmap fase berikutnya

# PRD - Sistem Psikotes Online HR

**Departemen**: HR System - PT Jhonlin Group
**Dibuat oleh**: Adee (Ade Saputra)
**Tanggal**: 7 Juli 2026
**Versi**: 1.0

---

## 1. Latar Belakang

Departemen HR butuh sistem buat ngadain psikotes online, dipakai buat dua jenis peserta: **kandidat** (pelamar kerja) dan **karyawan** (yang udah kerja, misal buat evaluasi berkala/promosi). Selama ini kemungkinan psikotes masih manual atau pakai tools pihak ketiga, jadi dibikin sistem internal biar semua data ke-track dan bisa direview HR langsung dari dashboard.

Aplikasi ini terpisah dari sistem tracking aktivitas karyawan yang sedang dikerjakan di fase lain, tapi bisa jadi satu ekosistem HR System ke depannya.

## 2. Tujuan

- Peserta (kandidat/karyawan) bisa daftar dan ikut tes psikotes secara online.
- Tes terdiri dari beberapa kategori/sesi (misal: kepribadian, kognitif), masing-masing punya durasi sendiri.
- Hasil tes otomatis diskor dan diinterpretasikan sistem, HR tinggal review.
- Admin HR bisa kelola soal, kategori, peserta, dan hasil tes dari satu dashboard.
- Sistem hak akses (permission) fleksibel, bukan cuma admin vs user biasa.

## 3. Tech Stack

| Komponen | Pilihan |
|---|---|
| Framework | Laravel (versi terbaru stabil, pakai Blade, **tanpa Filament**) |
| Database | MySQL |
| Auth | Laravel breeze/UI bawaan Laravel (atau auth manual) - dibahas di section 8 |
| View | Blade biasa (resources/views), struktur `index.blade.php`, `tambah.blade.php`, `ubah.blade.php`, `detail.blade.php` |
| CSS | Tailwind CSS (biar gampang custom, bukan template Laravel bawaan/Breeze default yang keliatan generic) |
| JS | Alpine.js kalau butuh interaktivitas ringan (dropdown, modal, timer soal) |

Catatan: sengaja gak pakai Filament karena butuh kontrol penuh atas tampilan CRUD dan belum familiar sama package-nya. Semua CRUD dibikin manual pakai controller + view blade standar.

## 4. Role & Hak Akses

Sistem pakai role-based permission, tapi role-nya bisa diatur (bukan hardcode 2 level doang).

### 4.1 Role default (bisa nambah role baru lewat admin)

| Role | Deskripsi |
|---|---|
| **Super Admin** | Akses penuh, termasuk atur role & permission |
| **Admin HR** | Kelola user, soal, kategori tes, review hasil |
| **HR Viewer** | Cuma bisa lihat dashboard & hasil tes, gak bisa edit apa-apa |
| **Kandidat** | Isi profil, ikut tes, lihat hasil sendiri (kalau HR izinkan) |
| **Karyawan** | Sama kayak kandidat, tapi terhubung ke data kepegawaian |

### 4.2 Struktur permission

Tabel `permissions` isinya granular, contoh:
- `user.tambah`, `user.edit`, `user.hapus`, `user.lihat`
- `soal.tambah`, `soal.edit`, `soal.hapus`, `soal.lihat`
- `kategori_tes.kelola`
- `hasil_tes.lihat`, `hasil_tes.review`
- `dashboard.lihat`

Permission soal dipecah granular karena bisa aja ada HR yang cuma diizinkan **nambah soal** (misal ngisi bank soal dari materi psikolog) tapi gak boleh hapus/edit soal orang lain, tergantung kebijakan nanti.

Role adalah kumpulan permission (`role_has_permissions`). Pas admin bikin/edit role, dia centang permission mana aja yang masuk role itu. Pas bikin user baru, admin tinggal pilih role-nya (opsional: override permission per-user kalau butuh pengecualian).

## 5. Alur Registrasi & Login

### 5.1 Registrasi mandiri (dari halaman login)
- Ada tombol "Daftar" di halaman login.
- User pilih tipe akun: **Kandidat** atau **Karyawan**.
- Isi form: nama, email, no HP, password.
  - Kalau pilih Karyawan: tambahan field NIK/nomor karyawan, departemen (buat validasi ke data internal, atau nanti diverifikasi manual HR).
  - Kalau pilih Kandidat: tambahan field posisi yang dilamar, pendidikan terakhir.
- Setelah daftar, akun otomatis masuk role sesuai tipe (Kandidat/Karyawan) dan langsung tampil di halaman admin (list user) - status bisa "menunggu verifikasi" kalau HR mau approve dulu sebelum bisa akses tes.

### 5.2 Dibuatkan admin (dari dashboard)
- Admin HR/Super Admin bisa tambah user manual dari dashboard.
- Form isi: data user + **pilih role/hak akses langsung** (dropdown role, atau kalau mau custom bisa centang permission manual).
- Cocok buat bikin akun Admin HR baru atau HR Viewer.

### 5.3 Login
- Satu halaman login buat semua role, sistem redirect ke dashboard sesuai role setelah login:
  - Kandidat/Karyawan -> halaman tes psikotes
  - Admin HR/Super Admin/HR Viewer -> dashboard admin

## 6. Modul Psikotes

### 6.1 Kategori Tes
- Setiap tes terdiri dari beberapa **kategori/sesi**, contoh: "Tes Kepribadian", "Tes Logika", "Tes Numerik".
- Tiap kategori punya durasi waktu sendiri (misal Kepribadian 20 menit, Logika 15 menit).
- Peserta ngerjain kategori satu-satu secara berurutan, timer jalan per kategori.
- Kalau waktu habis, otomatis submit jawaban yang udah keisi dan lanjut ke kategori berikutnya.
- Tiap kategori punya setting `jumlah_soal_diambil` — berapa soal yang bakal ditampilkan ke peserta pas ngerjain (diambil random dari bank soal kategori itu, bukan semua soal yang ada di bank ditampilkan sekaligus).

### 6.1a Bank Soal: Sistem vs HR

Ada dua sumber soal dalam satu bank soal per kategori:

- **Soal bawaan sistem** — disediain di awal (seed data), berfungsi sebagai bank soal default yang bisa langsung dipakai.
- **Soal tambahan dari HR** — HR yang punya permission `soal.tambah` bisa nambahin soal baru ke bank soal kategori manapun lewat halaman admin.

Soal dari kedua sumber ini masuk ke **bank soal yang sama** per kategori. Pas peserta mulai tes, sistem ngambil soal secara acak dari keseluruhan bank (campur soal sistem + soal HR) sejumlah `jumlah_soal_diambil`, jadi tiap peserta bisa dapet kombinasi soal yang beda (mengurangi kemungkinan nyontek antar peserta). Soal yang statusnya nonaktif gak ikut diundi.

### 6.2 Tipe Soal
Semua soal **pilihan ganda**, dibagi 2 jenis:
- **Kepribadian**: tiap opsi jawaban punya bobot/nilai ke dimensi kepribadian tertentu (misal dimensi D-I-S-C atau Big Five, tergantung nanti soal dari HR kayak apa). Gak ada jawaban benar/salah.
- **Kognitif**: tiap soal ada 1 opsi jawaban benar, sisanya salah. Dihitung skor berdasarkan jumlah benar.

### 6.3 Scoring & Interpretasi Otomatis
- **Kognitif**: skor = (jumlah benar / total soal) x 100, atau bobot per soal kalau perlu.
- **Kepribadian**: total nilai per dimensi dihitung dari akumulasi bobot opsi yang dipilih, lalu dicocokkan ke tabel rentang skor -> teks interpretasi (misal skor dimensi tertentu 70-100 = "Cenderung dominan dan tegas dalam pengambilan keputusan").
- Tabel interpretasi ini dikelola admin, jadi bisa diubah tanpa ubah kode (skor min-max + teks interpretasi disimpan di database).
- Hasil akhir tiap kategori + hasil keseluruhan tampil di halaman hasil, bisa diexport atau dilihat HR di dashboard.

### 6.4 Manajemen Soal (Admin)
- CRUD kategori tes (`tambah.blade.php`, `ubah.blade.php`, dst), termasuk setting `jumlah_soal_diambil`
- CRUD soal per kategori, termasuk opsi jawaban & bobot/kunci jawaban — dibuka buat HR yang punya permission `soal.tambah` (gak harus Admin HR full)
- Tiap soal nyimpen siapa yang bikin (`dibuat_oleh`) dan sumbernya (sistem/manual), buat tracking asal soal
- Admin bisa nonaktifin soal tertentu (misal soal ketauan bocor) tanpa hapus permanen, biar gak keluar lagi pas random
- CRUD aturan interpretasi (skor range -> teks)
- Preview soal sebelum dipublish

## 7. Struktur Database (Rancangan Awal)

```
users
- id, name, email, password, no_hp
- tipe_akun (enum: kandidat, karyawan)
- role_id (FK ke roles)
- status (aktif, menunggu_verifikasi, nonaktif)
- created_at, updated_at

roles
- id, nama_role, deskripsi

permissions
- id, kode_permission, deskripsi

role_has_permissions
- id, role_id, permission_id

candidate_profiles
- id, user_id, posisi_dilamar, pendidikan_terakhir, no_ktp

employee_profiles
- id, user_id, nik, departemen, jabatan

test_categories
- id, nama_kategori, deskripsi, jenis (kepribadian/kognitif), durasi_menit, urutan
- jumlah_soal_diambil (berapa soal diacak & ditampilkan per peserta)

questions
- id, category_id, pertanyaan
- sumber (enum: sistem, manual)
- dibuat_oleh (FK user, nullable - null kalau soal bawaan sistem)
- status (aktif, nonaktif)

question_options
- id, question_id, opsi_teks
- is_correct (nullable, dipakai buat kognitif)
- dimensi (nullable, dipakai buat kepribadian, misal 'D','I','S','C')
- bobot_nilai (dipakai buat kepribadian)

interpretation_rules
- id, category_id, dimensi (nullable), skor_min, skor_max, teks_interpretasi

test_attempts
- id, user_id, category_id
- waktu_mulai, waktu_selesai, status (belum_mulai, berlangsung, selesai)
- skor_total

test_attempt_questions
- id, attempt_id, question_id, urutan_tampil
(dipakai buat "ngunci" soal acak mana aja yang muncul di attempt itu, biar konsisten pas direview ulang / gak berubah kalau halaman di-refresh)

answers
- id, attempt_id, question_id, option_id, waktu_jawab

test_results
- id, user_id
- ringkasan_skor (json, per kategori/dimensi)
- interpretasi_final (text)
- direview_oleh (FK user, nullable)
- catatan_hr (nullable)
- status_review (belum_direview, sudah_direview)
```

Catatan: skema ini masih rancangan awal, migration Laravel-nya bakal disesuaikan pas eksekusi (termasuk index, foreign key constraint, soft delete kalau perlu).

## 8. Struktur Halaman (Blade Views)

```
resources/views/
  auth/
    login.blade.php
    register.blade.php
  admin/
    dashboard.blade.php
    user/
      index.blade.php
      tambah.blade.php
      ubah.blade.php
      detail.blade.php
    role/
      index.blade.php
      tambah.blade.php
      ubah.blade.php
    kategori-tes/
      index.blade.php
      tambah.blade.php
      ubah.blade.php
    soal/
      index.blade.php
      tambah.blade.php
      ubah.blade.php
    interpretasi/
      index.blade.php
      tambah.blade.php
      ubah.blade.php
    hasil-tes/
      index.blade.php
      detail.blade.php
  peserta/
    dashboard.blade.php        (list tes yang tersedia/status)
    tes/
      mulai.blade.php          (halaman pengerjaan soal per kategori, ada timer)
      selesai.blade.php        (konfirmasi submit kategori)
    hasil/
      index.blade.php          (lihat hasil sendiri, kalau diizinkan HR)
  layouts/
    admin.blade.php
    peserta.blade.php
    auth.blade.php
  components/
    (partial reusable: navbar, sidebar, alert, timer, dst)
```

## 9. Routing (Ringkasan)

| Route | Method | Keterangan |
|---|---|---|
| `/login` | GET/POST | Login semua role |
| `/register` | GET/POST | Registrasi mandiri (pilih kandidat/karyawan) |
| `/logout` | POST | Logout |
| `/admin/dashboard` | GET | Dashboard admin |
| `/admin/users` | GET/POST | List & tambah user |
| `/admin/users/{id}/edit` | GET/PUT | Ubah user |
| `/admin/roles` | GET/POST | Kelola role & permission |
| `/admin/kategori-tes` | GET/POST | Kelola kategori tes |
| `/admin/soal` | GET/POST | Kelola soal per kategori |
| `/admin/interpretasi` | GET/POST | Kelola aturan skor->interpretasi |
| `/admin/hasil-tes` | GET | List & review hasil tes |
| `/peserta/dashboard` | GET | Dashboard peserta |
| `/peserta/tes/{kategori}/mulai` | GET/POST | Pengerjaan soal |
| `/peserta/hasil` | GET | Lihat hasil sendiri |

Semua route admin dibungkus middleware permission check (misal middleware custom `permission:user.tambah`), bukan cuma cek role doang, biar granular.

## 10. UI/UX Guidelines

- Bukan tampilan default Laravel/Bootstrap starter — pakai Tailwind, custom bikin clean & simpel.
- Warna netral, terang, gampang dibaca (putih/abu muda buat background, satu warna aksen buat tombol utama & status).
- Layout admin: sidebar kiri (menu sesuai permission yang dimiliki user), konten utama di kanan, header simpel isi nama user & logout.
- Layout peserta: minim distraksi, terutama pas ngerjain soal (fokus ke pertanyaan + timer, gak ada sidebar rame-rame).
- Timer soal harus jelas keliatan (misal pojok kanan atas), warning warna beda kalau waktu tinggal sedikit.
- Form tambah/ubah pakai layout konsisten antar modul biar gak keliatan "template acak" tiap CRUD beda gaya.
- Table list data (user, soal, hasil tes) pakai pagination + search sederhana.

## 11. Rencana Fase Pengembangan

**Fase 1 - Fondasi**
- Setup project Laravel + MySQL + Tailwind
- Auth: login, register (pilih kandidat/karyawan), middleware role/permission
- CRUD role & permission
- CRUD user (dari admin)
- Layout dasar admin & peserta

**Fase 2 - Modul Tes**
- CRUD kategori tes
- CRUD soal + opsi jawaban
- Halaman pengerjaan tes peserta (timer per kategori, submit otomatis)

**Fase 3 - Scoring & Hasil**
- Logic scoring otomatis (kognitif & kepribadian)
- CRUD aturan interpretasi
- Halaman hasil tes (admin review + peserta lihat hasil)

**Fase 4 - Polish**
- Notifikasi (email/UI) status tes, hasil, dll
- Export hasil (PDF/Excel) kalau dibutuhkan HR
- Refinement UI berdasarkan feedback pemakaian nyata

## 12. Non-Functional Requirements

- Data psikotes sensitif -> pastikan hasil tes cuma bisa diakses role yang punya permission terkait, gak bocor ke peserta lain.
- Timer tes harus validasi di server-side juga (jangan cuma JS di client), biar gak bisa dicurangi.
- Password di-hash standar Laravel (bcrypt).
- Siapkan `.env.example` buat koneksi MySQL pas mulai coding.

---

*Dokumen ini jadi acuan awal buat mulai bikin migration, model, controller, dan view di Laravel. Detail teknis (nama kolom pasti, validasi, dll) bisa disesuaikan pas eksekusi kalau ketemu kasus yang belum kepikiran di sini.*
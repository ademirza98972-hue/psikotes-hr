# CHANGELOG FIXES

Terbaru → terlama. Hanya tulis setelah terverifikasi.

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

# CHANGELOG FIXES

Terbaru → terlama. Hanya tulis setelah terverifikasi.

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

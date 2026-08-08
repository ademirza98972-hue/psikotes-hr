# Master Roadmap — Sistem Psikotes HR
## PT Jhonlin Group | Laravel 13 + MySQL

> Dokumen ini adalah sumber kebenaran tunggal untuk status dan rencana penyelesaian sistem.
> Update setiap kali ada keputusan baru yang mengubah scope atau urutan.

---

## Ringkasan Sistem

**Stack**: Laravel 13, PHP 8.3+, MySQL, Tailwind CSS v4, Alpine.js, Blade, Vite, Laragon 6 (Windows)  
**Repo**: `ademirza98972-hue/psikotes-hr`  
**Alat tes aktif (menunggu konfirmasi psikolog)**: CFIT, EPPS, Papikostik, Kraepelin  
**Alat tes dihapus dari sistem**: DISC, IST, MMPI-2 — dihapus total Fase A  
**Alat tes pending keputusan**: Army Alpha/TIU (format tidak kompatibel), WARTEG (gambar, manual)

---

## Status Per Fase

```
Fase A ✅ → Fase B ✅ → Fase C 🔶 (sedang berjalan)
                                        ↓
              Fase F (paralel)  ←  Fase D → Fase E
```

---

## Fase A — Beresin dummy lama ✅ SELESAI

- ✅ DISC, IST, MMPI-2 dihapus total dari frontend + semua script
- ✅ Diverifikasi di browser, tidak ada halaman yang error

---

## Fase B — Satu alur peserta end-to-end (EPPS) ✅ SELESAI

- ✅ Backend skoring 4 alat tes (CFIT, EPPS, Papikostik, Kraepelin) — selesai dan tervalidasi lewat tinker
- ✅ EPPS Hasil Tes — baca dari database, tervalidasi di browser
- ✅ Dashboard Peserta — baca dari database (Eloquent)
- ✅ EPPS Pengerjaan Tes — peserta jawab soal, tersimpan ke DB, autoskoring jalan
- ✅ Validasi end-to-end di browser: login → dashboard → kerjakan → tersimpan → skor → status update

**Bug yang sudah difix selama Fase B:**
- `sessionKey()` parameter typed `int` bukan `string`
- `@elseif` dangling setelah IST dihapus
- `format_dasar` case mismatch (skoring diam-diam skip)
- `kelompok_segmen='default'` tidak cocok norma EPPS (difix ke `'2'`)
- `belongsToMany` vs `hasMany` — `status_pengerjaan` selalu null

---

## Fase C — CRUD Admin tersambung DB 🔶 SEDANG BERJALAN

### C.1 — Alat Tes CRUD

**Status controller (`AlatTesController.php`):**
- ✅ `DUMMY_ALAT_TES` dihapus
- ✅ `use App\Models\AlatTes;` ditambahkan
- ✅ `index()` — query dari DB (`AlatTes::withTrashed(false)->orderBy('nama')->get()->toArray()`)
- ✅ `tambah()` — pass `$pilihanFormat` ke view
- ✅ `simpan()` — insert nyata dengan validasi

**Validasi di `simpan()`:**
```
nama            required|string|max:150
kode            required|string|max:20|unique:alat_tes,kode
format_dasar    required|string|max:50
jumlah_soal     nullable|integer|min:0
durasi_total_menit  nullable|integer|min:0
batas_waktu_per_soal_aktif  boolean
is_sensitif     boolean
```

**Yang belum ada di controller:**
- ⬜ `edit()` — tampilkan form edit
- ⬜ `update()` — simpan perubahan
- ⬜ `hapus()` — soft delete
- ⬜ `restore()` — pulihkan dari trash

**Status view (`tambah.blade.php`):**
- ⬜ Field "Kode Alat Tes": `name="nama"` → harus `name="kode"`, `id="kode"`, `maxlength="20"`, `old('kode')`
- ⬜ Field "Nama Lengkap": `name="nama_lengkap"` → harus `name="nama"`, `id="nama"`, `maxlength="150"`, `old('nama')`
- ⬜ Toggle "Status Aktif" (`name="status"`) — hapus, always active saat dibuat
- ⬜ Section "Konfigurasi Dimensi Penilaian" — hapus dari form ini, dikelola di halaman terpisah

**Prompt fix tambah.blade.php (siap paste ke Claude Code):**
```
Fix form tambah.blade.php di resources/views/admin/alat-tes/tambah.blade.php.
Ada 3 perubahan:

1. Field "Kode Alat Tes": ubah id="nama" dan name="nama" → id="kode"
   dan name="kode". Tambahkan maxlength="20". old() juga ganti ke
   old('kode').

2. Field "Nama Lengkap Alat Tes": ubah id="nama_lengkap" dan
   name="nama_lengkap" → id="nama" dan name="nama". Tambahkan
   maxlength="150". old() ganti ke old('nama').

3. Hapus seluruh section "Konfigurasi Dimensi Penilaian" (div
   dengan h3 "Konfigurasi Dimensi Penilaian" sampai closing div-nya)
   beserta semua Alpine state yang hanya dipakai section itu:
   tipe_kategori, dimensiArr, addDimensi(), removeDimensi().
   Pertahankan batasAktif dan formatDasar karena masih dipakai.

4. Hapus toggle "Status Aktif" (div yang berisi input name="status"
   dan label "Status Aktif"). Alat tes selalu aktif saat dibuat —
   controller sudah hardcode is_aktif: true.

Scope: HANYA tambah.blade.php. Jangan ubah controller atau file lain.
```

**Keputusan desain dikunci:**
- Dimensi dikelola di halaman terpisah, bukan inline di form tambah/edit
- Pola: setelah alat tes disimpan, ada tombol "Kelola Dimensi" di halaman index/detail
- Ini konsisten dengan pola Bank Soal (soal dikelola terpisah dari metadata)
- `level_dimensi` (norma per pita R/K/C/B/BS) diisi lewat halaman Kelola Dimensi, bukan form tambah

**Halaman Kelola Dimensi (dibikin setelah CRUD dasar selesai):**
- Route: `admin.alat-tes.dimensi` (nested di bawah alat tes)
- Controller method baru: `kelolaSDimensi($id)`, `simpanDimensi($id)`, `hapusDimensi($id)`
- Form per dimensi: nama_dimensi, kode_dimensi, tipe_kategori (psikogram/klinis), deskripsi_aspek, arah_skor
- Setelah dimensi tersimpan, baru bisa input level_dimensi (ambang batas per pita)
- Untuk psikogram: 4 ambang (ambang_r, ambang_k, ambang_c, ambang_b) → 5 pita R-K-C-B-BS
- Untuk klinis: 2 ambang (ambang_normal, ambang_perlu_perhatian) → 3 pita

**`HasilTesController.php:442` — sengaja di-skip dulu:**
- Masih manggil `AlatTesController::DUMMY_ALAT_TES` yang sudah dihapus → akan error
- `hitungPsikogram()` bergantung pada data `dimensi_alat_tes` + `level_dimensi` yang belum ada di DB
- Akan dibenerin setelah halaman Kelola Dimensi selesai dan data dimensi/level sudah terisi
- **Jangan disentuh Claude Code sampai ada instruksi eksplisit**

---

### C.2 — Bank Soal CRUD

**Status**: belum disentuh, masih full dummy

**Yang perlu dilakukan:**
- ⬜ `BankSoalController` — cutover semua method ke DB
- ⬜ Begitu cutover selesai, soal EPPS/CFIT/Papikostik/Kraepelin dari seeder langsung muncul di listing
- ⬜ Form tambah/edit soal — pastikan field cocok dengan tabel `soal` dan `opsi_jawaban`
- ⬜ Filter per alat tes tetap jalan (dropdown alat tes baca dari `AlatTes` model)

---

### C.3 — Penjadwalan Tes CRUD

**Status**: belum disentuh, masih full dummy

**Yang perlu dilakukan:**
- ⬜ `PenjadwalanTesController` — cutover ke DB
- ⬜ Tabel: `sesi_tes`, pivot `alat_tes_sesi_tes`, pivot `peserta_sesi_tes`
- ⬜ Form tambah sesi: nama_sesi, departemen_terkait_id (FK ke departemen), tanggal_mulai, tanggal_selesai, status, pilih alat tes (multi-select dari `alat_tes`)
- ⬜ Assign peserta ke sesi (insert ke `peserta_sesi_tes`)
- ⬜ Status sesi: Draft / Aktif / Selesai

---

### C.4 — Kolom `pola_skoring` + Dispatcher

**Status**: belum dikerjakan

**Tujuan**: sistem otomatis tahu rumus skoring mana yang dipakai per alat tes, tanpa hardcode di controller. Sekarang masih harus panggil lewat tinker manual.

**Yang perlu dilakukan:**
- ⬜ Kolom `pola_skoring` di tabel `alat_tes` (sudah ada di migration, belum diisi dari UI)
- ⬜ Nilai: `kognitif`, `forced_choice`, `consistency`, `rollup`, `grid`
- ⬜ Dispatcher di `ScoringEngineService` — baca `pola_skoring` dari DB, panggil method yang sesuai otomatis
- ⬜ Saat peserta selesai mengerjakan tes (`selesai()` dipanggil), dispatcher langsung tahu pakai rumus apa

---

### C.5 — Upload CSV Norma

**Status**: belum dikerjakan

**Tujuan**: input data norma (EPPS/CFIT punya puluhan-ratusan baris) lewat UI admin, bukan seeder manual.

**Yang perlu dilakukan:**
- ⬜ Form upload CSV di halaman Alat Tes (atau sub-halaman khusus norma)
- ⬜ Validasi format CSV sebelum insert ke `norma_konversi`
- ⬜ Preview hasil parse sebelum konfirmasi import

---

### Urutan pengerjaan Fase C

```
1. Fix tambah.blade.php (prompt sudah siap di C.1)
2. Tambah edit/update/hapus/restore di AlatTesController
3. Buat halaman Kelola Dimensi (controller + view)
4. Benerin HasilTesController:442 (setelah dimensi ada di DB)
5. Cutover BankSoalController ke DB
6. Cutover PenjadwalanTesController ke DB
7. Kolom pola_skoring + dispatcher
8. Upload CSV norma
```

---

## Fase D — Hasil Tes & Pengerjaan Tes 3 alat tes sisanya

**Catatan**: EPPS sudah selesai di Fase B. Fase D khusus 3 alat tes yang belum punya tampilan sama sekali.

### D.1 — Halaman Hasil Tes

**CFIT:**
- ⬜ Tampilan: badge IQ tunggal (skala IQ standar, satu angka)
- ⬜ Baca dari `hasil_skor_peserta` model CFIT

**Papikostik:**
- ⬜ Tampilan: kategori rollup + warna Acceptable / Perlu Ditingkatkan / Optimal
- ⬜ Baca dari `hasil_skor_peserta` model Papikostik

**Kraepelin:**
- ⬜ Tampilan: 4 metrik grid (Panker, Janker, Hanker, Tianker)
- ⬜ Baca dari `hasil_kolom_grid` dan `hasil_skor_peserta` model Kraepelin

### D.2 — Halaman Pengerjaan Tes

**CFIT & Papikostik:**
- ⬜ Pola sama dengan EPPS (pilihan ganda/forced-choice), bisa reuse komponen yang sama

**Kraepelin:**
- ⬜ Widget input angka khusus (numpad) — beda total dari soal pilihan ganda
- ⬜ Peserta input hasil penjumlahan baris angka, bukan pilih opsi jawaban
- ⬜ Timer per kolom, auto-advance ke kolom berikutnya saat waktu habis
- ⬜ Jawaban tersimpan ke `grid_input_peserta`, bukan `jawaban_peserta`

### D.3 — Input soal & norma asli

- ⬜ Masukin soal asli CFIT, Papikostik, Kraepelin lewat CRUD dari Fase C
- ⬜ Masukin norma asli lewat Upload CSV dari Fase C

---

## Fase E — Polish & fitur pendukung

- ⬜ **Catatan HR** — textarea di halaman Hasil Tes, sambung ke DB (kolom `catatan_hr` di `hasil_skor_peserta` atau tabel terpisah)
- ⬜ **Data Terhapus** — Trash/Restore untuk modul Alat Tes, Bank Soal, Sesi Tes
  - Catatan: hapus permanen sesi tes yang ada jawaban/hasil akan kena RESTRICT constraint — controller perlu nangkep ini dan tampilkan pesan yang readable, bukan error SQL
- ⬜ **PDF export** — tombol "Cetak PDF" sudah ada di UI Hasil Tes, belum nyambung fungsi apapun
- ⬜ **Pemetaan Bidang Laporan** — HR/psikolog tentukan taksonomi (Intelektual / Sikap Kerja / Kepribadian / Potensi Kerja / Sensitif) per dimensi alat tes

---

## Fase F — Nunggu keputusan pihak luar (bisa paralel, tidak memblok)

- ⬜ **Konfirmasi psikolog**: daftar final alat tes yang benar-benar dipakai (info HR Agustus 2026 menyebut 6 alat: Army Alpha/TIU, Papikostik, Kraepelin, WARTEG, EPPS, CFIT — belum final)
- ⬜ **Army Alpha/TIU**: keputusan format — format "Directions Test" tidak kompatibel dengan skema soal/opsi_jawaban biasa. Pilihan: (a) manual seperti WARTEG, (b) kanvas interaktif khusus
- ⬜ **WARTEG**: alur upload gambar + catatan manual psikolog — tidak bisa diotomasi
- ⬜ **Beres-beres 3 modul Fase 1** (sengaja ditunda): Data Terhapus, Kelola Admin/Staff, Kelola Peran & Izin — masih campuran real + dummy. Di luar scope psikotes, nyusul setelah Fase A-E selesai

---

## Skema Database — Referensi Cepat

### Tabel yang sudah ada (19 tabel, migration sudah dijalankan)

| Tabel | Keterangan |
|---|---|
| `alat_tes` | Metadata instrumen psikotes |
| `soal` | Butir soal per alat tes |
| `opsi_jawaban` | Pilihan jawaban per soal |
| `dimensi_alat_tes` | Dimensi penilaian per alat tes |
| `bobot_opsi_dimensi` | Bobot opsi jawaban per dimensi |
| `norma_konversi` | Tabel norma untuk konversi skor mentah |
| `level_dimensi` | Ambang batas pita penilaian (R/K/C/B/BS atau Normal/Perlu Perhatian) |
| `bidang_laporan` | Kategori laporan psikogram (Intelektual, Sikap Kerja, dst) |
| `dimensi_bidang_laporan` | Mapping dimensi → bidang laporan |
| `sesi_tes` | Jadwal/sesi pengerjaan tes |
| `alat_tes_sesi_tes` | Pivot: alat tes apa saja yang ada di satu sesi |
| `peserta_sesi_tes` | Pivot: siapa saja peserta di satu sesi + status pengerjaan |
| `jawaban_peserta` | Jawaban per soal per peserta (format pilihan ganda/Likert/forced-choice) |
| `grid_input_peserta` | Input grid per kolom Kraepelin |
| `hasil_kolom_grid` | Hasil kalkulasi per kolom grid (Kraepelin) |
| `hasil_skor_peserta` | Skor final per dimensi per peserta |
| `standar_kompetensi_posisi` | Standar kompetensi per posisi jabatan |
| *(+ 2 tabel lain dari Fase 1)* | users, departemen, posisi, dst |

### Field penting yang perlu diingat

**`alat_tes`**: `kode` (unique, max 20), `nama` (max 150), `format_dasar`, `durasi_total_menit`, `batas_waktu_per_soal_aktif`, `batas_waktu_per_soal_detik`, `is_sensitif`, `jumlah_soal`, `is_aktif`, softDeletes

**`dimensi_alat_tes`**: `kode_dimensi`, `nama_dimensi`, `deskripsi_aspek`, `tipe_kategori` (enum: psikogram/klinis), `arah_skor` (enum: tinggi_baik/rendah_baik), `urutan`

**`level_dimensi`**: menyimpan ambang batas. Psikogram: isi 4 baris (ambang_r, ambang_k, ambang_c, ambang_b). Klinis: isi 2 baris (ambang_normal, ambang_perlu_perhatian). Skema generik, bukan kolom terpisah.

**`peserta_sesi_tes`**: `user_id`, `sesi_tes_id`, `status_pengerjaan` (enum: Belum Mengerjakan/Sedang Berjalan/Selesai), `tanggal_pengerjaan`

**Index custom** (nama diperpendek karena batas MySQL 64 karakter):
- `norma_konversi`: index `norma_konversi_lookup_index`
- `grid_input_peserta`: index `grid_input_peserta_lookup_index`

**FK delete behavior**: semua `RESTRICT` (default Laravel) — hapus sesi tes yang ada jawaban akan error, bukan cascade. Ini disengaja untuk proteksi data historis.

---

## Keputusan Desain yang Sudah Dikunci

| Keputusan | Detail |
|---|---|
| Dimensi dikelola terpisah | Bukan inline di form tambah/edit alat tes. Halaman tersendiri "Kelola Dimensi" |
| DISC/IST/MMPI-2 dihapus total | Bukan dinonaktifkan, tapi dihapus dari semua file |
| Army Alpha dihapus dari batch aktif | Format tidak kompatibel dengan skema soal standar |
| WARTEG tetap manual | Drawing-based, tidak bisa diotomasi |
| Ambang batas di `level_dimensi` | Bukan kolom di `dimensi_alat_tes`. Skema generik yang bisa nampung psikogram maupun klinis |
| FK sesi_tes RESTRICT | Proteksi data historis. Error jika coba hapus sesi yang ada jawaban |
| pola_skoring per alat tes | Enum: kognitif/forced_choice/consistency/rollup/grid. Dispatcher otomatis pilih rumus |
| Status toggle dihapus dari form tambah | Alat tes selalu aktif saat pertama dibuat |

---

## Aturan yang Tidak Boleh Dilanggar

1. **Jangan `migrate:fresh` tanpa izin eksplisit** — ini sudah jadi aturan permanen sejak insiden wipe database
2. **Scope Claude Code harus ketat** — sebutkan HANYA file mana yang boleh diubah di setiap prompt
3. **Verifikasi output Claude Code sebelum migrate** — jangan langsung percaya ringkasan teks, cek kode aslinya
4. **HasilTesController:442 jangan disentuh** sampai data dimensi/level sudah ada di DB lewat halaman Kelola Dimensi
5. **Setiap cutover ke DB, cek dulu**: apakah ada referensi DUMMY yang masih tersisa di file lain yang pakai data yang sama

---

## Log Perubahan Dokumen

| Tanggal | Perubahan |
|---|---|
| Agustus 2026 | Dokumen pertama dibuat, menggabungkan semua keputusan dari Fase A-B dan awal Fase C |
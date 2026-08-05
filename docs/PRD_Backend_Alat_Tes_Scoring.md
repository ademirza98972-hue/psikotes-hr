# PRD — Backend Modul Alat Tes & Scoring (Psikotes HR)

**Project**: Psikotes HR — PT Jhonlin Group
**Stack**: Laravel 13, PHP 8.3+, MySQL, Laragon 6 (Windows)
**Konvensi**: Semua nama tabel/kolom/model pakai Bahasa Indonesia, snake_case, konsisten sama Fase 1 & 2 yang udah ada.

---

## 1. Latar Belakang & Tujuan

Frontend Fase 2 (Hasil Tes, Alat Tes, Bank Soal, Penjadwalan Tes) udah jalan pakai data hardcoded. PRD ini nutup gap ke backend beneran: skema database + mesin skoring otomatis buat modul alat tes.

Sumber data: audit 27 alat tes dari file Excel/PDF/Word vendor psikotes, dibedah formula & strukturnya satu-satu.

## 2. Scope Batch Ini

**Dikerjain sekarang (4 alat tes)**: CFIT ✅ (selesai & tervalidasi), EPPS, Papikostik, Kraepelin. Semuanya punya jawaban langsung (pilih opsi / isi angka) — bisa full otomatis dari ujung ke ujung.

**Di luar scope batch ini**: WARTEG dan Army Alpha/TIU (dikeluarkan — dua-duanya butuh interpretasi visual/gambar yang gak bisa langsung dicocokkan ke kunci jawaban sederhana; Army Alpha ternyata format "Directions Test" — narator baca instruksi kompleks, peserta gambar/tandain di lembar berisi bentuk-bentuk, bukan pilihan ganda), dan 19 alat tes lain (BFI, DISC, 16PF, MBTI, MMPI, dst — tetap terdokumentasi tuntas di file lain, tinggal ditambah data-nya kapan aja tanpa ubah skema).

**Prinsip desain**: 1 mesin skoring generik buat semua alat tes (bukan kode terpisah per alat tes). Yang beda-beda cuma datanya (dimensi, bobot, norma) — bukan strukturnya.

## 3. Skema Database

16 tabel, urutan migration sesuai dependency FK:

### 3.1 `alat_tes`
```php
Schema::create('alat_tes', function (Blueprint $table) {
    $table->id();
    $table->string('kode', 20)->unique();
    $table->string('nama', 150);
    $table->string('kategori', 100)->nullable();
    $table->text('deskripsi')->nullable();
    $table->string('format_dasar', 50)->nullable(); // 'Skala Likert', 'Pilihan Ganda', 'Forced Choice'
    $table->unsignedInteger('durasi_total_menit')->nullable();
    $table->boolean('batas_waktu_per_soal_aktif')->default(false);
    $table->unsignedInteger('batas_waktu_per_soal_detik')->nullable();
    $table->boolean('is_sensitif')->default(false); // terhubung ke izin hasil_tes.lihat_sensitif
    $table->unsignedInteger('jumlah_soal')->nullable();
    $table->boolean('is_aktif')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```
*(field `format_dasar` s.d. `jumlah_soal`: hasil cross-check ke `AlatTesController::DUMMY_ALAT_TES` di repo — cocokin ke situ, bukan diasumsikan.)*

### 3.2 `soal`
```php
Schema::create('soal', function (Blueprint $table) {
    $table->id();
    $table->foreignId('alat_tes_id')->constrained('alat_tes')->cascadeOnDelete();
    $table->unsignedInteger('nomor');
    $table->text('teks_soal');
    $table->enum('tipe_format', ['pilihan_ganda', 'skala_likert', 'forced_choice', 'grid', 'naratif']);
    $table->foreignId('duplikat_dari_soal_id')->nullable()->constrained('soal')->nullOnDelete();
    $table->unsignedInteger('urutan')->default(0);
    $table->timestamps();
    $table->softDeletes();
    $table->index(['alat_tes_id', 'urutan']);
    $table->index('duplikat_dari_soal_id');
});
```

> `duplikat_dari_soal_id`: kolom nullable self-referencing FK ke `soal.id`. Menandai bahwa soal ini adalah pengulangan soal lain (mis. EPPS item 211–225 yang mengulang pasangan dari 210 item utama). Digunakan mesin skoring untuk cek konsistensi jawaban — skor "con" EPPS dihitung dari kecocokan jawaban soal asli vs pengulangnya. Alat tes lain dapat memanfaatkan kolom ini di masa depan.

### 3.3 `opsi_jawaban`
```php
Schema::create('opsi_jawaban', function (Blueprint $table) {
    $table->id();
    $table->foreignId('soal_id')->constrained('soal')->cascadeOnDelete();
    $table->string('teks_opsi', 500);
    $table->unsignedInteger('urutan')->default(0);
    $table->timestamps();
});
```

### 3.4 `dimensi_alat_tes`
```php
Schema::create('dimensi_alat_tes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('alat_tes_id')->constrained('alat_tes')->cascadeOnDelete();
    $table->string('kode_dimensi', 20);
    $table->string('nama_dimensi', 150);
    $table->text('deskripsi_aspek')->nullable(); // teks di bawah nama dimensi di psikogram
    $table->enum('tipe_kategori', ['psikogram', 'klinis'])->default('psikogram');
    $table->enum('arah_skor', ['tinggi_baik', 'rendah_baik'])->default('tinggi_baik');
    $table->unsignedInteger('urutan')->default(0);
    $table->timestamps();
    $table->unique(['alat_tes_id', 'kode_dimensi']);
});
```
*(`tipe_kategori` nentuin skema banding di `level_dimensi`: `psikogram` = 5 pita R-K-C-B-BS, `klinis` = 2-3 pita normal/perlu_perhatian. Ambang batas itu sendiri gak jadi kolom di sini, diisi sebagai baris di `level_dimensi`.)*

### 3.5 `bobot_opsi_dimensi`
Jantung mesin skoring Pola A (Likert+reverse) & Pola B (forced-choice).
```php
Schema::create('bobot_opsi_dimensi', function (Blueprint $table) {
    $table->id();
    $table->foreignId('opsi_jawaban_id')->constrained('opsi_jawaban')->cascadeOnDelete();
    $table->foreignId('dimensi_id')->constrained('dimensi_alat_tes')->cascadeOnDelete();
    $table->decimal('bobot', 8, 2);
    $table->boolean('is_reverse')->default(false);
    $table->timestamps();
    $table->unique(['opsi_jawaban_id', 'dimensi_id']);
});
```

### 3.6 `norma_konversi`
Buat Pola C (kognitif+norma). `sumber_dimensi_id` nullable — null = lookup pakai skor dimensi sendiri; diisi = "pinjam" skor dimensi lain (kasus EPPS "con").
```php
Schema::create('norma_konversi', function (Blueprint $table) {
    $table->id();
    $table->foreignId('alat_tes_id')->constrained('alat_tes')->cascadeOnDelete();
    $table->foreignId('dimensi_id')->nullable()->constrained('dimensi_alat_tes')->nullOnDelete();
    $table->foreignId('sumber_dimensi_id')->nullable()->constrained('dimensi_alat_tes')->nullOnDelete();
    $table->string('kelompok_segmen', 50)->default('default'); // "usia_21_25", "L", "P", dst
    $table->unsignedTinyInteger('tahap')->default(1);
    $table->decimal('skor_mentah_min', 10, 2);
    $table->decimal('skor_mentah_max', 10, 2);
    $table->decimal('skor_hasil', 10, 2);
    $table->timestamps();
    $table->index(['alat_tes_id', 'dimensi_id', 'kelompok_segmen', 'tahap']);
});
```

### 3.7 `dimensi_turunan_komponen`
Skor gabungan/rollup (Papikostik: 20 skala→7 kategori).
```php
Schema::create('dimensi_turunan_komponen', function (Blueprint $table) {
    $table->id();
    $table->foreignId('dimensi_turunan_id')->constrained('dimensi_alat_tes')->cascadeOnDelete();
    $table->foreignId('dimensi_komponen_id')->constrained('dimensi_alat_tes')->cascadeOnDelete();
    $table->decimal('bobot', 8, 2)->default(1);
    $table->timestamps();
    $table->unique(['dimensi_turunan_id', 'dimensi_komponen_id'], 'turunan_komponen_unique');
});
```

### 3.8 `level_dimensi`
```php
Schema::create('level_dimensi', function (Blueprint $table) {
    $table->id();
    $table->foreignId('alat_tes_id')->constrained('alat_tes')->cascadeOnDelete();
    $table->foreignId('dimensi_id')->nullable()->constrained('dimensi_alat_tes')->cascadeOnDelete();
    $table->string('label', 50);
    $table->decimal('skor_min', 10, 2);
    $table->decimal('skor_max', 10, 2);
    $table->unsignedInteger('urutan')->default(0);
    $table->timestamps();
});
```

### 3.9 `interpretasi_teks`
Di-defer isinya (kosong dulu, diisi manual sama psikolog lewat UI nanti).
```php
Schema::create('interpretasi_teks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('dimensi_id')->constrained('dimensi_alat_tes')->cascadeOnDelete();
    $table->foreignId('level_id')->constrained('level_dimensi')->cascadeOnDelete();
    $table->text('teks_narasi')->nullable();
    $table->timestamps();
    $table->unique(['dimensi_id', 'level_id']);
});
```

### 3.10 `bidang_laporan`
```php
Schema::create('bidang_laporan', function (Blueprint $table) {
    $table->id();
    $table->string('nama', 150);
    $table->unsignedInteger('urutan')->default(0);
    $table->timestamps();
});
```

### 3.11 `dimensi_bidang_laporan`
```php
Schema::create('dimensi_bidang_laporan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('dimensi_id')->constrained('dimensi_alat_tes')->cascadeOnDelete();
    $table->foreignId('bidang_laporan_id')->constrained('bidang_laporan')->cascadeOnDelete();
    $table->timestamps();
    $table->unique(['dimensi_id', 'bidang_laporan_id']);
});
```

### 3.12 `jawaban_peserta`
Sesuaikan FK `peserta`/`sesi_tes` ke nama tabel Fase 1 yang sebenarnya.
```php
Schema::create('jawaban_peserta', function (Blueprint $table) {
    $table->id();
    $table->foreignId('peserta_id')->constrained('peserta'); // sesuaikan nama tabel Fase 1
    $table->foreignId('sesi_tes_id')->constrained('sesi_tes'); // sesuaikan nama tabel Fase 1
    $table->foreignId('soal_id')->constrained('soal');
    $table->foreignId('opsi_dipilih_id')->nullable()->constrained('opsi_jawaban');
    $table->text('jawaban_teks')->nullable();
    $table->decimal('nilai_input', 10, 2)->nullable();
    $table->timestamp('waktu_jawab')->nullable();
    $table->timestamps();
    $table->index(['peserta_id', 'sesi_tes_id', 'soal_id']);
});
```

### 3.13 `grid_input_peserta`
Khusus Kraepelin.
```php
Schema::create('grid_input_peserta', function (Blueprint $table) {
    $table->id();
    $table->foreignId('peserta_id')->constrained('peserta');
    $table->foreignId('sesi_tes_id')->constrained('sesi_tes');
    $table->foreignId('alat_tes_id')->constrained('alat_tes');
    $table->unsignedInteger('kolom_ke');
    $table->unsignedInteger('baris_ke');
    $table->unsignedTinyInteger('jawaban_peserta')->nullable();
    $table->unsignedTinyInteger('jawaban_benar');
    $table->boolean('is_benar')->default(false);
    $table->timestamp('waktu_input')->nullable();
    $table->timestamps();
    $table->index(['peserta_id', 'sesi_tes_id', 'alat_tes_id', 'kolom_ke']);
});
```

### 3.14 `hasil_kolom_grid`
```php
Schema::create('hasil_kolom_grid', function (Blueprint $table) {
    $table->id();
    $table->foreignId('peserta_id')->constrained('peserta');
    $table->foreignId('sesi_tes_id')->constrained('sesi_tes');
    $table->foreignId('alat_tes_id')->constrained('alat_tes');
    $table->unsignedInteger('kolom_ke');
    $table->unsignedInteger('jumlah_benar')->default(0);
    $table->unsignedInteger('jumlah_salah')->default(0);
    $table->unsignedInteger('jumlah_kelewat')->default(0);
    $table->unsignedInteger('waktu_pakai_detik')->nullable();
    $table->timestamps();
    $table->unique(['peserta_id', 'sesi_tes_id', 'alat_tes_id', 'kolom_ke'], 'hasil_kolom_unique');
});
```

### 3.15 `hasil_skor_peserta`
Muara semua alat tes.
```php
Schema::create('hasil_skor_peserta', function (Blueprint $table) {
    $table->id();
    $table->foreignId('peserta_id')->constrained('peserta');
    $table->foreignId('sesi_tes_id')->constrained('sesi_tes');
    $table->foreignId('alat_tes_id')->constrained('alat_tes');
    $table->foreignId('dimensi_id')->constrained('dimensi_alat_tes');
    $table->decimal('skor_mentah', 10, 2)->nullable();
    $table->decimal('skor_akhir', 10, 2)->nullable();
    $table->foreignId('level_id')->nullable()->constrained('level_dimensi');
    $table->timestamps();
    $table->unique(['peserta_id', 'sesi_tes_id', 'dimensi_id'], 'hasil_skor_unique');
});
```

### 3.16 `standar_kompetensi_posisi` (opsional/nanti)
```php
Schema::create('standar_kompetensi_posisi', function (Blueprint $table) {
    $table->id();
    $table->foreignId('posisi_id')->constrained('posisi');
    $table->foreignId('dimensi_id')->constrained('dimensi_alat_tes');
    $table->foreignId('level_id_diharapkan')->constrained('level_dimensi');
    $table->timestamps();
    $table->unique(['posisi_id', 'dimensi_id'], 'standar_kompetensi_unique');
});
```

---

## 4. Formula Skoring per Alat Tes (5 Batch Aktif)

### 4.1 CFIT (Culture Fair Intelligence Test) — Skala 3A & 3B

- **4 sub-tes**: TES1 (13 soal, pilihan a-f), TES2 (14 soal, pilihan a-e, **jawaban wajib pilih 2 huruf sekaligus** — format unik, mis. "be", "ae"), TES3 (13 soal, pilihan a-f), TES4 (10 soal, pilihan a-e)
- **Skoring**: tiap soal benar/salah dibanding kunci, dijumlah → skor mentah 0-50
- **Norma**: lookup 1 tahap, khusus kelompok usia **17 tahun ke atas** (tes ini emang buat dewasa, wajar cuma 1 kelompok usia) → RS 0→IQ 38 s.d. RS 50→IQ 183. **Data lengkap: `database/seeders/data/norma_cfit.csv`** (kolom: `raw_score`, `iq`)
- **Klasifikasi IQ** (8 tingkat): ≤69 Mentally Retardation, ≤79 Borderline Defective, ≤89 Low Average, ≤109 Average, ≤119 High Average, ≤139 Superior, ≤169 Very Superior, >169 Genius
- **Sumber soal**: PDF hasil scan (gak ada teks extractable) — perlu transkrip manual. Ada 1 lembar jawaban resmi LPSP3 UI (JPG) yang bisa jadi rujukan struktur.
- **Pola**: C (kognitif + norma), paling simpel dari 5 batch — **rekomendasi jadi pilot pertama**

### 4.2 Army Alpha / TIU (5 & 6) — ⏸️ DI-DEFER, di luar batch aktif

**TIU 5/6 gak dipake** (dikonfirmasi HR — dari 3 sub-instrumen di folder ini, cuma Army Alpha yang relevan). **Army Alpha sendiri ternyata format "Directions Test"**: narator baca instruksi kompleks (mis. "buat tanda silang di lingkaran pertama DAN huruf 1-5 di lingkaran setelah 4"), peserta gambar/tandai/tulis di lembar berisi bentuk-bentuk (lingkaran, segitiga, bujur sangkar) — bukan pilihan ganda, gak cocok sama struktur `soal`/`opsi_jawaban`/`bobot_opsi_dimensi` yang ada. Sebagian instruksi bisa dibikin interaktif (klik zona, isi angka di kotak), tapi sebagian lain (mis. "tarik garis dari lingkaran 3 ke 6, lewat di bawah lingkaran 4") butuh cek jalur/geometri yang levelnya jauh di atas kebutuhan alat tes lain. **Disamakan perlakuannya kayak WARTEG** — di luar mesin skoring generik, nunggu giliran terpisah kalau nanti mau dikerjain.

- **Struktur**: TIU 5, TIU 6, dan Army Alpha — 3 sub-instrumen berbeda tapi 1 folder
- **Skoring**: pilihan ganda benar/salah standar
- **Norma TIU 5**: tabel WS (Weighted Score) → kategori, ada 8 tingkatan (BS/B/S/K/KS area, lihat tabel norma asli). **Data: `database/seeders/data/norma_tiu5.csv`** (kolom: `instrumen`, `ws`, `raw_score_min`, `raw_score_max`, `kategori_dugaan` — kolom kategori masih dugaan, lihat `Panduan_Data_Norma_Mentah.md` buat catatannya)
- **Skala Army Alpha & TIU 6**: Weighted Score 1-5 → 5 kategori: **Kurang Sekali, Kurang, Rata-rata, Baik, Baik Sekali**. TIU 6 raw score range: 1-8/9-16/17-24/25-32/33-40 masing-masing map ke 5 kategori itu. **Data: `database/seeders/data/norma_army_alpha_tiu6.csv`** (kolom: `instrumen`, `weighted_score`, `raw_score_min`, `raw_score_max`, `kategori`)
- **Sumber soal**: hampir semua PDF (Army Alpha Kunci, FORM TEST ARMY, TIU 5 kunci, TIU 5 soal, TIU 6 Kunci, TIU 6 alat tes) hasil **scan gambar, gak ada teks extractable** — butuh transkrip manual paling banyak dari 5 batch ini. Yang bisa dibaca cuma "KUNCI JAWABAN DAN NORMA-TIU 5.pdf" dan "SKALA ARMY ALPHA DAN TIU 6.docx/pdf"
- **Pola**: C (kognitif + norma), sama kayak CFIT — mesin scoring bisa reuse dari CFIT

### 4.3 EPPS (Edwards Personal Preference Schedule)

- **Total 225 item**: 210 pasang forced-choice buat 15 kebutuhan (ach, def, ord, exh, aut, aff, int, suc, dom, aba, nur, chg, end, het, agg) + **15 pasang tambahan (item 211-225) yang mengulang pasangan dari bagian awal**, buat cek konsistensi jawaban
- **15 skor kebutuhan**: raw tally → percentile (norma bercabang gender) → 5 level: Sangat Rendah/Rendah/Sedang/Tinggi/Sangat Tinggi
- **1 skor validitas "con"**: raw = jumlah jawaban yang konsisten antara soal asli & pengulangannya (dari 15 pasang) → percentile (norma bercabang gender via VLOOKUP) → 5 level: Sangat Pembohong/Pembohong/Cukup Jujur/Jujur/Sangat Jujur — **tampilkan ini duluan/terpisah di laporan**, mirip validity scale MMPI
- **Data norma lengkap (15 kebutuhan + con)**: `database/seeders/data/norma_epps_full.csv` (kolom: `kode_kategori` [1-4, maknanya belum pasti — perlu konfirmasi psikolog, lihat `Panduan_Data_Norma_Mentah.md`], `raw_score`, lalu 16 kolom dimensi: ach, def, ord, exh, aut, aff, int, suc, dom, aba, nur, chg, end, het, agg, con)
- **Validasi input**: total pilihan A+B harus = 210 (buat 210 item utama, di luar 15 consistency check)
- **Sumber soal**: PDF & DOC teksnya **bisa dibaca langsung**, gak perlu OCR — paling gampang buat digitalisasi dari 5 batch ini
- **Pola**: B (forced-choice) + validity check tambahan

### 4.4 Papikostik (PAPI Kostick)

- **90 soal**, pilihan A/B per soal
- **20 skala dasar** (kode: E, C, D, R, S, V, T, I, L, G, W, F, K, Z, O, B, X, P, A, N) — raw score dihitung via tally per blok soal (COUNTIF per kelompok ~11 soal)
- **Rollup 2 tingkat** ke **7 kategori besar**: Arah Kerja, Kepemimpinan, Aktivitas, Pergaulan, Sifat, Ketaatan, Gaya Kerja (pakai `dimensi_turunan_komponen`)
- **Klasifikasi**: bukan Rendah/Sedang/Tinggi, tapi **3 warna**: Acceptable (kuning) / Perlu ditingkatkan (merah muda) / Optimal (putih)
- **Interpretasi teks**: ada per skala (HLOOKUP ke tabel narasi)
- **Catatan**: gak butuh file CSV norma — ini murni tally (COUNTIF) + rollup, gak ada tabel skor-mentah-ke-nilai-lain yang perlu dikonversi
- **Sumber soal**: file "Papi Kostick.xls" (lama) diabaikan, pakai "Software Papikostik Terbaru.xlsm" sebagai acuan utama
- **Pola**: B (forced-choice) + rollup

### 4.5 Kraepelin

- **Bukan soal-jawaban biasa** — peserta jumlahin pasangan angka berurutan dalam kolom (50 kolom), sistem generate angka & tau jawaban benar secara real-time
- **4 faktor, 100% otomatis** (pakai formula asli vendor dari file zip, bukan istilah dokumen perusahaan):
  - **Kecepatan** (Panker) = rata-rata jumlah benar per kolom = Σ(jumlah benar semua kolom) / 50
  - **Ketelitian** (Tianker) = jumlah salah + jumlah terlewat
  - **Ketahanan** (Hanker) = selisih garis regresi linier (performa vs nomor kolom) di titik kolom 50 vs kolom 1, otomatis dilabeli **"meningkat"** kalau positif atau **"menurun"** kalau negatif
  - **Keajegan** (Janker) = rata-rata deviasi (mean absolute deviation) jumlah-benar-per-kolom terhadap rata-rata keseluruhan
- **Catatan**: file asli vendor punya tabel "kode pendidikan" per faktor, tapi **gak nyambung ke formula manapun** — aman diabaikan sepenuhnya, bukan kelalaian kita
- **Pola**: D (grid), satu-satunya yang gak pakai tabel `soal`/`opsi_jawaban`/`bobot_opsi_dimensi` — pakai `grid_input_peserta` + `hasil_kolom_grid`
- **Rekomendasi**: kerjain paling akhir dari 5 batch, karena mekanismenya paling beda sendiri

---

## 5. Rencana Implementasi Bertahap

1. **Migration** — jalankan semua 19 tabel (16 awal + sesi_tes + 2 pivot dari Tahap 1c), urutan sesuai section 3 (FK dependency) — ✅ selesai
2. **Model + relasi Eloquent** — 1 model per tabel — ✅ selesai
3. **Pilot: CFIT** — alat tes paling simpel (Pola C polos, gak ada rollup/validity/grid). Input data (soal + kunci + norma), build `ScoringEngineService` versi awal, validasi hasil sistem vs hasil Excel vendor pakai 1 set jawaban contoh — ✅ selesai, tervalidasi cocok persis (skor_mentah 13, skor_akhir IQ 72)
4. ~~Army Alpha/TIU~~ — ⏸️ di-defer (lihat section 4.2), lompat ke EPPS
5. **EPPS** — extend `ScoringEngineService`: tambah dukungan validity/consistency check (`norma_konversi.sumber_dimensi_id`) — **lanjut ke sini**
6. **Papikostik** — extend lagi: tambah dukungan rollup (`dimensi_turunan_komponen`)
7. **Kraepelin** — paling akhir, mekanisme grid yang beda total dari 3 lainnya

Prinsip: bangun `ScoringEngineService` bertahap nambah kapabilitas per alat tes, bukan desain generik penuh dari awal tanpa ada yang teruji jalan.

## 6. Asumsi & Perlu Diverifikasi Sebelum/Selama Implementasi

- ~~Nama tabel Fase 1 (`peserta`, `sesi_tes`)~~ — **udah diverifikasi langsung ke repo** (bukan asumsi lagi): `peserta` gak perlu tabel baru, pakai `users.id` langsung (`users` udah punya `tipe_akun` enum kandidat/karyawan). `sesi_tes` emang belum ada, field-nya diambil dari dummy `PenjadwalanTesController` (`nama_sesi`, `departemen_terkait`, `tanggal_mulai/selesai`, `status`, `daftar_alat_tes`, `jumlah_peserta`, `jumlah_selesai`).
- **Field `alat_tes` & `dimensi_alat_tes` udah disesuaikan** ke struktur dummy `AlatTesController` yang sebenarnya (lihat section 3.1 & 3.4) — bukan asumsi awal lagi.
- **Mismatch alat tes frontend vs backend**: dummy data frontend yang udah jadi (`AlatTesController`, `BankSoalController`, `HasilTesController`, `PengerjaanTesController`) itu isinya DISC, IST, EPPS, MMPI-2 — bukan 5 alat tes batch aktif ini (Army Alpha/TIU, Papikostik, Kraepelin, EPPS, CFIT). Cuma EPPS yang overlap. **Keputusan**: tetap lanjut backend buat 5 alat tes asli dari HR; frontend buat 4 sisanya (Army Alpha/TIU, Papikostik, Kraepelin, CFIT) nyusul dikerjain belakangan, reuse pola tampilan yang udah ada (`tipe_kategori` psikogram/klinis, dst).
- Norma table CFIT & Army Alpha/TIU perlu diketik ulang manual dari sumber (sebagian PDF hasil scan, gak bisa auto-extract)
- Daftar 5 alat tes ini masih berdasar info HR yang **belum final** — psikolog belum konfirmasi resmi
- Pemetaan `bidang_laporan` (dimensi masuk kategori laporan yang mana) — **udah ada contohnya dari dummy** (`bidang_psikogram`: 'Potensi Kerja', 'Kepribadian', 'Sikap Kerja', 'Intelektual', 'Sensitif'), tapi buat 5 alat tes baru tetap perlu HR/psikolog nentuin, gak menghalangi migration/implementasi inti
- Interpretasi teks (`interpretasi_teks`) sengaja dikosongin dulu — diisi manual sama psikolog lewat UI nanti, bukan urgent buat batch ini

## 7. Dokumen Referensi Lain

- `Skema_Database_Alat_Tes_Scoring.md` — versi naratif/konsep skema (lebih detail alasan tiap tabel)
- `Ringkasan_Tampilan_Hasil_Skoring_26_Alat_Tes.md` — breakdown tampilan hasil semua 26 alat tes (termasuk 21 yang di luar batch ini)
- `Tracker_CrossCheck_26_Alat_Tes.md` — checklist status verifikasi tiap alat tes
- `Migration_Schema_Alat_Tes_Scoring.md` — versi standalone dari section 3 di atas
- `database/seeders/data/Panduan_Data_Norma_Mentah.md` — penjelasan tiap kolom di 4 file CSV norma (`norma_cfit.csv`, `norma_tiu5.csv`, `norma_army_alpha_tiu6.csv`, `norma_epps_full.csv`), termasuk catatan bagian mana yang masih perlu dikonfirmasi (kategori TIU5, kode_kategori EPPS)
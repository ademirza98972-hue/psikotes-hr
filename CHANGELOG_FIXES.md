# CHANGELOG_FIXES.md

### Fix #6 — Stat Cards Selalu 0: $selectedSesiId Memilih Sesi Tanpa Peserta
Tanggal · 2026-08-16
File · `app/Http/Controllers/Admin/HasilTesController.php` (method `index()`)
Masalah · Stat cards (TINGKAT PENYELESAIAN, STATUS SELESAI, PROGRES KOLEKTIF) selalu menampilkan 0% dan 0/0 peserta, padahal tabel peserta di tab "Per Sesi" menampilkan data dengan benar.
Akar · Baris `$selectedSesiId = $sesiList->first()->id;` memilih sesi pertama secara absolut (diurutkan DESC by `tanggal_mulai`), bukan sesi pertama yang memiliki peserta. Sesi terbaru (id=7, nama "tes") memiliki `jumlah_peserta = 0`, sehingga `$pesertaBySesi[$selectedSesiId]` selalu kosong.
Fix · Tambah blok pra-pemilihan: loop `$sesiList` sekali untuk menyimpan ID sesi yang memiliki `pesertaSesiTesRecords` tidak kosong ke `$hasilPeserta[]`. Gunakan `$sesiList->first(fn($s) => $hasilPeserta[$s->id])` untuk mengambil sesi pertama yang benar-benar punya peserta, dengan fallback ke `$sesiList->first()->id` jika semua sesi kosong.
Verifikasi · Simulasi tinker: sebelum fix `selectedSesiId=7` (kosong), setelah fix `selectedSesiId=6` (5 peserta). Stats count untuk sesi 6 = 5 (bukan 0).
Pelajaran · Logic "pilih default" harus mempertimbangkan konten, bukan hanya urutan — asumsi "first record = valid default" bisa salah jika record tersebut empty.
Log Keyword · `selectedSesiId`, `pesertaBySesi`, `stat cards`, `kosong`, `default selection`
Deploy · local

### Fix #5 — Timer Subtes IST Double-Submit Guard
Tanggal · 2026-08-15
File · `resources/views/peserta/pengerjaan-soal.blade.php`
Masalah · Timer subtes IST memicu double-submit: saat timer habis, JS memanggil `form.submit()`, tetapi server juga bisa redirect ke `jawab()` dengan `auto_submit=true` jika kondisi `$sisaWaktuDetik <= 0` terdeteksi pada request berikutnya (race condition).
Akar · Tidak ada guard di sisi klien yang mencegah form di-submit lebih dari satu kali.
Fix · Tambah `submitted: false` di dalam blok `x-data` timer IST. Pada blok `else` (timer habis), ganti `document.getElementById('form-jawaban').submit()` langsung menjadi conditional: hanya submit jika `!this.submitted`, lalu set `this.submitted = true` sebelum submit.
Verifikasi · Code review manual — penambahan guard hanya di satu baris, tidak mengubah flow form submission lain (nav prev/next, tombol selesai).
Pelajaran · Race condition antara client-initiated submit dan server-side auto-redirect perlu guard idempotency di sisi klien.
Log Keyword · `double-submit`, `timer subtes IST`, `submitted guard`, `x-data`
Deploy · local

### Fix #4 — Proteksi Copy-Paste pada Halaman Pengerjaan Soal
Tanggal · 2026-08-15
File · `resources/views/peserta/pengerjaan-soal.blade.php`
Masalah · Peserta dapat melakukan copy-paste isi soal menggunakan klik kanan, Ctrl+C, atau menu browser, berpotensi membocorkan konten tes.
Akar · Tidak ada handler yang memblokir event DOM terkait copy, cut, paste, dan text selection.
Fix · Menambahkan script vanilla JS setelah `@vite` di akhir view: (1) `preventDefault` pada event contextmenu, copy, cut, paste, selectstart; (2) blokir keyboard shortcut Ctrl+C, Ctrl+X, Ctrl+V, Ctrl+A, Ctrl+U; (3) CSS `user-select: none` pada elemen teks soal dan jawaban.
Verifikasi · Test manual: klik kanan dicegah, Ctrl+C/X/V/A/U dicegah, teks soal tidak bisa diseleksi.
Pelajaran · Proteksi front-end bersifat soft guard — tetap perlu verifikasi di sisi server bahwa jawaban unik dan tidak ada kebocoran API.
Log Keyword · `contextmenu`, `copy`, `paste`, `selectstart`, `user-select`
Deploy · local

### Fix #1 — IDE Warning Bersih: Laravel IDE Helper + Tipe Eksplisit
Tanggal · 2026-08-05
File · `app/Http/Controllers/PengerjaanTesController.php`, `composer.json`
Masalah · VS Code Problems panel menampilkan "Undefined method 'user'" dari `auth()->user()` dan "Parameter has no type information available" di beberapa parameter metode di `PengerjaanTesController`.
Akar · Laravel helper method seperti `auth()`, `redirect()`, `view()` tidak dikenal oleh IDE tanpa IDE Helper. Parameter `$sesiId` dan `$name` di `PengerjaanTesController` belum bertipe eksplisit.
Fix · 1) Install `barryvdh/laravel-ide-helper` (dev dependency), jalankan `php artisan ide-helper:generate` dan `ide-helper:meta`. 2) Tambah type hint `int` pada parameter `$sesiId` (baris 65, 80, 195, 248) dan `int` pada `$name` (baris 75) di `PengerjaanTesController.php`.
Verifikasi · `php -l` pada semua controller yang disentuh menghasilkan no syntax errors.
Pelajaran · Laravel helper method selalu butuh IDE Helper untuk tipe yang dikenali IDE.
Log Keyword · `ide-helper`, `Parameter has no type information`, `Undefined method 'user'`
Deploy · local

### Fix #3 — Mixed Format: kunci_jawaban Tidak Terekstrak dari Validated Input
Tanggal · 2026-08-14
File · `app/Http/Controllers/Admin/BankSoalController.php`
Masalah · Pada format Mixed, rule `mixedRules()` meminta field `'kunci'` (uppercase A-D), tetapi update() membaca `$validated['kunci']` padahal form mengirim `'kunci_jawaban'` (lowercase a-e). Akibatnya kunci jawaban tidak tersimpan dan opsi E tidak pernah valid.
Akar · Nama field validasi tidak konsisten antara rule, input form (kartu-soal.blade), dan penugasan ke model.
Fix · 1) `mixedRules()`: ganti `'kunci'` → `'kunci_jawaban'` dengan rule `'in:a,b,c,d,e'`. 2) `update()`: ganti `$validated['kunci'] ?? null` → `$validated['kunci_jawaban'] ?? $request->input('kunci_jawaban')`. 3) `update()`: `$hurufMap` jadi `['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5]` agar cocok dengan format lowercase.
Verifikasi · `grep -n` pada controller menampilkan ketiga perubahan; kartu-soal.blade sudah menggunakan `strtolower($soal['kunci_jawaban'])` sehingga cocok dengan format lowercase.
Pelajaran · Nama field di rule validasi harus selalu sinkron dengan nama field yang dikirim form.
Log Keyword · `mixedRules`, `kunci_jawaban`, `hurufMap`, `Mixed format`
Deploy · local

### Fix #2 — sessionKey() $name: Tipe Salah Menyebabkan TypeError Saat Runtime
Tanggal · 2026-08-05
File · `app/Http/Controllers/PengerjaanTesController.php` (baris 75)
Masalah · Setelah Fix #1, `sessionKey(int $sesiId, int $name)` punya tipe `int` untuk `$name`, padahal semua pemanggil (baris 105/110/116/147/219/232) mengirim string seperti `'current_step'` dan `'answers'`. Saat fungsi dipanggil runtime, PHP akan melempar `TypeError: sessionKey(): Argument #2 ($name) must be of type int, string given`.
Akar · Penambahan type hint di Fix #1 menggunakan `int` karena baris `$sesiId` juga `int`, tetapi `$name` adalah label/key string yang di-interpolasi ke dalam session key (`"pengerjaan_tes.sesi_{$sesiId}.{$name}"`).
Fix · Ubah signature jadi `private function sessionKey(int $sesiId, string $name): string`. Tidak ada perubahan pada kode pemanggil karena tipe baru sesuai dengan argumen yang sudah dikirim.
Verifikasi · `php -l` menghasilkan no syntax errors. Tidak ada panggilan ke `sessionKey()` di file lain (dicek via grep) sehingga blast radius hanya file ini.
Pelajaran · Type hint harus sesuai dengan tipe argumen runtime, bukan sekadar meniru tipe parameter sebelah. Saat memperkenalkan type hint, **periksa semua call site** sebelum commit.
Log Keyword · `sessionKey`, `TypeError`, `string $name`, `Argument #2`
Deploy · local

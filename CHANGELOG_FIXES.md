# CHANGELOG_FIXES.md

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

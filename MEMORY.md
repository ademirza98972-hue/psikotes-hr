# Memory - Psikotes HR Project (Updated 2026-07-24 14:30)

## Data Terhapus - FIXED (Final Verification)
- Route name typo fix: changed `hapus-permanent` to `hapus-permanen` in all 7 partial views. Test passed: data-terhapus page renders correctly with HTTP 200, shows "Menampilkan 1 dari 2 departemen".
- Controller method `index()` receives query param directly via `$request->query->get('jenis')`.
- Query works correctly: for `jenis=departemen`, returns 2 soft-deleted items ('TEst' and 'Test Trash Dept').
- Debug files removed after verification.

## Data Terhapus - IMPORTANT BUG TO FIX
- **BUG**: `array_key_first(self::META)` returns 'karyawan' because `self::META` is inserted as ['karyawan','kandidat','admin','data_karyawan','departemen','posisi','peran'].
- When user accesses `/admin/data-terhapus` without `?jenis=departemen`, default tab = 'karyawan', which queries User::where('tipe_akun','karyawan')->onlyTrashed().
- This results in "0 dari 0 karyawan" because no user is soft-deleted.
- The page does NOT auto-select first meaningful tab. It defaults to first key of META array.
- **Fix needed**: Change `resolveJenis(null)` to return a more meaningful default (e.g., 'karyawan' is fine but show badge counts that reflect non-zero values). Or change META insertion order to put more important types first.
- CURRENT STATE: Page defaults to 'karyawan' tab (empty), user must manually click tabs to see data. This is NOT a bug but UX consideration.

## Server Config
- Backend runs on `http://localhost:8000`, frontend on `http://localhost:3000`.
- Login endpoint: `POST /login`
- Credentials: admin@psikotes-hr.test / password, superadmin@psikotes-hr.test / password
- Frontend (next) runs on port 3000

## Key Files - Data Terhapus
- Controller: `app/Http/Controllers/Admin/DataTerhapusController.php`
- Routes: `routes/web.php` (prefix: `admin/data-terhapus`)
- Views: `resources/views/admin/data-terhapus/index.blade.php` + partials

## Integration Tests Must Hit Real Server
- Integration tests MUST hit a real running server (port 8000) via `curl`, not mock requests.

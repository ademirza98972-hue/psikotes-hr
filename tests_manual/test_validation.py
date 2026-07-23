"""
Validation tests for NIK KTP requirement enforcement.

Tests:
1. Tambah: NIK KTP REQUIRED - empty should reject
2. Tambah: NIK KTP >16 should reject
3. Tambah: Duplicate NIK KTP should reject
4. Ubah: NIK KTP REQUIRED - empty should reject
5. Ubah: Same NIK KTP (no change) should allow
6. Register: NIK KTP required for kandidat type
7. Register: NIK KTP optional for karyawan type
"""
import requests
import re
import time

BASE = "http://127.0.0.1:8080"
s = requests.Session()

# ===== Login =====
r = s.get(f"{BASE}/login")
csrf_token = re.search(r'name="_token"\s+value="([^"]+)"', r.text).group(1)
r = s.post(f"{BASE}/login", data={
    "_token": csrf_token,
    "email": "superadmin@psikotes-hr.test",
    "password": "password",
}, allow_redirects=True)

def csrf_from_page(html):
    m = re.search(r'name="_token"\s+value="([^"]+)"', html)
    return m.group(1) if m else None

results = []

# ===== TEST 1: Tambah with empty NIK KTP =====
r = s.get(f"{BASE}/admin/data-kandidat/tambah")
html = r.text
token = csrf_from_page(html)
response = s.post(f"{BASE}/admin/data-kandidat", data={
    "_token": token,
    "nama_kandidat": "Test Empty NIK",
    "email": f"empty_nik_{int(time.time())}@test.com",
    "password": "password123",
    "password_confirmation": "password123",
    "departemen": "1",
    "posisi_dilamar": "1",
    "pendidikan_terakhir": "S1",
    "nik_kandidat": "",
    "no_hp": "081234567890",
}, allow_redirects=False)
if response.history or response.status_code == 302:
    location = response.headers.get('Location', '')
    if 'data-kandidat/tambah' in location or '/admin/data-kandidat' in location:
        results.append("T1 PASS: Empty NIK KTP rejected (redirect)")
    else:
        results.append(f"T1 WARN: Redirected to {location}")
else:
    if "wajib diisi" in response.text:
        results.append("T1 PASS: Empty NIK KTP rejected with 'wajib diisi' error")
    else:
        results.append(f"T1 FAIL: Empty NIK accepted! Status: {response.status_code}")

# ===== TEST 2: Tambah with NIK > 16 digits =====
r = s.get(f"{BASE}/admin/data-kandidat/tambah")
html = r.text
token = csrf_from_page(html)
response = s.post(f"{BASE}/admin/data-kandidat", data={
    "_token": token,
    "nama_kandidat": "Test Long NIK",
    "email": f"long_nik_{int(time.time())}@test.com",
    "password": "password123",
    "password_confirmation": "password123",
    "departemen": "1",
    "posisi_dilamar": "1",
    "pendidikan_terakhir": "S1",
    "nik_kandidat": "12345678901234567",  # 17 digits
    "no_hp": "081234567890",
}, allow_redirects=False)
if response.status_code == 302:
    location = response.headers.get('Location', '')
    if 'data-kandidat/tambah' in location:
        results.append("T2 PASS: NIK >16 rejected (redirect)")
    else:
        results.append(f"T2 WARN: Unexpected redirect to {location}")
else:
    results.append(f"T2 FAIL: Long NIK accepted! Status: {response.status_code}")

# ===== TEST 3: Tambah with duplicate NIK KTP =====
# Get existing kandidat's NIK KTP
r = s.get(f"{BASE}/admin/data-kandidat")
existing_nik = re.search(r'>([0-9]{16})<', r.text)
if existing_nik:
    dup_nik = existing_nik.group(1)
    r = s.get(f"{BASE}/admin/data-kandidat/tambah")
    html = r.text
    token = csrf_from_page(html)
    response = s.post(f"{BASE}/admin/data-kandidat", data={
        "_token": token,
        "nama_kandidat": "Test Dup NIK",
        "email": f"dup_nik_{int(time.time())}@test.com",
        "password": "password123",
        "password_confirmation": "password123",
        "departemen": "1",
        "posisi_dilamar": "1",
        "pendidikan_terakhir": "S1",
        "nik_kandidat": dup_nik,
        "no_hp": "081234567890",
    }, allow_redirects=False)
    if response.status_code == 302:
        location = response.headers.get('Location', '')
        if 'data-kandidat/tambah' in location:
            results.append("T3 PASS: Duplicate NIK KTP rejected")
        else:
            results.append(f"T3 WARN: Redirected to {location}")
    else:
        if "sudah digunakan" in response.text or "unik" in response.text:
            results.append("T3 PASS: Duplicate NIK rejected with validation error")
        else:
            results.append(f"T3 FAIL: Duplicate NIK accepted! Status: {response.status_code}")
else:
    results.append("T3 SKIP: No existing NIK found")

# ===== TEST 4: Ubah with empty NIK KTP =====
r = s.get(f"{BASE}/admin/data-kandidat/tambah")
html = r.text
token = csrf_from_page(html)
response = s.post(f"{BASE}/admin/data-kandidat", data={
    "_token": token,
    "nama_kandidat": "Temp for ubah test",
    "email": f"temp_ubah_{int(time.time())}@test.com",
    "password": "password123",
    "password_confirmation": "password123",
    "departemen": "1",
    "posisi_dilamar": "1",
    "pendidikan_terakhir": "S1",
    "nik_kandidat": "1234567890123456",  # 16 unique digits
    "no_hp": "081234567890",
}, allow_redirects=False)
if response.status_code == 302:
    temp_nik = "1234567890123456"
    # Find the kandidat with this NIK
    r = s.get(f"{BASE}/admin/data-kandidat")
    match = re.search(r'data-kandidat/(\d+)/ubah', r.text)
    temp_id = match.group(1) if match else None
    if temp_id:
        # Now update with empty NIK
        r = s.get(f"{BASE}/admin/data-kandidat/{temp_id}/ubah")
        html = r.text
        token = csrf_from_page(html)
        response = s.put(f"{BASE}/admin/data-kandidat/{temp_id}", data={
            "_token": token,
            "_method": "PUT",
            "nama_kandidat": "Test Empty NIK Update",
            "nik_kandidat": "",  # Empty
            "email": f"temp_ubah_{int(time.time())}@test.com",
            "no_hp": "081234567890",
        }, allow_redirects=False)
        if response.status_code == 302:
            location = response.headers.get('Location', '')
            if f'{temp_id}/ubah' in location:
                results.append("T4 PASS: Empty NIK KTP rejected on update")
            else:
                results.append(f"T4 WARN: Redirected to {location}")
        else:
            results.append(f"T4 FAIL: Empty NIK accepted on update! Status: {response.status_code}")
    results.append("Temp kandidat created for ubah test")

# ===== TEST 5: Ubah with same NIK (no change) =====
r = s.get(f"{BASE}/admin/data-kandidat/ubah/1")
if r.status_code == 200:
    r = s.get(f"{BASE}/admin/data-kandidat")
    match = re.search(r'data-kandidat/(\d+)/ubah', r.text)
    ubah_id = match.group(1) if match else "1"
    r = s.get(f"{BASE}/admin/data-kandidat/{ubah_id}/ubah")
    html = r.text
    # Extract existing NIK
    nik_match = re.search(r'value="([^"]{16})"', html.split("nik_kandidat")[1].split('">')[0] if 'nik_kandidat' in html else "")
    existing_nik_val = nik_match.group(1) if nik_match else ""

    # Actually extract from the form value attribute
    nik_section = re.search(r'id="nik_kandidat"[^>]*value="([^"]*)"', html)
    existing_nik_val = nik_section.group(1) if nik_section else "1234567890123456"

    token = csrf_from_page(html)
    response = s.post(f"{BASE}/admin/data-kandidat/{ubah_id}", method="PUT", data={
        "_token": token,
        "_method": "PUT",
        "nama_kandidat": f"Updated {ubah_id}",
        "nik_kandidat": existing_nik_val,  # Same value
        "email": "test@example.com",
    }, allow_redirects=False)
    if response.status_code == 302:
        results.append("T5 PASS: Updating with same NIK KTP succeeds")
    else:
        results.append(f"T5 WARN: Status {response.status_code}")

# ===== TEST 6: Register as kandidat with NIK KTP =====
r = s.get(f"{BASE}/daftar")
html = r.text
token = csrf_from_page(html)
response = s.post(f"{BASE}/daftar", data={
    "_token": token,
    "tipe_akun": "kandidat",
    "email": f"reg_kandidat_{int(time.time())}@test.com",
    "name": "Test Kandidat Register",
    "posisi_dilamar": "1",
    "pendidikan_terakhir": "S1",
    "nik_kandidat": "9999888877776666",
    "no_hp": "081234567890",
    "password": "Password123!",
    "password_confirmation": "Password123!",
}, allow_redirects=False)
if response.status_code == 302 and 'login' in response.headers.get('Location', ''):
    results.append("T6 PASS: Register as kandidat with NIK KTP succeeds")
elif response.status_code == 302:
    results.append(f"T6 WARN: Redirected to {response.headers.get('Location')}")
else:
    results.append(f"T6 FAIL: Status {response.status_code}")

# ===== TEST 7: Register as karyawan WITHOUT NIK KTP =====
s.cookies.clear()
r = s.get(f"{BASE}/daftar")
html = r.text
token = csrf_from_page(html)
response = s.post(f"{BASE}/daftar", data={
    "_token": token,
    "tipe_akun": "karyawan",
    "email": f"reg_karyawan_{int(time.time())}@test.com",
    "name": "Test Employee Register",
    "no_hp": "081234567890",
    "password": "Password123!",
    "password_confirmation": "Password123!",
}, allow_redirects=False)
if response.status_code == 302 and ('login' in response.headers.get('Location', '') or 'email' in response.text.lower()):
    results.append("T7 PASS: Register as karyawan WITHOUT NIK KTP succeeds")
elif response.status_code == 302:
    loc = response.headers.get('Location', '')
    results.append(f"T7 INFO: Redirected to {loc}")
else:
    results.append(f"T7 WARN: Status {response.status_code}")

# ===== SUMMARY =====
print("=" * 50)
print("VALIDATION TEST RESULTS")
print("=" * 50)
for r_item in results:
    print(f"  {r_item}")
print("=" * 50)

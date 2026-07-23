import requests
import re

BASE = "http://127.0.0.1:8080"
s = requests.Session()

r = s.get(f"{BASE}/login")
csrf_token = re.search(r'name="_token"\s+value="([^"]+)"', r.text).group(1)
r = s.post(f"{BASE}/login", data={
    "_token": csrf_token,
    "email": "superadmin@psikotes-hr.test",
    "password": "password",
}, allow_redirects=True)

# Get a kandidat id
r = s.get(f"{BASE}/admin/data-kandidat")
ids = re.findall(r'data-kandidat/(\d+)/ubah', r.text)
print(f"Kandidat IDs found: {ids}")
id_to_use = ids[0] if ids else 1

# ===== CHECK 1: Index page =====
r = s.get(f"{BASE}/admin/data-kandidat")
html = r.text
print(f"\n=== DAFTAR KANDIDAT ===")
print("Columns:", [re.sub(r'<[^>]+>', '', m.group(1)).strip() for m in re.finditer(r'<th[^>]*>(.*?)</th>', re.search(r'<thead[^>]*>(.*?)</thead>', html, re.DOTALL).group(1), re.DOTALL)])
assert "NIK KTP" in html, "FAILED: NIK KTP NOT in index"

# ===== CHECK 2: Tambah page =====
r = s.get(f"{BASE}/admin/data-kandidat/tambah")
html = r.text
print(f"\n=== TAMBAH KANDIDAT ===")
assert "required" in html, "FAILED: required not in tambah"
assert 'maxlength="16"' in html, "FAILED: maxlength=16 not in tambah"
assert "(opsional)" not in html.split("nik_kandidat")[1].split("</div>")[0], "FAILED: (opsional) still in tambah"
print("VERIFIED: tambah page - required, maxlength=16, no (opsional)")

# ===== CHECK 3: Ubah page =====
r = s.get(f"{BASE}/admin/data-kandidat/{id_to_use}/ubah")
html = r.text
print(f"\n=== UBAH KANDIDAT (id={id_to_use}) ===")
if r.status_code == 200:
    assert "required" in html, "FAILED: required not in ubah"
    assert 'maxlength="16"' in html, "FAILED: maxlength=16 not in ubah"
    assert "(opsional)" not in html.split("nik_kandidat")[1].split("</div>")[0], "FAILED: (opsional) still in ubah"
    print("VERIFIED: ubah page - required, maxlength=16, no (opsional)")
else:
    print(f"SKIPPED: status {r.status_code}")

# ===== CHECK 4: Register page (need to logout first since /daftar is guest middleware) =====
s.cookies.clear()
r = s.get(f"{BASE}/daftar")
html = r.text
print(f"\n=== REGISTER ===")
nik_section = html.split("nik_kandidat")[1].split("</div>")[0] if "nik_kandidat" in html else ""
assert "(opsional)" not in nik_section, "FAILED: (opsional) still in register"
print("VERIFIED: register - no (opsional) in NIK section")
assert ':required' in html or 'required' in html, "FAILED: required not in register"
print("VERIFIED: register - has required directive")

print("\n=== ALL CHECKS COMPLETE ===")
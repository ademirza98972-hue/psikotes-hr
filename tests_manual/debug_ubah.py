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

r = s.get(f"{BASE}/admin/data-kandidat/ubah/1")
print(f"Status: {r.status_code}")
print(f"Length: {len(r.text)}")
print(f"Has required: {'required' in r.text}")
print(f"Has NIK KTP: {'NIK KTP' in r.text}")
print(f"Has opsional: {'opsional' in r.text}")
print(f"First 500 chars: {r.text[:500]}")
print("---")
print(f"Has 'id=nik_kandidat' section:")
m = re.search(r'<input[^>]*id=["\']nik_kandidat["\'][^>]*>', r.text)
if m:
    print(f"  Found: {m.group(0)}")

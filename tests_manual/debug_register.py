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

r = s.get(f"{BASE}/daftar")
print(f"Status: {r.status_code}")
# Find the nik_kandidat section
nik_section = re.search(r'<input[^>]*id=["\']nik_kandidat["\'][^>]*>', r.text)
if nik_section:
    print(f"NIK input: {nik_section.group(0)}")
else:
    print("NIK input not found")
    print(f"Length: {len(r.text)}")
    print(f"Has NIK: {'NIK KTP' in r.text}")

"""
Verify form behavior: NIK KTP should be optional for karyawan type, required for kandidat type.
"""
import requests
import re

BASE = "http://127.0.0.1:8080"
s = requests.Session()

r = s.get(f"{BASE}/daftar")
html = r.text

# Check NIK kandidat input - has :required dynamic
print("=== NIK KANDIDAT (kandidat type) ===")
nik_kandidat_match = re.search(r'<input[^>]*id=["\']nik_kandidat["\'][^>]*>', html)
if nik_kandidat_match:
    print(f"  {nik_kandidat_match.group(0)}")
    # Extract attributes
    has_dynamic_required = ':required="tipe === \'kandidat\'"' in nik_kandidat_match.group(0)
    has_required = 'required' in nik_kandidat_match.group(0)
    has_maxlength_16 = 'maxlength="16"' in nik_kandidat_match.group(0)
    print(f"  has :required (dynamic): {has_dynamic_required}")
    print(f"  has required (any): {has_required}")
    print(f"  has maxlength=16: {has_maxlength_16}")

# Check NIK karyawan input - should NOT be required by HTML, but the controller requires it for lookup
print("\n=== NIK KARYAWAN (karyawan type) ===")
nik_karyawan_match = re.search(r'<input[^>]*id=["\']nik_karyawan["\'][^>]*>', html)
if nik_karyawan_match:
    print(f"  {nik_karyawan_match.group(0)}")
    has_dynamic_required = ':required="tipe === \'karyawan\'"' in nik_karyawan_match.group(0)
    print(f"  has :required (karyawan type only): {has_dynamic_required}")

# Check NIK KTP label - should have * indicator
print("\n=== LABEL ===")
label_match = re.search(r'<label[^>]*for=["\']nik_kandidat["\'][^>]*>(.*?)</label>', html, re.DOTALL)
if label_match:
    print(f"  Label: {re.sub(r'<[^>]+>', '', label_match.group(1)).strip()}")
    has_required_asterisk = '*' in label_match.group(1) and 'rose-600' in label_match.group(1)
    has_opsional = '(opsional)' in label_match.group(1)
    print(f"  has '*' (required indicator): {has_required_asterisk}")
    print(f"  has '(opsional)': {has_opsional}")

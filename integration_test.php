<?php
// Integration test: access data-terhapus via real server with session cookie
$cookieJar = sys_get_temp_dir() . '/cookies_integration_test.txt';
if (file_exists($cookieJar)) unlink($cookieJar);

// Step 1: Login
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
$html = curl_exec($ch);
preg_match('/_token.*?value=["\']([^"\']+)/', $html, $m);
$token = $m[1] ?? null;
curl_close($ch);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    '_token' => $token,
    'email' => 'superadmin@psikotes-hr.test',
    'password' => 'password',
]));
curl_exec($ch);
curl_close($ch);

// Step 2: Test ALL jenis tabs
$jenisList = ['karyawan', 'kandidat', 'admin', 'data_karyawan', 'departemen', 'posisi', 'peran'];

echo "=== INTEGRATION TEST: Data Terhapus ===\n\n";

foreach ($jenisList as $jenis) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8000/admin/data-terhapus?jenis=$jenis");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    $page = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (preg_match('/Menampilkan\s*<strong>(\d+)<\/strong>\s*dari\s*<strong>(\d+)<\/strong>/', $page, $mm)) {
        $current = $mm[1];
        $total = $mm[2];
        echo "✓ $jenis : HTTP $httpCode | Menampilkan $current dari $total\n";
    } else {
        echo "? $jenis : HTTP $httpCode | No pagination info found\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";

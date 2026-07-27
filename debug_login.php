<?php
// Extract CSRF token from login page
$loginHtml = file_get_contents('http://localhost:8000/login');
preg_match('/_token.*?value=["\']([^"\']+)["\']/', $loginHtml, $m);
$token = $m[1] ?? null;
echo "Token: " . ($token ? 'found' : 'NOT FOUND') . "\n";

if (!$token) {
    echo "Could not extract token. Login page starts with:\n";
    echo substr($loginHtml, 0, 500) . "\n";
    exit(1);
}

$cookieJar = sys_get_temp_dir() . '/csrf_debug.txt';

// Step 1: GET login to set cookies
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost:8000/login',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookieJar,
    CURLOPT_COOKIEFILE => $cookieJar,
]);
curl_exec($ch);
curl_close($ch);

// Step 2: Login POST
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost:8000/login',
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookieJar,
    CURLOPT_COOKIEFILE => $cookieJar,
    CURLOPT_POSTFIELDS => http_build_query([
        '_token' => $token,
        'email' => 'superadmin@psikotes-hr.test',
        'password' => 'password',
    ]),
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Login HTTP code: $httpCode\n";
curl_close($ch);

// Step 3: Access data-terhapus
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost:8000/admin/data-terhapus?jenis=departemen',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookieJar,
    CURLOPT_COOKIEFILE => $cookieJar,
    CURLOPT_FOLLOWLOCATION => true, // Follow any redirects
]);
$html = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "data-terhapus HTTP code: $httpCode\n";
echo "HTML length: " . strlen($html) . "\n";
echo "First 200 chars: " . substr($html, 0, 200) . "\n";

if (strlen($html) < 100) {
    echo "HTML too short - saving full response for inspection\n";
    file_put_contents(sys_get_temp_dir() . '/short_response.html', $html);
} else {
    file_put_contents(sys_get_temp_dir() . '/data_terhapus_debug.html', $html);

    // Look for table content
    if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/s', $html, $rows)) {
        echo "Found " . count($rows[0]) . " table rows\n";
        foreach (array_slice($rows[0], 0, 5) as $i => $row) {
            $clean = strip_tags($row);
            echo "Row $i: " . trim($clean) . "\n";
        }
    }

    // Check for specific text
    echo "\nContent checks:\n";
    echo "TEst: " . (strpos($html, 'TEst') !== false ? 'FOUND' : 'NOT FOUND') . "\n";
    echo "Test Trash Dept: " . (strpos($html, 'Test Trash Dept') !== false ? 'FOUND' : 'NOT FOUND') . "\n";
    echo "Menampilkan: " . (strpos($html, 'Menampilkan') !== false ? 'FOUND' : 'NOT FOUND') . "\n";
    echo "Belum ada departemen: " . (strpos($html, 'Belum ada departemen') !== false ? 'FOUND' : 'NOT FOUND') . "\n";
    echo "Data Terhapus: " . (strpos($html, 'Data Terhapus') !== false ? 'FOUND' : 'NOT FOUND') . "\n";
}

curl_close($ch);
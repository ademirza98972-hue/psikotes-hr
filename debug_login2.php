<?php
// Proper 2-step approach: GET login for token, then POST login
$cookieJar = sys_get_temp_dir() . '/csrf_debug2.txt';

// Step 1: GET login page to get fresh CSRF token AND set session cookie
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost:8000/login',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookieJar,
    CURLOPT_COOKIEFILE => $cookieJar,
]);
$loginHtml = curl_exec($ch);
preg_match('/_token.*?value=["\']([^"\']+)["\']/', $loginHtml, $m);
$token = $m[1] ?? null;
curl_close($ch);

echo "Token: " . ($token ? 'found' : 'NOT FOUND') . "\n";

if (!$token) exit("No token\n");

// Step 2: Login with SAME cookie jar (session preserved)
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost:8000/login',
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true, // Follow redirect after successful login
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
$urlAfterLogin = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
echo "Login response code: $httpCode\n";
echo "URL after login: $urlAfterLogin\n";
curl_close($ch);

// Step 3: Access data-terhapus
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost:8000/admin/data-terhapus?jenis=departemen',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_COOKIEJAR => $cookieJar,
    CURLOPT_COOKIEFILE => $cookieJar,
]);
$html = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "data-terhapus HTTP code: $httpCode\n";
echo "HTML length: " . strlen($html) . "\n";

// Save full HTML for inspection
file_put_contents(sys_get_temp_dir() . '/data_terhapus_full.html', $html);

// Search for key content
echo "\n=== Content Analysis ===\n";
if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/s', $html, $rows)) {
    echo "Table rows found: " . count($rows[0]) . "\n";
    foreach (array_slice($rows[0], 0, 5) as $i => $row) {
        $clean = strip_tags($row);
        echo "Row $i: " . trim(substr($clean, 0, 100)) . "\n";
    }
}

echo "\nData checks:\n";
echo "TEst: " . (strpos($html, 'TEst') !== false ? 'FOUND ✓' : 'NOT FOUND ✗') . "\n";
echo "Test Trash Dept: " . (strpos($html, 'Test Trash Dept') !== false ? 'FOUND ✓' : 'NOT FOUND ✗') . "\n";
echo "Belum ada departemen: " . (strpos($html, 'Belum ada departemen') !== false ? 'FOUND ✗' : 'NOT FOUND ✓') . "\n";

// Extract pagination info
if (preg_match('/<span[^>]*>\s*Menampilkan\s*<\/span>.*?<strong[^>]*>(\d+)<\/strong>.*?<span[^>]*>\s*dari\s*<\/span>.*?<strong[^>]*>(\d+)<\/strong>/', $html, $pg)) {
    echo "Pagination: Menampilkan <strong>$pg[1]</strong> dari <strong>$pg[2]</strong> departemen\n";
}
// Simpler pattern
if (preg_match('/Menampilkan.*?(\d+)\s*dari\s*(\d+)', $html, $pg)) {
    echo "Simple pagination: $pg[1] dari $pg[2]\n";
}

echo "\nFull page text (stripped):\n";
$lines = array_filter(array_map('trim', preg_split('/[\s]+/', strip_tags($html))));
echo implode("\n", array_slice($lines, 0, 100)) . "\n";

curl_close($ch);
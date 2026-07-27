<?php
$html = file_get_contents(sys_get_temp_dir() . '/data_terhapus_response.html');

// Search for departemen-related text
echo "=== Looking for departemen display count ===\n";
preg_match_all('/Menampilkan.*?departemen/i', $html, $matches);
foreach ($matches[0] as $m) {
    echo "Found: $m\n";
}

echo "\n=== Looking for table content (tbody) ===\n";
if (preg_match('/<tbody[^>]*>(.*?)<\/tbody>/s', $html, $tb)) {
    $tbody = trim($tb[1]);
    echo "tbody length: " . strlen($tbody) . "\n";
    // Remove all HTML tags to see text
    $clean = preg_replace('/<[^>]+>/', ' ', $tbody);
    $clean = preg_replace('/\s+/', ' ', $clean);
    echo "Clean text: " . trim($clean) . "\n";

    // Count <tr> elements
    preg_match_all('/<tr[^>]*>/', $tbody, $trMatches);
    echo "Number of rows: " . count($trMatches[0]) . "\n";
} else {
    echo "No tbody found\n";
}

echo "\n=== Looking for 'Test Trash Dept' or 'TEst' ===\n";
echo "Contains 'TEst': " . (strpos($html, 'TEst') !== false ? 'YES' : 'NO') . "\n";
echo "Contains 'Test Trash Dept': " . (strpos($html, 'Test Trash Dept') !== false ? 'YES' : 'NO') . "\n";

echo "\n=== Looking for empty state message ===\n";
echo "Contains 'Tidak ada departemen yang dihapus': " . (strpos($html, 'Tidak ada departemen yang dihapus') !== false ? 'YES' : 'NO') . "\n";
echo "Contains 'Belum ada departemen': " . (strpos($html, 'Belum ada departemen') !== false ? 'YES' : 'NO') . "\n";

echo "\n=== First 500 chars of tbody area ===\n";
if (preg_match('/<tbody[^>]*>(.*){1,200}/s', $html, $m)) {
    echo substr($m[0], 0, 500) . "\n";
}
<?php
$html = file_get_contents(sys_get_temp_dir() . '/data_terhapus_full.html');

// Context around departemen section
$idx = 0;
while (($pos = stripos($html, 'jenisAktif === \'departemen\'', $idx)) !== false) {
    echo "--- Found at position $pos ---\n";
    // Show ~1500 chars of context
    $start = max(0, $pos - 100);
    $context = substr($html, $start, 3000);
    echo $context . "\n";
    echo "--- END ---\n\n";
    break;
}

// Check if the page uses HTMX or Alpine for tab loading
echo "\n=== HTMX detection ===\n";
echo "Has hx-get: " . (strpos($html, 'hx-get') !== false ? 'YES' : 'NO') . "\n";
echo "Has hx-post: " . (strpos($html, 'hx-post') !== false ? 'YES' : 'NO') . "\n";
echo "Has x-init: " . (strpos($html, 'x-init') !== false ? 'YES' : 'NO') . "\n";
echo "Has x-data: " . (strpos($html, 'x-data') !== false ? 'YES' : 'NO') . "\n";

// Check how many times departemen appears in total
$count = substr_count(strtolower($html), 'departemen');
echo "\nOccurrences of 'departemen': $count\n";

// Check pagination info line
if (preg_match('/Menampilkan\s*<strong>([^<]*)<\/strong>\s*dari\s*<strong>([^<]*)<\/strong>\s*departemen/i', $html, $m)) {
    echo "Pagination: Menampilkan <strong>{$m[1]}</strong> dari <strong>{$m[2]}</strong> departemen\n";
}
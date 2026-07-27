<?php
$html = file_get_contents(sys_get_temp_dir() . '/data_terhapus_response.html');
echo "HTML length: " . strlen($html) . "\n";

// Find if this is about departemen tab
echo "Contains 'departemen' (case insensitive): " . (stripos($html, 'departemen') !== false ? 'YES' : 'NO') . "\n";
echo "Contains 'Data Terhapus': " . (strpos($html, 'Data Terhapus') !== false ? 'YES' : 'NO') . "\n";
echo "Contains 'Hapus': " . (strpos($html, 'Hapus') !== false ? 'YES' : 'NO') . "\n";

// Check class names related to table
echo "\n=== Looking for key patterns ===\n";
$patterns = [
    'Menampilkan',
    'dari',
    'tidak ada',
    'belum ada',
    'terakhir dihapus',
    'Kembalikan',
    'Hapus secara permanen',
    'Kosongkan',
    'Action',
    'Actions',
];
foreach ($patterns as $p) {
    echo "'$p': " . (stripos($html, $p) !== false ? 'FOUND' : 'NOT FOUND') . "\n";
}

// Extract the page title and main section text
echo "\n=== Page text content (first 3000 chars) ===\n";
$clean = strip_tags($html);
$lines = array_filter(array_map('trim', explode("\n", $clean)));
for ($i = 0; $i < min(50, count($lines)); $i++) {
    if (strlen($lines[$i]) > 1) {
        echo $lines[$i] . "\n";
    }
}
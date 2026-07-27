<?php
$html = file_get_contents(sys_get_temp_dir() . '/data_terhapus_full.html');

// Find all pagination patterns
preg_match_all('/Menampilkan\s*<strong>\d+<\/strong>\s*dari\s*<strong>\d+<\/strong>\s*\w+/i', $html, $matches);
echo "=== All pagination patterns found ===\n";
foreach ($matches[0] as $i => $m) {
    echo "$i: $m\n";
}

// Find context around "0 dari 0 departemen"
$idx = 0;
while (($pos = stripos($html, '0</strong> dari <strong>0', $idx)) !== false) {
    $start = max(0, $pos - 100);
    $end = min(strlen($html), $pos + 200);
    echo "\n--- Found '0 dari 0' at $pos ---\n";
    echo substr($html, $start, $end - $start) . "\n";
    $idx = $pos + 10;
}

// Check badge "2" specifically
echo "\n=== Badge '2' in departemen tab ===\n";
$idx = 0;
$count = 0;
while (($pos = stripos($html, 'x-show="jenisAktif === \'departemen\'"', $idx)) !== false && $count < 5) {
    $start = max(0, $pos - 50);
    $end = min(strlen($html), $pos + 600);
    echo "--- Context $count (position $pos) ---\n";
    echo strip_tags(substr($html, $start, $end - $start)) . "\n";
    echo "---\n";
    $count++;
    $idx = $pos + 10;
}
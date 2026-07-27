<?php
$html = file_get_contents(sys_get_temp_dir() . '/data_terhapus_full.html');

// Find the departemen section
if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/s', $html, $rows)) {
    foreach ($rows[0] as $i => $row) {
        $clean = trim(preg_replace('/\s+/', ' ', strip_tags($row)));
        if (stripos($clean, 'departemen') !== false || stripos($clean, 'TEst') !== false || stripos($clean, 'Trash') !== false) {
            echo "Row $i: $clean\n";
        }
    }
}

// Look for departemen table content specifically
$departemenSection = '';
if (preg_match('/Departemen.*?(?=Posisi|Peran|<tbody|<tfoot)/si', $html, $m)) {
    $departemenSection = $m[0];
    echo "\n=== Departemen section first 1500 chars ===\n";
    echo substr($departemenSection, 0, 1500) . "\n";
}
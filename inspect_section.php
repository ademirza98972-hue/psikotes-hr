<?php
$html = file_get_contents(sys_get_temp_dir() . '/data_terhapus_full.html');

// Extract the section around "Departemen" sidebar link and its table
$idx = stripos($html, 'departemen');
echo "First 'departemen' at position: $idx\n";

// Get context around each occurrence
$contexts = [];
$i = 0;
while (($pos = stripos($html, 'departemen', $i)) !== false) {
    $start = max(0, $pos - 50);
    $end = min(strlen($html), $pos + 300);
    $contexts[] = substr($html, $start, $end - $start);
    $i = $end;
}
echo "Found '$pos' occurrences of 'departemen'\n";
echo "\n=== Contexts ===\n";
foreach ($contexts as $ci => $ctx) {
    echo "--- Context $ci ---\n";
    echo substr($ctx, 0, 200) . "\n\n";
}

// Find tbody for departemen
if (preg_match_all('/<tbody[^>]*>(.*?)<\/tbody>/s', $html, $tbs)) {
    echo "\n=== All TBODIES ===\n";
    foreach ($tbs[1] as $ti => $tbody) {
        $clean = strip_tags(trim($tbody));
        echo "TBODY $ti (${strlen($tbody)} chars): " . substr($clean, 0, 100) . "\n";
        if (stripos($clean, 'departemen') !== false) {
            echo "  -> This is the departemen table!\n";
        }
    }
}
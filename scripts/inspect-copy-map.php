<?php
$map = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/shared-language-copy-map.json'), true);

echo "Total pairs: " . count($map) . "\n";
$doc21_pairs = array_filter($map, fn($p) => $p['document'] === 21);
echo "Doc 21 pairs: " . count($doc21_pairs) . "\n";
foreach ($doc21_pairs as $p) {
    echo "  {$p['traditional_id']} / {$p['simplified_id']}: {$p['traditional']} | {$p['simplified']}\n";
}

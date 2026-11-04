<?php
$dir = 'C:/Projects/mpcbc/wordpress/elementor';
$files = glob($dir . '/*.json');

echo "Auditing JSON files in {$dir}:\n";
$total_styles = 0;
$total_divs = 0;
foreach ($files as $f) {
    $bn = basename($f);
    if ($bn === 'real-exported-sample.json') continue; // reference only
    $content = file_get_contents($f);
    $styles = substr_count($content, 'style=');
    $divs = substr_count($content, '<div');
    $total_styles += $styles;
    $total_divs += $divs;
    if ($styles > 0 || $divs > 0) {
        echo "  [FAIL] {$bn}: styles={$styles}, divs={$divs}\n";
    } else {
        echo "  [PASS] {$bn}: 0 styles, 0 divs\n";
    }
}
echo "Total styles in site templates: {$total_styles}\n";
echo "Total divs in site templates: {$total_divs}\n";

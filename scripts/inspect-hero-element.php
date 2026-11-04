<?php
$current = json_decode(get_post_meta(21, '_elementor_data', true), true);
$before = json_decode(file_get_contents(__DIR__ . '/../content-source/before-visual-pass/post-21.json'), true);

$elCurr = $current[0]['elements'][0]['elements'][0]['elements'][0]['elements'][0]['elements'][0]['elements'][0];
$elBef = $before[0]['elements'][0]['elements'][0]['elements'][0]['elements'][0]['elements'][0]['elements'][0];

echo "Curr ID: " . $elCurr['id'] . "\n";
echo "Curr CSS ID: " . ($elCurr['settings']['_cssid']['value'] ?? 'none') . "\n";
echo "Curr widgetType: " . ($elCurr['widgetType'] ?? 'none') . "\n";
echo "Curr padding: " . json_encode($elCurr['settings']['padding'] ?? null) . "\n";
echo "Bef padding: " . json_encode($elBef['settings']['padding'] ?? null) . "\n";

<?php
$data = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/before-visual-pass/21.json'), true);

$sec4 = $data[4];
$tabs = $sec4['elements'][0]['elements'][1];

echo "e-tabs styles:\n" . json_encode($tabs['styles'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
echo "e-tabs-menu styles:\n" . json_encode($tabs['elements'][0]['styles'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
echo "First e-tab styles:\n" . json_encode($tabs['elements'][0]['elements'][0]['styles'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

<?php
$data = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/before-visual-pass/21.json'), true);

$sec5 = $data[5];
$collage = $sec5['elements'][0]['elements'][0]['elements'][1];

foreach ($collage['elements'] as $idx => $img) {
    echo "Image $idx ({$img['id']}):\n";
    echo "  settings: " . json_encode($img['settings'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
}

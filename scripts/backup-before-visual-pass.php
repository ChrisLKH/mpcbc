<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$outDir = 'C:/Projects/mpcbc/content-source/before-visual-pass';
if (!is_dir($outDir)) {
    mkdir($outDir, 0777, true);
}

$pages = [21, 210, 211, 511, 16];
foreach ($pages as $id) {
    $raw = get_post_meta($id, '_elementor_data', true);
    $data = json_decode($raw, true);
    $filePath = "$outDir/$id.json";
    file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    echo "Backed up $id to $filePath (" . strlen($raw) . " bytes)\n";
}
echo "Backup complete.\n";

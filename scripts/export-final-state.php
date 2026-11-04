<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$root = 'C:/Projects/mpcbc';
$actual = json_decode(get_post_meta(21, '_elementor_data', true), true);

$path = "$root/elementor/home-content-filled.json";
$exp = json_decode(file_get_contents($path), true);
$exp['content'] = $actual;
file_put_contents($path, wp_json_encode($exp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "Updated $path (" . strlen(json_encode($actual)) . " bytes content)\n";

if (file_exists("$root/content-source/atomic-compiled/21.json")) {
    file_put_contents("$root/content-source/atomic-compiled/21.json", wp_json_encode($actual, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    echo "Updated $root/content-source/atomic-compiled/21.json\n";
}

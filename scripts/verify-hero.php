<?php
$current = json_decode(get_post_meta(21, '_elementor_data', true), true);
$before = json_decode(file_get_contents(__DIR__ . '/../content-source/before-visual-pass/21.json'), true);
$heroCurrent = $current[0];
$heroBefore = $before[0];

if ($heroCurrent === $heroBefore) {
    echo "HERO_IDENTICAL: YES\n";
} else {
    echo "HERO_IDENTICAL: NO\n";
}

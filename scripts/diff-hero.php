<?php
$current = json_decode(get_post_meta(21, '_elementor_data', true), true);
$before = json_decode(file_get_contents(__DIR__ . '/../content-source/before-visual-pass/post-21.json'), true);
$heroCurrent = $current[0];
$heroBefore = $before[0];

function diff($a, $b, $path = '') {
    if (is_array($a) && is_array($b)) {
        $keys = array_unique(array_merge(array_keys($a), array_keys($b)));
        foreach ($keys as $k) {
            if (!array_key_exists($k, $a)) {
                echo "Missing in A: $path/$k\n";
            } elseif (!array_key_exists($k, $b)) {
                echo "Missing in B: $path/$k\n";
            } else {
                diff($a[$k], $b[$k], "$path/$k");
            }
        }
    } else {
        if ($a !== $b) {
            echo "Diff at $path: A=" . json_encode($a) . " vs B=" . json_encode($b) . "\n";
        }
    }
}

diff($heroCurrent, $heroBefore);

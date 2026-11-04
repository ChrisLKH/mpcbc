<?php
$current = json_decode(get_post_meta(21, '_elementor_data', true), true);
$before = json_decode(file_get_contents(__DIR__ . '/../content-source/before-visual-pass/post-21.json'), true);

// Let's traverse heroCurrent and heroBefore and find all differences
function find_diffs($a, $b, $path = '') {
    $diffs = [];
    if (is_array($a) && is_array($b)) {
        $keys = array_unique(array_merge(array_keys($a), array_keys($b)));
        foreach ($keys as $k) {
            if (!array_key_exists($k, $a)) {
                $diffs[] = "Missing in curr: $path/$k";
            } elseif (!array_key_exists($k, $b)) {
                $diffs[] = "Missing in before: $path/$k";
            } else {
                $diffs = array_merge($diffs, find_diffs($a[$k], $b[$k], "$path/$k"));
            }
        }
    } else {
        if ($a !== $b) {
            $diffs[] = "Value diff at $path: curr=" . json_encode($a) . " vs before=" . json_encode($b);
        }
    }
    return $diffs;
}

$heroDiffs = find_diffs($current[0], $before[0]);
echo "Hero diffs count: " . count($heroDiffs) . "\n";
foreach ($heroDiffs as $d) {
    echo "$d\n";
}

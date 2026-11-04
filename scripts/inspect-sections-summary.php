<?php
$data = json_decode(file_get_contents('C:/Projects/mpcbc/content-source/before-visual-pass/21.json'), true);

foreach ($data as $i => $s) {
    echo "========================================\n";
    echo "Section $i: ID={$s['id']}, Type={$s['elType']}/" . ($s['widgetType'] ?? '') . "\n";
    $cssid = $s['settings']['_cssid']['value'] ?? ($s['settings']['_cssid'] ?? 'none');
    echo "  CSS ID: $cssid\n";
    if (isset($s['elements'])) {
        echo "  Direct children count: " . count($s['elements']) . "\n";
        foreach ($s['elements'] as $j => $c) {
            $c_type = $c['widgetType'] ?? $c['elType'];
            $c_id = $c['settings']['_cssid']['value'] ?? ($c['settings']['_cssid'] ?? $c['id']);
            echo "    [$j] $c_type ($c_id)\n";
        }
    }
}

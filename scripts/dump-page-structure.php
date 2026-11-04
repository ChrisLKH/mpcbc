<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

function inspect_elements($elements, $depth = 0) {
    foreach ($elements as $el) {
        $type = $el['elType'] ?? 'unknown';
        $widget = $el['widgetType'] ?? '';
        $id = $el['id'] ?? '';
        $indent = str_repeat('  ', $depth);
        $info = "$indent- [$type] $widget ($id)";
        if ($widget === 'heading') {
            $info .= ' : ' . substr($el['settings']['title'] ?? '', 0, 50);
        } elseif ($widget === 'text-editor') {
            $content = $el['settings']['editor'] ?? '';
            $hasStyle = strpos($content, 'style=') !== false ? ' [HAS STYLE]' : '';
            $hasDiv = strpos($content, '<div') !== false ? ' [HAS DIV]' : '';
            $info .= $hasStyle . $hasDiv . ' : ' . substr(strip_tags($content), 0, 50);
        } elseif ($widget === 'html') {
            $html = $el['settings']['html'] ?? '';
            $hasStyle = strpos($html, 'style=') !== false ? ' [HAS STYLE]' : '';
            $hasDiv = strpos($html, '<div') !== false ? ' [HAS DIV]' : '';
            $info .= " [HTML widget$hasStyle$hasDiv] : " . substr(strip_tags($html), 0, 50);
        }
        echo $info . PHP_EOL;
        if (!empty($el['elements'])) {
            inspect_elements($el['elements'], $depth + 1);
        }
    }
}

foreach ([21 => 'Home', 17 => 'Welcome', 18 => 'Prayer', 16 => 'Footer'] as $pid => $title) {
    echo "=== $title ($pid) ===\n";
    $raw = get_post_meta($pid, '_elementor_data', true);
    $data = json_decode($raw, true);
    if ($data) inspect_elements($data);
}

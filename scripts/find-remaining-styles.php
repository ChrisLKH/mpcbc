<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

function inspect_elements($elements, $page_name) {
    foreach ($elements as $el) {
        $id = $el['id'] ?? 'unknown';
        $elType = $el['elType'] ?? '';
        $widgetType = $el['widgetType'] ?? '';

        if (!empty($el['settings'])) {
            foreach ($el['settings'] as $k => $v) {
                if (is_string($v) && (strpos($v, 'style=') !== false || strpos($v, '<div') !== false)) {
                    echo "[{$page_name}] Element {$id} ({$elType}/{$widgetType}) setting '{$k}':\n";
                    echo "   " . substr($v, 0, 200) . "\n";
                }
            }
        }

        if (!empty($el['elements'])) {
            inspect_elements($el['elements'], $page_name);
        }
    }
}

foreach ([17 => 'Welcome', 21 => 'Home'] as $pid => $pname) {
    $data = json_decode(get_post_meta($pid, '_elementor_data', true), true);
    inspect_elements($data, $pname);
}

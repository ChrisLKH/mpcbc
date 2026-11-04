<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$home_data = json_decode(get_post_meta(21, '_elementor_data', true), true);

function validate_tree($elements, $path = 'root') {
    if (!is_array($elements)) {
        echo "[ERROR] Elements at {$path} is not array: " . var_export($elements, true) . "\n";
        return;
    }
    foreach ($elements as $k => $el) {
        $cur_path = "{$path}[{$k}]" . (isset($el['id']) ? "({$el['id']})" : '');
        if (!is_array($el)) {
            echo "[ERROR] Element at {$cur_path} is not array: " . var_export($el, true) . "\n";
            continue;
        }
        if (!isset($el['elType'])) {
            echo "[ERROR] Element at {$cur_path} missing 'elType': " . json_encode($el) . "\n";
        }
        if (!isset($el['id'])) {
            echo "[ERROR] Element at {$cur_path} missing 'id'\n";
        }
        if (isset($el['elements'])) {
            if (!is_array($el['elements'])) {
                echo "[ERROR] 'elements' at {$cur_path} is not array: " . var_export($el['elements'], true) . "\n";
            } else {
                validate_tree($el['elements'], $cur_path);
            }
        }
    }
}

validate_tree($home_data);
echo "Tree validation finished.\n";

<?php
require 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$welcome = get_page_by_path('welcome');
if (!$welcome) {
    echo "Welcome page not found\n";
    exit;
}

echo "Welcome page ID: " . $welcome->ID . "\n";
$data_raw = get_post_meta($welcome->ID, '_elementor_data', true);
$data = json_decode($data_raw, true);

// Search for eael-fancy-text
function find_fancy($elements) {
    foreach ($elements as $el) {
        if (isset($el['widgetType']) && $el['widgetType'] === 'eael-fancy-text') {
            echo "Found eael-fancy-text widget:\n";
            print_r($el['settings']);
            return;
        }
        if (!empty($el['elements'])) {
            find_fancy($el['elements']);
        }
    }
}

find_fancy($data);

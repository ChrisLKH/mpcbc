<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$plugin = 'duplicator/duplicator.php';

if (!is_plugin_active($plugin)) {
    $result = activate_plugin($plugin);
    if (is_wp_error($result)) {
        echo "Activation error: " . $result->get_error_message() . "\n";
    } else {
        echo "Duplicator activated successfully.\n";
    }
} else {
    echo "Duplicator is already active.\n";
}

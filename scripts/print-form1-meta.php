<?php
if ( ! defined( 'ABSPATH' ) ) exit(1);

global $wpdb;
$meta = $wpdb->get_results("SELECT meta_key, value FROM {$wpdb->prefix}fluentform_form_meta WHERE form_id = 1");
foreach ($meta as $m) {
    echo "=== {$m->meta_key} ===\n";
    echo $m->value . "\n\n";
}

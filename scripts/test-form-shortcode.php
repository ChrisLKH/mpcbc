<?php
if ( ! defined( 'ABSPATH' ) ) exit(1);

echo "Shortcode exists: " . (shortcode_exists('fluentform') ? "YES" : "NO") . "\n";

global $wpdb;
$forms = $wpdb->get_results("SELECT id, title, status FROM {$wpdb->prefix}fluentform_forms");
echo "Fluent Forms in DB:\n";
print_r($forms);

echo "\nExecuting shortcode [fluentform id=\"3\"]:\n";
$output = do_shortcode('[fluentform id="3"]');
echo "Length of output: " . strlen($output) . "\n";
echo "Output sample: " . substr($output, 0, 200) . "\n";

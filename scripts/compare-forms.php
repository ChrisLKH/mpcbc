<?php
if ( ! defined( 'ABSPATH' ) ) exit(1);

global $wpdb;
$f1 = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}fluentform_forms WHERE id = 1", ARRAY_A);
$f3 = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}fluentform_forms WHERE id = 3", ARRAY_A);

echo "--- FORM 1 (Working) ---\n";
foreach ($f1 as $k => $v) {
    if ($k === 'form_fields') echo "  $k: length " . strlen($v) . "\n";
    else echo "  $k: $v\n";
}

echo "\n--- FORM 3 (Imported) ---\n";
foreach ($f3 as $k => $v) {
    if ($k === 'form_fields') echo "  $k: length " . strlen($v) . "\n";
    else echo "  $k: $v\n";
}

$f1_meta = $wpdb->get_results("SELECT meta_key FROM {$wpdb->prefix}fluentform_form_meta WHERE form_id = 1");
$f3_meta = $wpdb->get_results("SELECT meta_key FROM {$wpdb->prefix}fluentform_form_meta WHERE form_id = 3");

echo "\nForm 1 meta keys:\n";
print_r(array_map(fn($m) => $m->meta_key, $f1_meta));

echo "\nForm 3 meta keys:\n";
print_r(array_map(fn($m) => $m->meta_key, $f3_meta));

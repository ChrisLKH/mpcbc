<?php
if ( ! defined( 'ABSPATH' ) ) exit(1);

global $wpdb;
$f1 = $wpdb->get_var("SELECT form_fields FROM {$wpdb->prefix}fluentform_forms WHERE id = 1");
$fields = json_decode($f1, true);
echo json_encode($fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

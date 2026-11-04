<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

global $wpdb;

$rows = $wpdb->get_results('SELECT id, type, name FROM wp_duplicator_entities');
print_r($rows);

// Also check duplicator_packages table
$pkgRows = $wpdb->get_results('SELECT id, name, status FROM wp_duplicator_backups');
print_r($pkgRows);

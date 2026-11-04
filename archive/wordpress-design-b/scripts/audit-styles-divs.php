<?php
/**
 * Audit all Elementor data for style= and <div
 * Both must be strictly 0.
 */
global $wpdb;

echo "=== AUDIT 1: Database _elementor_data ===\n";
$posts = $wpdb->get_results("SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data'");

$db_style_count = 0;
$db_div_count = 0;

foreach ($posts as $p) {
    $title = get_the_title($p->post_id);
    $data = $p->meta_value;
    
    $style_matches = substr_count($data, 'style=');
    $div_matches = substr_count($data, '<div');
    
    $db_style_count += $style_matches;
    $db_div_count += $div_matches;
    
    echo "Post ID {$p->post_id} ('$title'): style= count: $style_matches, <div count: $div_matches\n";
}

echo "\nTotal DB style= occurrences: $db_style_count\n";
echo "Total DB <div occurrences: $db_div_count\n";

echo "\n=== AUDIT 2: JSON Files in wordpress-design-b/elementor/ ===\n";
$json_files = glob('C:/Projects/mpcbc/wordpress-design-b/elementor/*.json');

$json_style_count = 0;
$json_div_count = 0;

foreach ($json_files as $f) {
    $content = file_get_contents($f);
    $style_matches = substr_count($content, 'style=');
    $div_matches = substr_count($content, '<div');
    
    $json_style_count += $style_matches;
    $json_div_count += $div_matches;
    
    echo basename($f) . ": style= count: $style_matches, <div count: $div_matches\n";
}

echo "\nTotal JSON style= occurrences: $json_style_count\n";
echo "Total JSON <div occurrences: $json_div_count\n";

if ($db_style_count === 0 && $db_div_count === 0 && $json_style_count === 0 && $json_div_count === 0) {
    echo "\n>>> SUCCESS: Both style= and <div are 0 across all pages and files! <<<\n";
} else {
    echo "\n>>> FAILED: Found occurrences of style= or <div! <<<\n";
}

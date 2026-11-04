<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

global $wpdb;
$rows = $wpdb->get_results("SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_data'");
foreach ($rows as $r) {
    $post = get_post($r->post_id);
    if (!$post || $post->post_status === 'trash') continue;
    $data = $r->meta_value;
    $styleCount = substr_count($data, 'style=');
    $divCount = substr_count($data, '<div');
    if ($styleCount > 0 || $divCount > 0) {
        echo "Post ID: {$r->post_id} ({$post->post_title} / {$post->post_name}) -> style=: {$styleCount}, <div: {$divCount}\n";
    }
}

<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$posts = get_posts([
    'post_type' => ['page', 'elementor_library'],
    'posts_per_page' => -1,
    'post_status' => 'publish',
]);

echo "Published pages and templates:\n";
foreach ($posts as $p) {
    $data = get_post_meta($p->ID, '_elementor_data', true);
    $styles = substr_count($data, 'style=');
    $divs = substr_count($data, '<div');
    echo "ID {$p->ID} ({$p->post_type}): {$p->post_title} ({$p->post_name}) -> styles: {$styles}, divs: {$divs}\n";
    if ($styles > 0) {
        // Find what contains style=
        preg_match_all('/style=[^"\'>\s]+|style="[^"]*"|style=\'[^\']*\'/i', $data, $matches);
        echo "   Sample styles: " . implode(' | ', array_slice($matches[0], 0, 3)) . "\n";
    }
}

<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$posts = get_posts(['post_type' => 'attachment', 'posts_per_page' => -1]);
echo "Attachment count: " . count($posts) . "\n";
foreach ($posts as $p) {
    echo $p->ID . ": " . $p->post_title . " -> " . wp_get_attachment_url($p->ID) . "\n";
}

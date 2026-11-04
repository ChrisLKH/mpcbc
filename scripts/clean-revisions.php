<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$revisions = get_posts([
    'post_type'      => 'revision',
    'posts_per_page' => -1,
    'post_status'    => 'any',
]);

echo "Found " . count($revisions) . " revisions. Deleting...\n";
foreach ($revisions as $rev) {
    wp_delete_post_revision($rev->ID);
}

echo "Revisions cleaned.\n";

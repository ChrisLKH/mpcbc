<?php
/**
 * Import all images to WordPress Media Library and create media-map.json
 */
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$mediaMap = [];

$filesToImport = [
    'logo.png' => 'C:/Projects/mpcbc/wordpress-design-b/design-reference/images/logo.png',
    'church-exterior.jpg' => 'C:/Projects/mpcbc/wordpress-design-b/design-reference/images/church-exterior.jpg',
    'church-sign.jpg' => 'C:/Projects/mpcbc/wordpress-design-b/design-reference/images/church-sign.jpg',
    'pastor-portrait.png' => 'C:/Projects/mpcbc/wordpress-design-b/placeholders/pastor-portrait.png',
    'pastor-cantonese.png' => 'C:/Projects/mpcbc/wordpress-design-b/placeholders/pastor-cantonese.png',
    'pastor-english.png' => 'C:/Projects/mpcbc/wordpress-design-b/placeholders/pastor-english.png',
    'pastor-mandarin.png' => 'C:/Projects/mpcbc/wordpress-design-b/placeholders/pastor-mandarin.png',
    'thumb-cantonese.png' => 'C:/Projects/mpcbc/wordpress-design-b/placeholders/thumb-cantonese.png',
    'thumb-english.png' => 'C:/Projects/mpcbc/wordpress-design-b/placeholders/thumb-english.png',
    'thumb-mandarin.png' => 'C:/Projects/mpcbc/wordpress-design-b/placeholders/thumb-mandarin.png',
    'kids-room.png' => 'C:/Projects/mpcbc/wordpress-design-b/placeholders/kids-room.png',
    'kids-craft.png' => 'C:/Projects/mpcbc/wordpress-design-b/placeholders/kids-craft.png',
    'kids-nursery.png' => 'C:/Projects/mpcbc/wordpress-design-b/placeholders/kids-nursery.png',
];

foreach ( $filesToImport as $key => $filePath ) {
    if ( ! file_exists( $filePath ) ) {
        echo "Missing file: $filePath\n";
        continue;
    }

    // Check if attachment with same title already exists
    $existing = get_posts([
        'post_type' => 'attachment',
        'title' => pathinfo( $key, PATHINFO_FILENAME ),
        'posts_per_page' => 1,
    ]);

    if ( ! empty( $existing ) ) {
        $attach_id = $existing[0]->ID;
        $url = wp_get_attachment_url( $attach_id );
        echo "Already imported $key -> ID $attach_id ($url)\n";
    } else {
        $file_array = [
            'name' => basename( $filePath ),
            'tmp_name' => $filePath,
        ];

        // Copy to temp file so wp_handle_sideload can move it
        $tmp = wp_tempnam( basename( $filePath ) );
        copy( $filePath, $tmp );
        $file_array['tmp_name'] = $tmp;

        $id = media_handle_sideload( $file_array, 0, pathinfo( $key, PATHINFO_FILENAME ) );

        if ( is_wp_error( $id ) ) {
            echo "Error importing $key: " . $id->get_error_message() . "\n";
            continue;
        }

        $attach_id = $id;
        $url = wp_get_attachment_url( $attach_id );
        echo "Imported $key -> ID $attach_id ($url)\n";
    }

    $mediaMap[ $key ] = [
        'id' => $attach_id,
        'url' => $url,
    ];
}

file_put_contents(
    'C:/Projects/mpcbc/wordpress-design-b/scripts/media-map.json',
    json_encode( $mediaMap, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES )
);
echo "Wrote media-map.json\n";

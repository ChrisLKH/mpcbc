<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

wp_set_current_user( 1 );

$map_file = 'C:/Projects/mpcbc/wordpress/scripts/media-map.json';
$map = json_decode( file_get_contents( $map_file ), true ) ?: [];

$new_files = [
    'live-thumb-english.png',
    'live-thumb-chinese.png',
];

$placeholders_dir = 'C:/Projects/mpcbc/wordpress/placeholders';
$upload_dir = wp_upload_dir();

foreach ( $new_files as $filename ) {
    $filepath = $placeholders_dir . '/' . $filename;
    if ( ! file_exists( $filepath ) ) {
        echo "File not found: $filepath\n";
        continue;
    }

    $target_path = $upload_dir['path'] . '/' . $filename;
    copy( $filepath, $target_path );

    $filetype = wp_check_filetype( $filename, null );
    $attachment = [
        'post_mime_type' => $filetype['type'],
        'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ];

    $attach_id = wp_insert_attachment( $attachment, $target_path );
    $attach_data = wp_generate_attachment_metadata( $attach_id, $target_path );
    wp_update_attachment_metadata( $attach_id, $attach_data );

    $map[ $filename ] = [
        'id'  => $attach_id,
        'url' => wp_get_attachment_url( $attach_id ),
    ];
    echo "Imported {$filename}: ID {$attach_id} => {$map[$filename]['url']}\n";
}

file_put_contents( $map_file, json_encode( $map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
echo "Updated media-map.json. Total items: " . count( $map ) . "\n";

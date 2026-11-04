<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

wp_set_current_user( 1 );

$placeholders_dir = 'C:/Projects/mpcbc/wordpress/placeholders';
$files = glob( $placeholders_dir . '/*.png' );

$upload_dir = wp_upload_dir();
$map = [];

foreach ( $files as $filepath ) {
    $filename = basename( $filepath );
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

file_put_contents( 'C:/Projects/mpcbc/wordpress/scripts/media-map.json', json_encode( $map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
echo "Saved media-map.json with " . count( $map ) . " items.\n";

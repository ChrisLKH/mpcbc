<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$raw = get_post_meta( 21, '_elementor_data', true );
$data = json_decode( $raw, true );

foreach ( $data as $sec ) {
    if ( $sec['id'] === 'events_messages_root' ) {
        echo "=== events_messages_root ===\n";
        echo json_encode( $sec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n\n";
    }
    if ( $sec['id'] === 'stay_loop_root' ) {
        echo "=== stay_loop_root ===\n";
        echo json_encode( $sec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n\n";
    }
}

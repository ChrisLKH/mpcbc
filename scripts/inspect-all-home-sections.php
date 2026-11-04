<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$raw = get_post_meta( 21, '_elementor_data', true );
$data = json_decode( $raw, true );

foreach ( $data as $i => $sec ) {
    echo "=== [$i] {$sec['id']} ===\n";
    echo "elType: {$sec['elType']}\n";
    echo "settings: " . json_encode( $sec['settings'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n\n";
}

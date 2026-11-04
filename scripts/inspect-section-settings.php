<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$raw = get_post_meta( 21, '_elementor_data', true );
$data = json_decode( $raw, true );

foreach ( $data as $i => $sec ) {
    $id = $sec['id'];
    echo "--- Section [$i] ($id) Settings ---\n";
    print_r( $sec['settings'] );
    if ( $i >= 3 ) break; // first few are enough
}

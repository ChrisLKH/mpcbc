<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$raw = get_post_meta( 21, '_elementor_data', true );
$data = json_decode( $raw, true );

echo "Home (post 21) top-level sections count: " . count( $data ) . PHP_EOL;
foreach ( $data as $i => $sec ) {
    $id = $sec['id'] ?? 'no-id';
    $elType = $sec['elType'] ?? 'no-type';
    $customId = $sec['settings']['_element_id'] ?? '';
    $title = $sec['settings']['_title'] ?? '';
    echo "Section [$i]: id=$id, elType=$elType, css_id='$customId', title='$title'" . PHP_EOL;
}

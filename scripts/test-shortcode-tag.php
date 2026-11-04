<?php
if ( ! defined( 'ABSPATH' ) ) exit(1);

global $shortcode_tags;
$callback = $shortcode_tags['fluentform'];
foreach ([1, 2, 3, 4] as $id) {
    $res = call_user_func( $callback, ['id' => $id], '' );
    echo "Form $id returned length: " . strlen($res) . "\n";
}

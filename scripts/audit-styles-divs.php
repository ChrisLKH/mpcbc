<?php
/**
 * Counts `style=` and `<div` in every live (non-revision) Elementor post (Home, Footer, templates, pages).
 * Both totals must be 0 for Home (post 21) and the Footer template (post 16).
 * Run: php audit-styles-divs.php   (bootstraps wp-load.php like the other scripts)
 */
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
define( 'WP_HTTP_BLOCK_EXTERNAL', true );
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';
global $wpdb;
$rows = $wpdb->get_results( "SELECT m.post_id, m.meta_value FROM {$wpdb->postmeta} m JOIN {$wpdb->posts} p ON p.ID = m.post_id WHERE m.meta_key = '_elementor_data' AND p.post_type <> 'revision'" );
$total = [ 0, 0 ];
foreach ( $rows as $r ) {
	$s = substr_count( $r->meta_value, 'style=' );
	$d = substr_count( $r->meta_value, '<div' );
	echo str_pad( $r->post_id, 5 ) . str_pad( get_the_title( $r->post_id ), 32 ) . " style= $s   <div $d\n";
	$total[0] += $s;
	$total[1] += $d;
}
echo "TOTAL style= {$total[0]}   <div {$total[1]}\n";

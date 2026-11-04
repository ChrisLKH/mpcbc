<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit(1);
}

wp_set_current_user( 1 );

echo "=== STARTING VOLUNTEER TEST ===\n\n";

// 1. Change hero headline
echo "Test 1: Changing hero headline in Elementor data...\n";
$home = get_page_by_path( 'home' );
$home_data = json_decode( get_post_meta( $home->ID, '_elementor_data', true ), true );

// Find hero heading
$original_headline = $home_data[0]['elements'][0]['settings']['title'];
echo "Original headline: '$original_headline'\n";
$home_data[0]['elements'][0]['settings']['title'] = 'All are welcome here.';
update_post_meta( $home->ID, '_elementor_data', wp_slash( json_encode( $home_data ) ) );
Elementor\Plugin::$instance->files_manager->clear_cache();

$live_html = file_get_contents( 'http://mpcbc.local/' );
$t1_pass = strpos( $live_html, 'All are welcome here.' ) !== false;
echo "Live site shows 'All are welcome here.': " . ($t1_pass ? "PASSED" : "FAILED") . "\n";

// Restore original headline
$home_data[0]['elements'][0]['settings']['title'] = 'You’re welcome here.';
update_post_meta( $home->ID, '_elementor_data', wp_slash( json_encode( $home_data ) ) );
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Restored original headline.\n\n";

// 2. Change service time
echo "Test 2: Changing Cantonese service time...\n";
$times_container = &$home_data[1]['elements'][0]['elements'][1]['elements'][1]['settings']['editor'];
$times_container = str_replace( '9:30', '9:45', $times_container );
update_post_meta( $home->ID, '_elementor_data', wp_slash( json_encode( $home_data ) ) );
Elementor\Plugin::$instance->files_manager->clear_cache();

$live_html = file_get_contents( 'http://mpcbc.local/' );
$t2_pass = strpos( $live_html, '9:45' ) !== false;
echo "Live site shows '9:45': " . ($t2_pass ? "PASSED" : "FAILED") . "\n";

// Restore original service time
$times_container = str_replace( '9:45', '9:30', $times_container );
update_post_meta( $home->ID, '_elementor_data', wp_slash( json_encode( $home_data ) ) );
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Restored original service time.\n\n";

// 3. Change card photo / background
echo "Test 3: Changing card background color/image...\n";
$card_en = &$home_data[2]['elements'][1]['elements'][0]['settings'];
$orig_bg = $card_en['background_color'];
$card_en['background_color'] = '#553322';
update_post_meta( $home->ID, '_elementor_data', wp_slash( json_encode( $home_data ) ) );
Elementor\Plugin::$instance->files_manager->clear_cache();
$live_html = file_get_contents( 'http://mpcbc.local/' );

$post_css_file = 'C:\Users\chris\Local Sites\mpcbc\app\public\wp-content\uploads\elementor\css\post-' . $home->ID . '.css';
$live_css = file_exists( $post_css_file ) ? file_get_contents( $post_css_file ) : $live_html;
$t3_pass = strpos( $live_css, '#553322' ) !== false || strpos( $live_html, '#553322' ) !== false;
echo "Updated card style in CSS: " . ($t3_pass ? "PASSED" : "FAILED") . "\n";

// Restore card background
$card_en['background_color'] = $orig_bg;
update_post_meta( $home->ID, '_elementor_data', wp_slash( json_encode( $home_data ) ) );
Elementor\Plugin::$instance->files_manager->clear_cache();
file_get_contents( 'http://mpcbc.local/' );
echo "Restored original card background.\n\n";

// 4. Change menu item in Appearance > Menus
echo "Test 4: Changing menu item navigation label...\n";
$menu = wp_get_nav_menu_object( 'Primary Menu' );
$items = wp_get_nav_menu_items( $menu->term_id );
$sundays_item = null;
foreach ( $items as $it ) {
    if ( $it->title === 'Sundays' ) {
        $sundays_item = $it;
        break;
    }
}
if ( $sundays_item ) {
    wp_update_nav_menu_item( $menu->term_id, $sundays_item->ID, [
        'menu-item-title'  => 'Sunday Services',
        'menu-item-status' => 'publish',
    ] );
    $live_html = file_get_contents( 'http://mpcbc.local/' );
    $t4_pass = strpos( $live_html, 'Sunday Services' ) !== false;
    echo "Live site shows 'Sunday Services': " . ($t4_pass ? "PASSED" : "FAILED") . "\n";

    // Restore menu item
    wp_update_nav_menu_item( $menu->term_id, $sundays_item->ID, [
        'menu-item-title'  => 'Sundays',
        'menu-item-status' => 'publish',
    ] );
    echo "Restored original menu item label.\n\n";
} else {
    echo "Could not find Sundays menu item\n";
    $t4_pass = false;
}

// 5. Change Primary Global Colour in Site Settings (Kit 5)
echo "Test 5: Changing Primary Global Colour to Plum (#7A2E5E)...\n";
$kit = Elementor\Plugin::$instance->kits_manager->get_active_kit();
$page_settings = get_post_meta( $kit->get_id(), '_elementor_page_settings', true );
$page_settings['system_colors'][0]['color'] = '#7A2E5E';
update_post_meta( $kit->get_id(), '_elementor_page_settings', $page_settings );
Elementor\Plugin::$instance->files_manager->clear_cache();

// Request page via HTTP to trigger CSS compilation
$html = file_get_contents( 'http://mpcbc.local/' );
$kit_css_file = 'C:\Users\chris\Local Sites\mpcbc\app\public\wp-content\uploads\elementor\css\post-5.css';
$live_kit_css = file_exists( $kit_css_file ) ? file_get_contents( $kit_css_file ) : $html;
$t5_pass = strpos( $live_kit_css, '#7A2E5E' ) !== false;
echo "Kit CSS updated with new Primary color #7A2E5E: " . ($t5_pass ? "PASSED" : "FAILED") . "\n";

// Restore Primary color back to Evergreen #0E6B5C
$page_settings['system_colors'][0]['color'] = '#0E6B5C';
update_post_meta( $kit->get_id(), '_elementor_page_settings', $page_settings );
Elementor\Plugin::$instance->files_manager->clear_cache();
file_get_contents( 'http://mpcbc.local/' );
echo "Restored Primary Global Colour to Evergreen #0E6B5C.\n\n";

echo "=== VOLUNTEER TEST SUMMARY ===\n";
echo "1. Hero headline change: " . ($t1_pass ? "PASS" : "FAIL") . "\n";
echo "2. Service time change: " . ($t2_pass ? "PASS" : "FAIL") . "\n";
echo "3. Card photo change: " . ($t3_pass ? "PASS" : "FAIL") . "\n";
echo "4. Menu item change: " . ($t4_pass ? "PASS" : "FAIL") . "\n";
echo "5. Primary colour change: " . ($t5_pass ? "PASS" : "FAIL") . "\n";

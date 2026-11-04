<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
define('DISABLE_WP_CRON', true);
define('WP_HTTP_BLOCK_EXTERNAL', true);
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';
wp_set_current_user( 1 );

if ( ! did_action( 'elementor/loaded' ) ) {
    exit(1);
}

$kit = Elementor\Plugin::$instance->kits_manager->get_active_kit();

// Update system colors: Harbor palette
$system_colors = [
    [
        '_id'   => 'primary',
        'title' => 'Harbor',
        'color' => '#155E75',
    ],
    [
        '_id'   => 'secondary',
        'title' => 'Deep sea',
        'color' => '#0F2A33',
    ],
    [
        '_id'   => 'text',
        'title' => 'Ink',
        'color' => '#13232A',
    ],
    [
        '_id'   => 'accent',
        'title' => 'Persimmon',
        'color' => '#F28C38',
    ],
];

// Custom colors
$custom_colors = [
    [
        '_id'   => 'paper',
        'title' => 'Paper',
        'color' => '#F6F7F5',
    ],
    [
        '_id'   => 'mist',
        'title' => 'Mist',
        'color' => '#E3EBEA',
    ],
    [
        '_id'   => 'stone',
        'title' => 'Stone (legacy)',
        'color' => '#E3EBEA',
    ],
    [
        '_id'   => 'ink_soft',
        'title' => 'Ink soft',
        'color' => '#4F6168',
    ],
    [
        '_id'   => 'harbor_deep',
        'title' => 'Harbor deep',
        'color' => '#0E4A5C',
    ],
    [
        '_id'   => 'persimmon_soft',
        'title' => 'Persimmon soft',
        'color' => '#FCE3CC',
    ],
    [
        '_id'   => 'on_persimmon',
        'title' => 'On persimmon',
        'color' => '#2B1404',
    ],
    [
        '_id'   => 'persimmon_hover',
        'title' => 'Persimmon hover',
        'color' => '#E07A26',
    ],
    [
        '_id'   => 'tick',
        'title' => 'Tick',
        'color' => '#C2600F',
    ],
    [
        '_id'   => 'live',
        'title' => 'Live',
        'color' => '#D92D27',
    ],
    [
        '_id'   => 'live_text',
        'title' => 'Live text',
        'color' => '#B8231E',
    ],
    [
        '_id'   => 'card_tint',
        'title' => 'Card tint',
        'color' => '#EAF2F3',
    ],
    [
        '_id'   => 'line',
        'title' => 'Line',
        'color' => '#D9E2E1',
    ],
    [
        '_id'   => 'line_strong',
        'title' => 'Line strong',
        'color' => '#BFCDCC',
    ],
    [
        '_id'   => 'footer',
        'title' => 'Footer',
        'color' => '#0A1F26',
    ],
    [
        '_id'   => 'white',
        'title' => 'White',
        'color' => '#FFFFFF',
    ],
];

$page_settings = get_post_meta( $kit->get_id(), '_elementor_page_settings', true );
if ( ! is_array( $page_settings ) ) {
    $page_settings = [];
}
$page_settings['system_colors'] = $system_colors;
$page_settings['custom_colors'] = $custom_colors;
update_post_meta( $kit->get_id(), '_elementor_page_settings', $page_settings );

// Clear Elementor CSS cache
Elementor\Plugin::$instance->files_manager->clear_cache();

echo "Global Colors updated successfully to Harbor palette!\n";

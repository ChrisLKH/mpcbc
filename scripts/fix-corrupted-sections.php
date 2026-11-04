<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

wp_set_current_user( 1 );

$media_map = json_decode( file_get_contents( 'C:/Projects/mpcbc/wordpress/scripts/media-map.json' ), true );

// 1. FIX FIND YOUR PLACE
$fyp_path = 'C:/Projects/mpcbc/wordpress/elementor/home-find-your-place.json';
$fyp = json_decode( file_get_contents( $fyp_path ), true );
$fyp_root =& $fyp['content'][0];

// Keep only first 2 elements of fyp_root: [ find_place_header_row, find_place_cards_grid ]
$header_row = $fyp_root['elements'][0];
$cards_grid = $fyp_root['elements'][1];

// Clean cards_grid settings
$cards_grid['settings'] = [
    'content_width'         => 'full',
    'flex_direction'        => 'row',
    'flex_wrap'             => 'wrap',
    'flex_justify_content'  => 'space-between',
    'flex_gap'              => [ 'unit' => 'px', 'size' => 16 ],
    'flex_direction_tablet' => 'row',
    'flex_direction_mobile' => 'column',
];

// In cards_grid['elements'], keep only first 4 cards
$cards_grid['elements'] = array_slice( $cards_grid['elements'], 0, 4 );

// Ensure each card has clean elements
foreach ($cards_grid['elements'] as &$card) {
    // Keep first 4 elements of each card: title, meta, desc, link
    $card['elements'] = array_values(array_filter($card['elements'], function($el) {
        return isset($el['elType']);
    }));
}

$fyp_root['elements'] = [ $header_row, $cards_grid ];
file_put_contents( $fyp_path, json_encode( $fyp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Fixed home-find-your-place.json\n";

// 2. FIX PASTOR & PRAYER
$pp_path = 'C:/Projects/mpcbc/wordpress/elementor/home-pastor-prayer.json';
$pp = json_decode( file_get_contents( $pp_path ), true );
$pp_root =& $pp['content'][0];

$col_img =& $pp_root['elements'][0];
$col_quote =& $pp_root['elements'][1];

// Reset col_img elements to strictly the single image widget
$col_img['elements'] = [
    [
        'id'         => 'pastor_portrait_img',
        'elType'     => 'widget',
        'widgetType' => 'image',
        'settings'   => [
            'image'               => [
                'url' => $media_map['pastor-portrait.png']['url'] ?? 'http://mpcbc.local/wp-content/uploads/2026/10/pastor-portrait.png',
                'id'  => $media_map['pastor-portrait.png']['id'] ?? 80,
            ],
            'width'               => [ 'unit' => 'px', 'size' => 200 ],
            'height'              => [ 'unit' => 'px', 'size' => 240 ],
            'image_border_radius' => [
                'unit' => 'px', 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16, 'isLinked' => true
            ],
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
];

// Clean quote card elements
$pp_root['elements'] = [ $col_img, $col_quote ];
file_put_contents( $pp_path, json_encode( $pp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Fixed home-pastor-prayer.json\n";

// 3. RE-MERGE HOME
$home_sections = [
    'hero'            => 'C:/Projects/mpcbc/wordpress/elementor/home-hero.json',
    'times_card'      => 'C:/Projects/mpcbc/wordpress/elementor/home-times-card.json',
    'chinese_band'    => 'C:/Projects/mpcbc/wordpress/elementor/home-chinese-band.json',
    'find_your_place' => 'C:/Projects/mpcbc/wordpress/elementor/home-find-your-place.json',
    'pastor_prayer'   => 'C:/Projects/mpcbc/wordpress/elementor/home-pastor-prayer.json',
    'kids'            => 'C:/Projects/mpcbc/wordpress/elementor/home-kids.json',
    'events_messages' => 'C:/Projects/mpcbc/wordpress/elementor/home-events-messages.json',
    'stay_in_loop'    => 'C:/Projects/mpcbc/wordpress/elementor/home-stay-in-loop.json',
];

$merged_content = [];
foreach ($home_sections as $sec_name => $sec_file) {
    $sec_data = json_decode( file_get_contents( $sec_file ), true );
    if ( ! empty( $sec_data['content'][0] ) ) {
        $merged_content[] = $sec_data['content'][0];
    }
}

$home_full = [
    'version'       => '0.4',
    'title'         => 'MPCBC – Home',
    'type'          => 'page',
    'page_settings' => [],
    'content'       => $merged_content,
];

file_put_contents( 'C:/Projects/mpcbc/wordpress/elementor/home-full.json', json_encode( $home_full, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
update_post_meta( 21, '_elementor_data', wp_slash( json_encode( $merged_content ) ) );
echo "Merged Home (Post 21).\n";

Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Done.\n";

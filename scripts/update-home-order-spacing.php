<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

wp_set_current_user( 1 );

$raw = get_post_meta( 21, '_elementor_data', true );
$data = json_decode( $raw, true );

// Index map by ID
$sections = [];
foreach ( $data as $s ) {
    $sections[ $s['id'] ] = $s;
}

echo "Found sections in post 21:\n";
foreach ( array_keys( $sections ) as $k ) {
    echo " - $k\n";
}

// -------------------------------------------------------------
// 0. Hero container
// -------------------------------------------------------------
$hero = $sections['hero_container'];
$hero['settings']['content_width'] = 'full';
$hero['settings']['min_height'] = [ 'unit' => 'px', 'size' => 760 ];
$hero['settings']['min_height_tablet'] = [ 'unit' => 'px', 'size' => 640 ];
$hero['settings']['min_height_mobile'] = [ 'unit' => 'vh', 'size' => 88 ];
$hero['settings']['padding'] = [ 'unit' => 'px', 'top' => '120', 'right' => '56', 'bottom' => '112', 'left' => '56', 'isLinked' => false ];
$hero['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '96', 'right' => '40', 'bottom' => '88', 'left' => '40', 'isLinked' => false ];
$hero['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '80', 'right' => '16', 'bottom' => '80', 'left' => '16', 'isLinked' => false ];

// -------------------------------------------------------------
// 1. Sunday times card
// -------------------------------------------------------------
$times = $sections['times_outer_section'];
$times['settings']['content_width'] = 'boxed';
$times['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$times['settings']['padding'] = [ 'unit' => 'px', 'top' => '0', 'right' => '56', 'bottom' => '0', 'left' => '56', 'isLinked' => false ];
$times['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '0', 'left' => '40', 'isLinked' => false ];
$times['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '0', 'right' => '16', 'bottom' => '0', 'left' => '16', 'isLinked' => false ];
// Negative top margin overlaps hero bottom by 56px
// Negative bottom margin pulls Watch live up so no paper strip shows between Hero and Watch live
$times['settings']['margin'] = [ 'unit' => 'px', 'top' => '-56', 'right' => 'auto', 'bottom' => '-128', 'left' => 'auto', 'isLinked' => false ];
$times['settings']['margin_tablet'] = [ 'unit' => 'px', 'top' => '-56', 'right' => 'auto', 'bottom' => '-335', 'left' => 'auto', 'isLinked' => false ];
$times['settings']['margin_mobile'] = [ 'unit' => 'px', 'top' => '-56', 'right' => 'auto', 'bottom' => '-500', 'left' => 'auto', 'isLinked' => false ];
$times['settings']['z_index'] = 2;

// -------------------------------------------------------------
// 2. Watch live + Latest messages
// -------------------------------------------------------------
$live = $sections['live_section_root'];
$live['settings']['_element_id'] = 'live';
$live['settings']['content_width'] = 'full';
$live['settings']['background_background'] = 'classic';
$live['settings']['background_color'] = '#EDE6DA'; // Stone
$live['settings']['__globals__'] = [
    'background_color' => 'globals/colors?id=stone',
];
// Desktop top padding 160px: 113px times card overlap + 48px breathing room
$live['settings']['padding'] = [ 'unit' => 'px', 'top' => '160', 'right' => '0', 'bottom' => '96', 'left' => '0', 'isLinked' => false ];
$live['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '380', 'right' => '0', 'bottom' => '64', 'left' => '0', 'isLinked' => false ];
$live['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '600', 'right' => '0', 'bottom' => '48', 'left' => '0', 'isLinked' => false ];

// Inner container boxed at 1328px with side padding
$live_inner = &$live['elements'][0];
$live_inner['settings']['content_width'] = 'boxed';
$live_inner['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$live_inner['settings']['padding'] = [ 'unit' => 'px', 'top' => '0', 'right' => '56', 'bottom' => '0', 'left' => '56', 'isLinked' => false ];
$live_inner['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '0', 'left' => '40', 'isLinked' => false ];
$live_inner['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '0', 'right' => '16', 'bottom' => '0', 'left' => '16', 'isLinked' => false ];
$live_inner['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 48 ];

// In Header: Watch live -> text color, 線上直播 -> ink soft
$live_header_group = &$live_inner['elements'][0];
$live_header_title = &$live_header_group['elements'][0];
$live_header_title['settings']['title_color'] = '#16140F';
$live_header_title['settings']['__globals__'] = [ 'title_color' => 'globals/colors?id=text' ];

$live_header_zh = &$live_header_group['elements'][1];
$live_header_zh['settings']['title_color'] = '#5C564D';
$live_header_zh['settings']['__globals__'] = [ 'title_color' => 'globals/colors?id=ink-soft' ];

// In Columns: English and Chinese
$live_cols = &$live_inner['elements'][1];
$live_col_en = &$live_cols['elements'][0];
$live_col_en['elements'][1]['settings']['title_color'] = '#16140F'; // Heading English
$live_col_en['elements'][1]['settings']['__globals__'] = [ 'title_color' => 'globals/colors?id=text' ];
$live_col_en['elements'][2]['settings']['text_color'] = '#5C564D';  // Text Sundays 11:00 AM
$live_col_en['elements'][2]['settings']['__globals__'] = [ 'text_color' => 'globals/colors?id=ink-soft' ];

// English Button outline-dark
$btn_en = &$live_col_en['elements'][3];
$btn_en['settings']['button_text_color'] = '#16140F';
$btn_en['settings']['border_color'] = 'rgba(22, 20, 15, 0.4)';
$btn_en['settings']['background_color'] = 'transparent';
$btn_en['settings']['hover_color'] = '#16140F';
$btn_en['settings']['button_background_hover_color'] = 'rgba(22, 20, 15, 0.08)';
$btn_en['settings']['border_hover_color'] = '#16140F';

// Chinese Column
$live_col_zh = &$live_cols['elements'][1];
$live_col_zh['elements'][1]['settings']['title_color'] = '#16140F'; // Heading 中文 粵語 · 國語
$live_col_zh['elements'][1]['settings']['__globals__'] = [ 'title_color' => 'globals/colors?id=text' ];
$live_col_zh['elements'][2]['settings']['text_color'] = '#5C564D';  // Text 粵語 9:30 AM · 國語 11:30 AM
$live_col_zh['elements'][2]['settings']['__globals__'] = [ 'text_color' => 'globals/colors?id=ink-soft' ];

// Chinese Button outline-dark
$btn_zh = &$live_col_zh['elements'][3];
$btn_zh['settings']['button_text_color'] = '#16140F';
$btn_zh['settings']['border_color'] = 'rgba(22, 20, 15, 0.4)';
$btn_zh['settings']['background_color'] = 'transparent';
$btn_zh['settings']['hover_color'] = '#16140F';
$btn_zh['settings']['button_background_hover_color'] = 'rgba(22, 20, 15, 0.08)';
$btn_zh['settings']['border_hover_color'] = '#16140F';

// Extract the 3 sermon rows and link from events_messages_root
$events_messages = $sections['events_messages_root'];
$sermon_elements = $events_messages['elements'][0]['elements'][1]['elements']; // col_latest_messages elements: heading, sermon_1, sermon_2, sermon_3, link

// Create latest messages group in live_inner
$latest_messages_wrap = [
    'id'       => 'live_latest_messages_wrap',
    'elType'   => 'container',
    'isInner'  => true,
    'settings' => [
        'content_width'  => 'full',
        'flex_direction' => 'column',
        'flex_gap'       => [ 'unit' => 'px', 'size' => 24 ],
        'margin'         => [ 'unit' => 'px', 'top' => '24', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
    ],
    'elements' => $sermon_elements,
];

// Heading in latest_messages_wrap: "Latest messages"
$latest_messages_wrap['elements'][0]['settings']['title_color'] = '#16140F';
$latest_messages_wrap['elements'][0]['settings']['__globals__'] = [ 'title_color' => 'globals/colors?id=text' ];
$latest_messages_wrap['elements'][0]['settings']['typography_font_size'] = [ 'unit' => 'px', 'size' => 32 ];
$latest_messages_wrap['elements'][0]['settings']['margin'] = [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '8', 'left' => '0', 'isLinked' => false ];

// Remove any existing latest_messages_wrap before adding
$filtered_live_inner = [];
foreach ( $live_inner['elements'] as $el ) {
    if ( ( $el['id'] ?? '' ) !== 'live_latest_messages_wrap' ) {
        $filtered_live_inner[] = $el;
    }
}
$filtered_live_inner[] = $latest_messages_wrap;
$live_inner['elements'] = $filtered_live_inner;

// -------------------------------------------------------------
// 3. Find your place
// -------------------------------------------------------------
$find_place = $sections['find_place_root'];
$find_place['settings']['content_width'] = 'boxed';
$find_place['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$find_place['settings']['padding'] = [ 'unit' => 'px', 'top' => '96', 'right' => '56', 'bottom' => '96', 'left' => '56', 'isLinked' => false ];
$find_place['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '64', 'right' => '40', 'bottom' => '64', 'left' => '40', 'isLinked' => false ];
$find_place['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '48', 'right' => '16', 'bottom' => '48', 'left' => '16', 'isLinked' => false ];
$find_place['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 32 ]; // heading to content gap 32px

// Cards row inside find your place
if ( isset( $find_place['elements'][1] ) ) {
    $find_place['elements'][1]['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 24 ]; // gap between cards 24px
}

// -------------------------------------------------------------
// 4. Kids
// -------------------------------------------------------------
$kids = $sections['kids_root'];
$kids['settings']['content_width'] = 'full';
$kids['settings']['padding'] = [ 'unit' => 'px', 'top' => '96', 'right' => '0', 'bottom' => '96', 'left' => '0', 'isLinked' => false ];
$kids['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '64', 'right' => '0', 'bottom' => '64', 'left' => '0', 'isLinked' => false ];
$kids['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '48', 'right' => '0', 'bottom' => '48', 'left' => '0', 'isLinked' => false ];

$kids_inner = &$kids['elements'][0];
$kids_inner['settings']['content_width'] = 'boxed';
$kids_inner['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$kids_inner['settings']['padding'] = [ 'unit' => 'px', 'top' => '0', 'right' => '56', 'bottom' => '0', 'left' => '56', 'isLinked' => false ];
$kids_inner['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '0', 'left' => '40', 'isLinked' => false ];
$kids_inner['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '0', 'right' => '16', 'bottom' => '0', 'left' => '16', 'isLinked' => false ];
$kids_inner['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 24 ]; // gap between content and photos

// Inside kids content column
$kids_col_content = &$kids_inner['elements'][0];
$kids_col_content['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 24 ];
// Tiles row
if ( isset( $kids_col_content['elements'][2] ) ) {
    $kids_col_content['elements'][2]['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 24 ];
}

// -------------------------------------------------------------
// 5. Pastor + prayer
// -------------------------------------------------------------
$pastor = $sections['pastor_root'];
$pastor['settings']['content_width'] = 'boxed';
$pastor['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$pastor['settings']['padding'] = [ 'unit' => 'px', 'top' => '96', 'right' => '56', 'bottom' => '96', 'left' => '56', 'isLinked' => false ];
$pastor['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '64', 'right' => '40', 'bottom' => '64', 'left' => '40', 'isLinked' => false ];
$pastor['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '48', 'right' => '16', 'bottom' => '48', 'left' => '16', 'isLinked' => false ];
$pastor['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 48 ];

// -------------------------------------------------------------
// 6. Merged "Coming up" + "Stay in the loop"
// -------------------------------------------------------------
$col_coming_up = $events_messages['elements'][0]['elements'][0]; // col_coming_up
$col_coming_up['settings']['width'] = [ 'unit' => '%', 'size' => 50 ];
$col_coming_up['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$col_coming_up['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];
$col_coming_up['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 24 ]; // gap between event rows

// Heading Coming up
$col_coming_up['elements'][0]['settings']['margin'] = [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '8', 'left' => '0', 'isLinked' => false ];

// Build Right Column (Stay in the loop: heading, form embed, social links)
$stay_loop = $sections['stay_loop_root'];
$stay_loop_title = $stay_loop['elements'][0]['elements'][0];   // heading "Stay in the loop"
$stay_loop_socials = $stay_loop['elements'][0]['elements'][1]; // icon-list
$stay_loop_form = $stay_loop['elements'][1]['elements'][0];    // shortcode [fluentform id="4"]

$stay_loop_title['settings']['typography_font_size'] = [ 'unit' => 'px', 'size' => 44 ];
$stay_loop_title['settings']['typography_font_size_mobile'] = [ 'unit' => 'px', 'size' => 32 ];
$stay_loop_title['settings']['margin'] = [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '8', 'left' => '0', 'isLinked' => false ];

$col_stay_loop = [
    'id'       => 'merged_col_stay_loop',
    'elType'   => 'container',
    'isInner'  => true,
    'settings' => [
        'width'          => [ 'unit' => '%', 'size' => 50 ],
        'width_tablet'   => [ 'unit' => '%', 'size' => 100 ],
        'width_mobile'   => [ 'unit' => '%', 'size' => 100 ],
        'flex_direction' => 'column',
        'flex_gap'       => [ 'unit' => 'px', 'size' => 24 ],
        'content_width'  => 'full',
    ],
    'elements' => [
        $stay_loop_title,
        $stay_loop_form,
        $stay_loop_socials,
    ],
];

$merged_events_loop = [
    'id'       => 'events_loop_root',
    'elType'   => 'container',
    'isInner'  => false,
    'settings' => [
        'content_width'         => 'full',
        'background_background' => 'classic',
        'background_color'      => '#EDE6DA', // Stone
        '__globals__'           => [
            'background_color' => 'globals/colors?id=stone',
        ],
        'padding'               => [ 'unit' => 'px', 'top' => '96', 'right' => '0', 'bottom' => '96', 'left' => '0', 'isLinked' => false ],
        'padding_tablet'        => [ 'unit' => 'px', 'top' => '64', 'right' => '0', 'bottom' => '64', 'left' => '0', 'isLinked' => false ],
        'padding_mobile'        => [ 'unit' => 'px', 'top' => '48', 'right' => '0', 'bottom' => '48', 'left' => '0', 'isLinked' => false ],
        'flex_direction'        => 'column',
        'flex_align_items'      => 'center',
    ],
    'elements' => [
        [
            'id'       => 'events_loop_inner',
            'elType'   => 'container',
            'isInner'  => true,
            'settings' => [
                'content_width'         => 'boxed',
                'boxed_width'           => [ 'unit' => 'px', 'size' => 1328 ],
                'padding'               => [ 'unit' => 'px', 'top' => '0', 'right' => '56', 'bottom' => '0', 'left' => '56', 'isLinked' => false ],
                'padding_tablet'        => [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '0', 'left' => '40', 'isLinked' => false ],
                'padding_mobile'        => [ 'unit' => 'px', 'top' => '0', 'right' => '16', 'bottom' => '0', 'left' => '16', 'isLinked' => false ],
                'flex_direction'        => 'row',
                'flex_direction_tablet' => 'column',
                'flex_direction_mobile' => 'column',
                'flex_gap'              => [ 'unit' => 'px', 'size' => 56 ],
                'flex_align_items'      => 'flex-start',
                'width'                 => [ 'unit' => '%', 'size' => 100 ],
            ],
            'elements' => [
                $col_coming_up,
                $col_stay_loop,
            ],
        ],
    ],
];

// -------------------------------------------------------------
// 7. 中文 strip (slimmed to 48px top/bottom)
// -------------------------------------------------------------
$chinese_band = $sections['chinese_band_root'];
$chinese_band['settings']['content_width'] = 'full';
$chinese_band['settings']['padding'] = [ 'unit' => 'px', 'top' => '48', 'right' => '0', 'bottom' => '48', 'left' => '0', 'isLinked' => false ];
$chinese_band['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '48', 'right' => '0', 'bottom' => '48', 'left' => '0', 'isLinked' => false ];
$chinese_band['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '48', 'right' => '0', 'bottom' => '48', 'left' => '0', 'isLinked' => false ];

if ( isset( $chinese_band['elements'][0] ) ) {
    $chinese_band['elements'][0]['settings']['content_width'] = 'boxed';
    $chinese_band['elements'][0]['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
    $chinese_band['elements'][0]['settings']['padding'] = [ 'unit' => 'px', 'top' => '0', 'right' => '56', 'bottom' => '0', 'left' => '56', 'isLinked' => false ];
    $chinese_band['elements'][0]['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '0', 'left' => '40', 'isLinked' => false ];
    $chinese_band['elements'][0]['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '0', 'right' => '16', 'bottom' => '0', 'left' => '16', 'isLinked' => false ];
}

// -------------------------------------------------------------
// Final Ordered Array of Sections
// -------------------------------------------------------------
$new_order = [
    $hero,               // 0. Hero
    $times,              // 1. Sunday times card
    $live,               // 2. Watch live + Latest messages
    $find_place,         // 3. Find your place
    $kids,               // 4. Kids
    $pastor,             // 5. Pastor + prayer
    $merged_events_loop, // 6. Coming up + Stay in the loop
    $chinese_band,       // 7. 中文 strip
];

echo "\nNew order of sections:\n";
foreach ( $new_order as $idx => $s ) {
    echo " [$idx] " . $s['id'] . "\n";
}

// Update DB
update_post_meta( 21, '_elementor_data', wp_slash( json_encode( $new_order, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) );
update_post_meta( 21, '_elementor_version', ELEMENTOR_VERSION );

\Elementor\Plugin::$instance->files_manager->clear_cache();
echo "\nSaved new order and spacing to Post 21 and cleared cache!\n";

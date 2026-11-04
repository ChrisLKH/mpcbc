<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

wp_set_current_user( 1 );

$elem_dir = 'C:/Projects/mpcbc/wordpress/elementor';

// Load templates from disk to ensure clean source components
$t_hero            = json_decode( file_get_contents( $elem_dir . '/home-hero.json' ), true )['content'][0];
$t_times           = json_decode( file_get_contents( $elem_dir . '/home-times-card.json' ), true )['content'][0];
$t_find            = json_decode( file_get_contents( $elem_dir . '/home-find-your-place.json' ), true )['content'][0];
$t_kids            = json_decode( file_get_contents( $elem_dir . '/home-kids.json' ), true )['content'][0];
$t_pastor          = json_decode( file_get_contents( $elem_dir . '/home-pastor-prayer.json' ), true )['content'][0];
$t_events_messages = json_decode( file_get_contents( $elem_dir . '/home-events-messages.json' ), true )['content'][0];
$t_stay_loop       = json_decode( file_get_contents( $elem_dir . '/home-stay-in-loop.json' ), true )['content'][0];
$t_chinese_band    = json_decode( file_get_contents( $elem_dir . '/home-chinese-band.json' ), true )['content'][0];
$t_live            = json_decode( file_get_contents( $elem_dir . '/home-live-section.json' ), true )['content'][0];

// =========================================================================
// 0. HERO CONTAINER
// =========================================================================
$hero = $t_hero;
$hero['settings']['content_width'] = 'full';
$hero['settings']['min_height'] = [ 'unit' => 'px', 'size' => 760 ];
$hero['settings']['min_height_tablet'] = [ 'unit' => 'px', 'size' => 640 ];
$hero['settings']['min_height_mobile'] = [ 'unit' => 'vh', 'size' => 88 ];
$hero['settings']['padding'] = [ 'unit' => 'px', 'top' => '120', 'right' => '56', 'bottom' => '112', 'left' => '56', 'isLinked' => false ];
$hero['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '96', 'right' => '40', 'bottom' => '88', 'left' => '40', 'isLinked' => false ];
$hero['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '80', 'right' => '16', 'bottom' => '80', 'left' => '16', 'isLinked' => false ];

// Ensure Hero Watch Live button points to #live
function fix_hero_link(&$elements) {
    foreach ($elements as &$el) {
        if (($el['widgetType'] ?? '') === 'button' && ($el['settings']['text'] ?? '') === 'Watch live') {
            $el['settings']['link']['url'] = '#live';
        }
        if (!empty($el['elements'])) {
            fix_hero_link($el['elements']);
        }
    }
}
fix_hero_link($hero['elements']);

// =========================================================================
// 1. SUNDAY TIMES CARD
// =========================================================================
$times = $t_times;
$times['settings']['content_width'] = 'boxed';
$times['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$times['settings']['padding'] = [ 'unit' => 'px', 'top' => '0', 'right' => '56', 'bottom' => '0', 'left' => '56', 'isLinked' => false ];
$times['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '0', 'left' => '40', 'isLinked' => false ];
$times['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '0', 'right' => '16', 'bottom' => '0', 'left' => '16', 'isLinked' => false ];
// -56px overlaps hero bottom; -128px pulls Watch live up so hero and watch live meet with zero gap/strip
$times['settings']['margin'] = [ 'unit' => 'px', 'top' => '-56', 'right' => 'auto', 'bottom' => '-128', 'left' => 'auto', 'isLinked' => false ];
$times['settings']['margin_tablet'] = [ 'unit' => 'px', 'top' => '-56', 'right' => 'auto', 'bottom' => '-335', 'left' => 'auto', 'isLinked' => false ];
$times['settings']['margin_mobile'] = [ 'unit' => 'px', 'top' => '-56', 'right' => 'auto', 'bottom' => '-500', 'left' => 'auto', 'isLinked' => false ];
$times['settings']['z_index'] = 2;

// Ensure Times Pill points to #live
function fix_times_link(&$elements) {
    foreach ($elements as &$el) {
        if (($el['widgetType'] ?? '') === 'button' && ($el['settings']['text'] ?? '') === 'Live now · Watch') {
            $el['settings']['link']['url'] = '#live';
        }
        if (!empty($el['elements'])) {
            fix_times_link($el['elements']);
        }
    }
}
fix_times_link($times['elements']);

// =========================================================================
// 2. WATCH LIVE (STONE BACKGROUND + LATEST MESSAGES)
// =========================================================================
$live = $t_live;
$live['settings']['_element_id'] = 'live';
$live['settings']['content_width'] = 'full';
$live['settings']['background_background'] = 'classic';
$live['settings']['background_color'] = '#EDE6DA'; // Stone
$live['settings']['__globals__'] = [ 'background_color' => 'globals/colors?id=stone' ];

// Top padding: accounts for times card overhang + 32px breathing room
$live['settings']['padding'] = [ 'unit' => 'px', 'top' => '160', 'right' => '0', 'bottom' => '96', 'left' => '0', 'isLinked' => false ];
$live['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '380', 'right' => '0', 'bottom' => '64', 'left' => '0', 'isLinked' => false ];
$live['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '540', 'right' => '0', 'bottom' => '48', 'left' => '0', 'isLinked' => false ];

// Inner boxed container
$live_inner = &$live['elements'][0];
$live_inner['settings']['content_width'] = 'boxed';
$live_inner['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$live_inner['settings']['padding'] = [ 'unit' => 'px', 'top' => '0', 'right' => '56', 'bottom' => '0', 'left' => '56', 'isLinked' => false ];
$live_inner['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '0', 'right' => '40', 'bottom' => '0', 'left' => '40', 'isLinked' => false ];
$live_inner['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '0', 'right' => '16', 'bottom' => '0', 'left' => '16', 'isLinked' => false ];
$live_inner['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 48 ];

// Header colors: Text color on Stone
$live_header_group = &$live_inner['elements'][0];
$live_header_group['elements'][0]['settings']['title_color'] = '#16140F';
$live_header_group['elements'][0]['settings']['__globals__'] = [ 'title_color' => 'globals/colors?id=text' ];
$live_header_group['elements'][1]['settings']['title_color'] = '#5C564D';
$live_header_group['elements'][1]['settings']['__globals__'] = [ 'title_color' => 'globals/colors?id=ink-soft' ];

// Column 1 (English)
$live_cols = &$live_inner['elements'][1];
$live_cols['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 24 ]; // gap between columns 24px

$live_col_en = &$live_cols['elements'][0];
$live_col_en['elements'][1]['settings']['title_color'] = '#16140F';
$live_col_en['elements'][1]['settings']['__globals__'] = [ 'title_color' => 'globals/colors?id=text' ];
$live_col_en['elements'][2]['settings']['text_color'] = '#5C564D';
$live_col_en['elements'][2]['settings']['__globals__'] = [ 'text_color' => 'globals/colors?id=ink-soft' ];
$live_col_en['elements'][3]['settings']['button_text_color'] = '#16140F';
$live_col_en['elements'][3]['settings']['border_color'] = '#16140F';
$live_col_en['elements'][3]['settings']['background_color'] = 'transparent';
$live_col_en['elements'][3]['settings']['hover_color'] = '#16140F';
$live_col_en['elements'][3]['settings']['button_background_hover_color'] = 'rgba(22, 20, 15, 0.08)';
$live_col_en['elements'][3]['settings']['border_hover_color'] = '#16140F';

// Column 2 (Chinese)
$live_col_zh = &$live_cols['elements'][1];
$live_col_zh['elements'][1]['settings']['title_color'] = '#16140F';
$live_col_zh['elements'][1]['settings']['__globals__'] = [ 'title_color' => 'globals/colors?id=text' ];
$live_col_zh['elements'][2]['settings']['text_color'] = '#5C564D';
$live_col_zh['elements'][2]['settings']['__globals__'] = [ 'text_color' => 'globals/colors?id=ink-soft' ];
$live_col_zh['elements'][3]['settings']['button_text_color'] = '#16140F';
$live_col_zh['elements'][3]['settings']['border_color'] = '#16140F';
$live_col_zh['elements'][3]['settings']['background_color'] = 'transparent';
$live_col_zh['elements'][3]['settings']['hover_color'] = '#16140F';
$live_col_zh['elements'][3]['settings']['button_background_hover_color'] = 'rgba(22, 20, 15, 0.08)';
$live_col_zh['elements'][3]['settings']['border_hover_color'] = '#16140F';

// Extract the 3 sermon rows and link from home-events-messages.json
$sermon_elements = $t_events_messages['elements'][0]['elements'][1]['elements'];
// Sermon elements: [0] = heading "Latest messages", [1] = sermon_row_1, [2] = sermon_row_2, [3] = sermon_row_3, [4] = latest_messages_link
$sermon_elements[0]['settings']['title_color'] = '#16140F';
$sermon_elements[0]['settings']['__globals__'] = [ 'title_color' => 'globals/colors?id=text' ];
$sermon_elements[0]['settings']['typography_font_size'] = [ 'unit' => 'px', 'size' => 32 ];
$sermon_elements[0]['settings']['margin'] = [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '8', 'left' => '0', 'isLinked' => false ];

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

// Clean live_inner and append latest_messages_wrap
$clean_live_inner = [
    $live_header_group,
    $live_cols,
    $latest_messages_wrap,
];
$live_inner['elements'] = $clean_live_inner;

// =========================================================================
// 3. FIND YOUR PLACE
// =========================================================================
$find_place = $t_find;
$find_place['settings']['content_width'] = 'boxed';
$find_place['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$find_place['settings']['padding'] = [ 'unit' => 'px', 'top' => '96', 'right' => '56', 'bottom' => '96', 'left' => '56', 'isLinked' => false ];
$find_place['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '64', 'right' => '40', 'bottom' => '64', 'left' => '40', 'isLinked' => false ];
$find_place['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '48', 'right' => '16', 'bottom' => '48', 'left' => '16', 'isLinked' => false ];
$find_place['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 32 ]; // heading to content gap 32px

// Cards row
if ( isset( $find_place['elements'][1] ) ) {
    $find_place['elements'][1]['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 24 ]; // gap between cards 24px
}

// =========================================================================
// 4. KIDS
// =========================================================================
$kids = $t_kids;
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
$kids_inner['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 24 ];

$kids_col_content = &$kids_inner['elements'][0];
$kids_col_content['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 24 ];
if ( isset( $kids_col_content['elements'][2] ) ) {
    $kids_col_content['elements'][2]['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 24 ];
}

// =========================================================================
// 5. PASTOR + PRAYER
// =========================================================================
$pastor = $t_pastor;
$pastor['settings']['content_width'] = 'boxed';
$pastor['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$pastor['settings']['padding'] = [ 'unit' => 'px', 'top' => '96', 'right' => '56', 'bottom' => '96', 'left' => '56', 'isLinked' => false ];
$pastor['settings']['padding_tablet'] = [ 'unit' => 'px', 'top' => '64', 'right' => '40', 'bottom' => '64', 'left' => '40', 'isLinked' => false ];
$pastor['settings']['padding_mobile'] = [ 'unit' => 'px', 'top' => '48', 'right' => '16', 'bottom' => '48', 'left' => '16', 'isLinked' => false ];
$pastor['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 48 ];

// =========================================================================
// 6. MERGED COMING UP + STAY IN THE LOOP (STONE BACKGROUND)
// =========================================================================
$col_coming_up = $t_events_messages['elements'][0]['elements'][0];
$col_coming_up['settings']['width'] = [ 'unit' => '%', 'size' => 50 ];
$col_coming_up['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$col_coming_up['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];
$col_coming_up['settings']['flex_gap'] = [ 'unit' => 'px', 'size' => 24 ];
$col_coming_up['elements'][0]['settings']['margin'] = [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '8', 'left' => '0', 'isLinked' => false ];

$stay_loop_title   = $t_stay_loop['elements'][0]['elements'][0];
$stay_loop_socials = $t_stay_loop['elements'][0]['elements'][1];
$stay_loop_form    = $t_stay_loop['elements'][1]['elements'][0];

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
        '__globals__'           => [ 'background_color' => 'globals/colors?id=stone' ],
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
                'flex_gap'              => [ 'unit' => 'px', 'size' => 48 ],
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

// =========================================================================
// 7. 中文 STRIP (SLIMMED TO 48PX TOP/BOTTOM)
// =========================================================================
$chinese_band = $t_chinese_band;
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

// =========================================================================
// ASSEMBLE NEW ORDER
// =========================================================================
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

echo "Assembled 8 sections in order:\n";
foreach ($new_order as $idx => $s) {
    echo " [$idx] " . $s['id'] . "\n";
}

// Save to WordPress database post 21
update_post_meta( 21, '_elementor_data', wp_slash( json_encode( $new_order, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) );
update_post_meta( 21, '_elementor_version', ELEMENTOR_VERSION );

// Clear CSS cache
\Elementor\Plugin::$instance->files_manager->clear_cache();

echo "\nPost 21 saved and Elementor CSS cache cleared successfully!\n";

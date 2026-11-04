<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

wp_set_current_user( 1 );

$media_map = json_decode( file_get_contents( 'C:/Projects/mpcbc/wordpress/scripts/media-map.json' ), true );

// -------------------------------------------------------------
// 1. WELCOME PAGE (Restore center hero from rev77, clean top logo, rebuild schedule strip)
// -------------------------------------------------------------
$rev77 = json_decode( file_get_contents( 'C:/Projects/mpcbc/wordpress/scripts/welcome-rev77.json' ), true );
$w_root = $rev77[0];

// 1.1 Top bar logo
$w_top_bar =& $w_root['elements'][0];
$logo_id  = $media_map['church-logo.png']['id'] ?? 78;
$logo_url = $media_map['church-logo.png']['url'] ?? '';

$w_top_bar['elements'][0] = [
    'id'         => 'welcome_logo',
    'elType'     => 'widget',
    'widgetType' => 'image',
    'settings'   => [
        'image' => [ 'url' => $logo_url, 'id' => $logo_id ],
        'width' => [ 'unit' => 'px', 'size' => 140 ],
    ],
    'isInner'    => false,
    'elements'   => [],
];

// 1.2 Schedule strip (element 2 of w_root)
$w_strip =& $w_root['elements'][2];

$w_times_container = [
    'id'       => 'welcome_strip_times_row',
    'elType'   => 'container',
    'settings' => [
        'content_width'         => 'full',
        'width'                 => [ 'unit' => '%', 'size' => 75 ],
        'width_tablet'          => [ 'unit' => '%', 'size' => 100 ],
        'width_mobile'          => [ 'unit' => '%', 'size' => 100 ],
        'flex_direction'        => 'row',
        'flex_wrap'             => 'wrap',
        'flex_align_items'      => 'center',
        'flex_gap'              => [ 'unit' => 'px', 'size' => 14 ],
    ],
    'elements' => [
        [
            'id'         => 'welcome_time_sundays',
            'elType'     => 'widget',
            'widgetType' => 'text-editor',
            'settings'   => [
                'editor'                => '<p><strong>Sundays</strong> <span lang="zh-Hant">主日</span></p>',
                'text_color'            => '#ffffff',
                'typography_typography' => 'custom',
                'typography_font_size'  => [ 'unit' => 'px', 'size' => 15 ],
            ],
            'isInner'    => false,
            'elements'   => [],
        ],
        [
            'id'         => 'welcome_time_canto',
            'elType'     => 'widget',
            'widgetType' => 'text-editor',
            'settings'   => [
                'editor'                => '<p><span lang="zh-Hant">粵語</span> <strong>9:30</strong></p>',
                'text_color'            => '#ffffff',
                'typography_typography' => 'custom',
                'typography_font_size'  => [ 'unit' => 'px', 'size' => 15 ],
            ],
            'isInner'    => false,
            'elements'   => [],
        ],
        [
            'id'         => 'welcome_time_live_pill',
            'elType'     => 'widget',
            'widgetType' => 'button',
            'settings'   => [
                'text'                  => 'Live',
                'link'                  => [ 'url' => '/watch/' ],
                'button_text_color'     => '#ffffff',
                'background_color'      => '#D92D27',
                '__globals__'           => [
                    'background_color' => 'globals/colors?id=live',
                ],
                'border_radius'         => [
                    'unit'     => 'px',
                    'top'      => 999,
                    'right'    => 999,
                    'bottom'   => 999,
                    'left'     => 999,
                    'isLinked' => true,
                ],
                'typography_typography' => 'custom',
                'typography_font_size'  => [ 'unit' => 'px', 'size' => 11 ],
                'typography_font_weight'=> '700',
                'padding'               => [
                    'unit'     => 'px',
                    'top'      => 2,
                    'right'    => 8,
                    'bottom'   => 2,
                    'left'     => 8,
                    'isLinked' => false,
                ],
            ],
            'isInner'    => false,
            'elements'   => [],
        ],
        [
            'id'         => 'welcome_time_en',
            'elType'     => 'widget',
            'widgetType' => 'text-editor',
            'settings'   => [
                'editor'                => '<p>English <strong>11:00</strong></p>',
                'text_color'            => '#ffffff',
                'typography_typography' => 'custom',
                'typography_font_size'  => [ 'unit' => 'px', 'size' => 15 ],
            ],
            'isInner'    => false,
            'elements'   => [],
        ],
        [
            'id'         => 'welcome_time_mando',
            'elType'     => 'widget',
            'widgetType' => 'text-editor',
            'settings'   => [
                'editor'                => '<p><span lang="zh-Hant">國語</span> <strong>11:30</strong></p>',
                'text_color'            => '#ffffff',
                'typography_typography' => 'custom',
                'typography_font_size'  => [ 'unit' => 'px', 'size' => 15 ],
            ],
            'isInner'    => false,
            'elements'   => [],
        ],
        [
            'id'         => 'welcome_time_kids',
            'elType'     => 'widget',
            'widgetType' => 'text-editor',
            'settings'   => [
                'editor'                => '<p>Kids <span lang="zh-Hant">兒童</span> <strong>11:00</strong></p>',
                'text_color'            => '#F4B942',
                '__globals__'           => [
                    'text_color' => 'globals/colors?id=accent',
                ],
                'typography_typography' => 'custom',
                'typography_font_size'  => [ 'unit' => 'px', 'size' => 15 ],
            ],
            'isInner'    => false,
            'elements'   => [],
        ],
        [
            'id'         => 'welcome_time_addr',
            'elType'     => 'widget',
            'widgetType' => 'text-editor',
            'settings'   => [
                'editor'                => '<p>[Street address]</p>',
                'text_color'            => 'rgba(255,255,255,0.7)',
                'typography_typography' => 'custom',
                'typography_font_size'  => [ 'unit' => 'px', 'size' => 13 ],
            ],
            'isInner'    => false,
            'elements'   => [],
        ],
    ],
    'isInner'  => false,
];

// Existing Watch live button widget in strip
$watch_btn_widget = $w_strip['elements'][1];
$w_strip['elements'] = [ $w_times_container, $watch_btn_widget ];

$welcome_doc = [
    'version'       => '0.4',
    'title'         => 'Welcome',
    'type'          => 'page',
    'content'       => [ $w_root ],
    'page_settings' => [],
];
file_put_contents( 'C:/Projects/mpcbc/wordpress/elementor/welcome-canvas.json', json_encode( $welcome_doc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
update_post_meta( 17, '_elementor_data', wp_slash( json_encode( [ $w_root ] ) ) );
echo "Cleaned Welcome page (ID 17).\n";

// -------------------------------------------------------------
// 2. CHINESE BAND (home-chinese-band.json)
// -------------------------------------------------------------
$cb_path = 'C:/Projects/mpcbc/wordpress/elementor/home-chinese-band.json';
$cb = json_decode( file_get_contents( $cb_path ), true );
$cb_root =& $cb['content'][0];
$cb_inner =& $cb_root['elements'][0];
$cb_desc =& $cb_inner['elements'][1];
$cb_desc['settings'] = [
    'editor'                => '<p lang="zh-Hant">我們有中文資訊：崇拜時間、地址、兒童安排。</p>',
    'text_color'            => 'rgba(255,255,255,0.92)',
    'typography_typography' => 'custom',
    'typography_font_size'  => [ 'unit' => 'px', 'size' => 22 ],
    'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.7 ],
];
file_put_contents( $cb_path, json_encode( $cb, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Cleaned Chinese Band section.\n";

// -------------------------------------------------------------
// 3. FIND YOUR PLACE (home-find-your-place.json)
// -------------------------------------------------------------
$fyp_path = 'C:/Projects/mpcbc/wordpress/elementor/home-find-your-place.json';
$fyp = json_decode( file_get_contents( $fyp_path ), true );
$fyp_root =& $fyp['content'][0];

// Description under title
$fyp_desc =& $fyp_root['elements'][1];
$fyp_desc['settings'] = [
    'editor'                => '<p>Three services on one Sunday morning, and a place for your kids.</p>',
    'text_color'            => '#5C564D',
    '__globals__'           => [ 'text_color' => 'globals/colors?id=ink_soft' ],
    'typography_typography' => 'custom',
    'typography_font_size'  => [ 'unit' => 'px', 'size' => 20 ],
    'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.5 ],
];

// Cards row (element 2)
$cards_row =& $fyp_root['elements'][2];

function clean_card(&$card, $meta_text, $desc_text, $link_text, $link_url, $is_zh = false) {
    // [0] is Heading title
    // [1] is meta
    $card['elements'][1]['settings'] = [
        'editor'                => "<p>{$meta_text}</p>",
        'text_color'            => '#ffffff',
        'typography_typography' => 'custom',
        'typography_font_size'  => [ 'unit' => 'px', 'size' => 16 ],
        'typography_font_weight'=> '600',
    ];
    // [2] is desc
    $desc_html = $is_zh ? "<p lang=\"zh-Hant\">{$desc_text}</p>" : "<p>{$desc_text}</p>";
    $card['elements'][2]['settings'] = [
        'editor'                => $desc_html,
        'text_color'            => 'rgba(255,255,255,0.9)',
        'typography_typography' => 'custom',
        'typography_font_size'  => [ 'unit' => 'px', 'size' => 16 ],
        'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.5 ],
    ];
    // [3] is link
    $card['elements'][3]['settings'] = [
        'editor'                => "<p><a href=\"{$link_url}\">{$link_text}</a></p>",
        'text_color'            => '#ffffff',
        'typography_typography' => 'custom',
        'typography_font_size'  => [ 'unit' => 'px', 'size' => 16 ],
        'typography_font_weight'=> '600',
    ];
}

clean_card( $cards_row['elements'][0], 'In English', 'Honest teaching, good music, lunch after.', 'See Sundays →', '/sundays/' );
clean_card( $cards_row['elements'][1], 'In Cantonese', '崇拜後一起飲茶傾偈。', 'Learn more →', '/zh/', true );
clean_card( $cards_row['elements'][2], 'In Mandarin', '新朋友很多，歡迎你。', 'Learn more →', '/zh/', true );
clean_card( $cards_row['elements'][3], 'Ages 0–18', 'Safe, fun classes while you’re in the service.', 'See Kids →', '/kids/' );

file_put_contents( $fyp_path, json_encode( $fyp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Cleaned Find Your Place section.\n";

// -------------------------------------------------------------
// 4. PASTOR & PRAYER (home-pastor-prayer.json)
// -------------------------------------------------------------
$pp_path = 'C:/Projects/mpcbc/wordpress/elementor/home-pastor-prayer.json';
$pp = json_decode( file_get_contents( $pp_path ), true );
$pp_root  =& $pp['content'][0];
$pp_inner =& $pp_root['elements'][0];
$col_p    =& $pp_inner['elements'][0];
$col_pr   =& $pp_inner['elements'][1];

// Pastor portrait image widget
$pastor_id  = $media_map['pastor-portrait.png']['id'] ?? 80;
$pastor_url = $media_map['pastor-portrait.png']['url'] ?? '';

$col_p['elements'][0]['elements'][0] = [
    'id'         => 'pastor_portrait_img',
    'elType'     => 'widget',
    'widgetType' => 'image',
    'settings'   => [
        'image'               => [ 'url' => $pastor_url, 'id' => $pastor_id ],
        'width'               => [ 'unit' => 'px', 'size' => 200 ],
        'height'              => [ 'unit' => 'px', 'size' => 240 ],
        'image_border_radius' => [
            'unit' => 'px', 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16, 'isLinked' => true
        ],
    ],
    'isInner'    => false,
    'elements'   => [],
];

// Pastor quote author
$col_p['elements'][1]['elements'][1]['settings'] = [
    'editor'                => '<p>[Pastor name], Senior Pastor</p>',
    'text_color'            => '#5C564D',
    '__globals__'           => [ 'text_color' => 'globals/colors?id=ink_soft' ],
    'typography_typography' => 'custom',
    'typography_font_size'  => [ 'unit' => 'px', 'size' => 18 ],
];

// Prayer card desc
$col_pr['elements'][0]['elements'][1]['settings'] = [
    'editor'                => '<p>Our pastors read every request and keep it private.</p>',
    'text_color'            => '#5C564D',
    '__globals__'           => [ 'text_color' => 'globals/colors?id=ink_soft' ],
    'typography_typography' => 'custom',
    'typography_font_size'  => [ 'unit' => 'px', 'size' => 17 ],
    'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.5 ],
];

file_put_contents( $pp_path, json_encode( $pp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Cleaned Pastor & Prayer section.\n";

// -------------------------------------------------------------
// 5. RE-MERGE ALL HOME SECTIONS INTO home-full.json AND POST 21
// -------------------------------------------------------------
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
    } else {
        echo "WARNING: Missing content in {$sec_name}\n";
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
echo "Merged and updated Home page (ID 21).\n";

// Also clear Elementor CSS cache
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Cleared Elementor cache.\n";

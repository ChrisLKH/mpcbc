<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

wp_set_current_user( 1 );

echo "Starting block rebuild...\n";

// Load media map
$media_map = json_decode( file_get_contents( 'C:/Projects/mpcbc/wordpress/scripts/media-map.json' ), true );

// -------------------------------------------------------------
// 1. PRAYER PAGE (Post ID 18)
// -------------------------------------------------------------
$prayer_json_path = 'C:/Projects/mpcbc/wordpress/elementor/prayer-page.json';
$prayer = json_decode( file_get_contents( $prayer_json_path ), true );

// In prayer_col_info:
// [0] is prayer_heading
// [1] is prayer_lead
// [2] is prayer_bullets -> change to container with 1px top border and icon-list
$prayer_root =& $prayer['content'][0];
$prayer_col_info =& $prayer_root['elements'][0];

// prayer_lead
$prayer_col_info['elements'][1] = [
    'id'         => 'prayer_lead',
    'elType'     => 'widget',
    'widgetType' => 'text-editor',
    'settings'   => [
        'editor'                => '<p>Our pastors read every request and pray for you by name this week.</p>',
        'text_color'            => '#5C564D',
        '__globals__'           => [
            'text_color' => 'globals/colors?id=ink_soft',
        ],
        'typography_typography' => 'custom',
        'typography_font_size'  => [ 'unit' => 'px', 'size' => 22 ],
        'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.55 ],
    ],
    'isInner'    => false,
    'elements'   => [],
];

// prayer_promises_container
$prayer_col_info['elements'][2] = [
    'id'       => 'prayer_promises_container',
    'elType'   => 'container',
    'settings' => [
        'content_width' => 'full',
        'border_border' => 'solid',
        'border_width'  => [
            'unit'     => 'px',
            'top'      => 1,
            'right'    => 0,
            'bottom'   => 0,
            'left'     => 0,
            'isLinked' => false,
        ],
        'border_color'  => '#E3DACB',
        'padding'       => [
            'unit'     => 'px',
            'top'      => 28,
            'right'    => 0,
            'bottom'   => 0,
            'left'     => 0,
            'isLinked' => false,
        ],
        'flex_direction'=> 'column',
    ],
    'elements' => [
        [
            'id'         => 'prayer_bullets',
            'elType'     => 'widget',
            'widgetType' => 'icon-list',
            'settings'   => [
                'icon_list' => [
                    [
                        'text'          => 'Read only by [Pastor names] — or the prayer team, if you choose.',
                        'selected_icon' => [ 'value' => '', 'library' => '' ],
                    ],
                    [
                        'text'          => 'Never posted or shared without your permission.',
                        'selected_icon' => [ 'value' => '', 'library' => '' ],
                    ],
                    [
                        'text'          => 'You don’t need to give your name.',
                        'selected_icon' => [ 'value' => '', 'library' => '' ],
                    ],
                ],
                'space_between'          => [ 'unit' => 'px', 'size' => 14 ],
                'text_color'             => '#5C564D',
                '__globals__'            => [
                    'text_color' => 'globals/colors?id=ink_soft',
                ],
                'typography_typography'  => 'custom',
                'typography_font_size'   => [ 'unit' => 'px', 'size' => 18 ],
                'typography_line_height' => [ 'unit' => 'em', 'size' => 1.5 ],
            ],
            'isInner'    => false,
            'elements'   => [],
        ],
    ],
    'isInner'  => false,
];

file_put_contents( $prayer_json_path, json_encode( $prayer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
update_post_meta( 18, '_elementor_data', wp_slash( json_encode( $prayer['content'] ) ) );
echo "Updated Prayer page (ID 18).\n";

// -------------------------------------------------------------
// 2. FOOTER TEMPLATE (Post ID 16)
// -------------------------------------------------------------
$footer_json_path = 'C:/Projects/mpcbc/wordpress/elementor/footer-template.json';
$footer = json_decode( file_get_contents( $footer_json_path ), true );

// Update text widgets in footer
$f_root =& $footer['content'][0];
$f_grid =& $f_root['elements'][0];
$f_col1 =& $f_grid['elements'][0];
$f_col2 =& $f_grid['elements'][1];
$f_col3 =& $f_grid['elements'][2];
$f_col4 =& $f_grid['elements'][3];
$f_bar  =& $f_root['elements'][1];

// col1: address
$f_col1['elements'][1]['settings'] = [
    'editor'                => '<p>Monterey Park Chinese Baptist Church<br>[Street address], Monterey Park, CA</p><p><a href="#">Directions</a></p>',
    'text_color'            => 'rgba(255,255,255,0.8)',
    'typography_typography' => 'custom',
    'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.7 ],
];

// col2: sundays
$f_col2['elements'][1]['settings'] = [
    'editor'                => '<p>Cantonese <span lang="zh-Hant">粵語</span> 9:30 AM<br>English 11:00 AM<br>Mandarin <span lang="zh-Hant">國語</span> 11:30 AM</p>',
    'text_color'            => 'rgba(255,255,255,0.8)',
    'typography_typography' => 'custom',
    'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.8 ],
];

// col3: explore
$f_col3['elements'][1]['settings'] = [
    'editor'                => '<p><a href="/visit/">Visit</a><br><a href="/kids/">Kids &amp; Youth</a><br><a href="/watch/">Watch</a><br><a href="/give/">Give</a><br><a href="/prayer/">Prayer request</a></p>',
    'text_color'            => 'rgba(255,255,255,0.8)',
    'typography_typography' => 'custom',
    'typography_line_height'=> [ 'unit' => 'em', 'size' => 2.0 ],
];

// col4: contact
$f_col4['elements'][1]['settings'] = [
    'editor'                => '<p>[Phone]<br>[Email]</p>',
    'text_color'            => 'rgba(255,255,255,0.8)',
    'typography_typography' => 'custom',
    'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.8 ],
];

// bottom bar: since
$f_bar['elements'][0]['settings'] = [
    'editor'                => '<p>Since 1976 · Monterey Park, California</p>',
    'text_color'            => 'rgba(255,255,255,0.6)',
    'typography_typography' => 'custom',
    'typography_font_size'  => [ 'unit' => 'px', 'size' => 14 ],
];

// bottom bar: zh link
$f_bar['elements'][1]['settings'] = [
    'editor'                => '<p><a href="/zh/" lang="zh-Hant">中文資訊</a></p>',
    'text_color'            => 'rgba(255,255,255,0.8)',
    'typography_typography' => 'custom',
    'typography_font_size'  => [ 'unit' => 'px', 'size' => 14 ],
];

file_put_contents( $footer_json_path, json_encode( $footer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
update_post_meta( 16, '_elementor_data', wp_slash( json_encode( $footer['content'] ) ) );
echo "Updated Footer template (ID 16).\n";

// -------------------------------------------------------------
// 3. WELCOME PAGE (Post ID 17)
// -------------------------------------------------------------
$welcome_json_path = 'C:/Projects/mpcbc/wordpress/elementor/welcome-canvas.json';
$welcome = json_decode( file_get_contents( $welcome_json_path ), true );

$w_root =& $welcome['content'][0];
// element [1] inside w_root is welcome_schedule_strip
$w_strip =& $w_root['elements'][1];

// Rebuild welcome_schedule_strip elements:
// Container for the times items + Watch Live button
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

// Keep existing Watch Live button widget
$w_watch_btn = $w_strip['elements'][1];

// Set new elements array in strip
$w_strip['elements'] = [ $w_times_container, $w_watch_btn ];

file_put_contents( $welcome_json_path, json_encode( $welcome, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
update_post_meta( 17, '_elementor_data', wp_slash( json_encode( $welcome['content'] ) ) );
echo "Updated Welcome page (ID 17).\n";

// -------------------------------------------------------------
// 4. HOME SECTIONS
// -------------------------------------------------------------

// 4.1 HERO (home-hero.json)
$hero_json_path = 'C:/Projects/mpcbc/wordpress/elementor/home-hero.json';
$hero = json_decode( file_get_contents( $hero_json_path ), true );
$h_root =& $hero['content'][0];

// h_subtext_en (element 1)
$h_root['elements'][1]['settings'] = [
    'editor'                => '<p>A friendly church in Monterey Park, with services in English, Cantonese and Mandarin.</p>',
    'text_color'            => 'rgba(255,255,255,0.9)',
    'typography_typography' => 'custom',
    'typography_font_size'  => [ 'unit' => 'px', 'size' => 23 ],
    'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.5 ],
];

// h_subtext_zh (element 2)
$h_root['elements'][2]['settings'] = [
    'editor'                => '<p lang="zh-Hant">歡迎你，我們有粵語及國語崇拜。</p>',
    'text_color'            => 'rgba(255,255,255,0.85)',
    'typography_typography' => 'custom',
    'typography_font_size'  => [ 'unit' => 'px', 'size' => 20 ],
    'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.5 ],
];

// h_actions_wrap (element 3)
$h_actions =& $h_root['elements'][3];
$h_actions['settings']['content_width'] = 'full';
$h_actions['settings']['padding'] = [
    'unit' => 'px', 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'isLinked' => true
];

// h_btn_group (element 0 of h_actions)
$h_btn_group =& $h_actions['elements'][0];
$h_btn_group['settings'] = [
    'content_width'         => 'full',
    'width'                 => [ 'unit' => '%', 'size' => 100 ],
    'padding'               => [ 'unit' => 'px', 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'isLinked' => true ],
    'margin'                => [ 'unit' => 'px', 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'isLinked' => true ],
    'flex_direction'        => 'row',
    'flex_wrap'             => 'wrap',
    'flex_justify_content'  => 'flex-start',
    'flex_align_items'      => 'center',
    'align_self'            => 'flex-start',
    'flex_gap'              => [ 'unit' => 'px', 'size' => 14 ],
];

file_put_contents( $hero_json_path, json_encode( $hero, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Updated Hero section.\n";

// 4.2 SUNDAY TIMES CARD (home-times-card.json)
$times_json_path = 'C:/Projects/mpcbc/wordpress/elementor/home-times-card.json';
$times = json_decode( file_get_contents( $times_json_path ), true );
$t_outer =& $times['content'][0];
$t_card  =& $t_outer['elements'][0];

// Common hover settings for all 4 service containers
$service_hover_settings = [
    'background_hover_background' => 'classic',
    'background_hover_color'      => '#F4EEE3',
    '__globals__'                 => [
        'background_hover_color' => 'globals/colors?id=sand',
    ],
    'background_hover_transition' => [ 'unit' => 's', 'size' => 0.3 ],
    'border_radius'               => [
        'unit' => 'px', 'top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12, 'isLinked' => true
    ],
    'padding'                     => [
        'unit' => 'px', 'top' => 8, 'right' => 12, 'bottom' => 8, 'left' => 14, 'isLinked' => false
    ],
];

// Helper to create a time row container (Heading 44px + Text AM 15px)
function create_time_row( $time_str, $id_prefix, $color = '#16140F', $global_color = 'globals/colors?id=text' ) {
    return [
        'id'       => "{$id_prefix}_time_row",
        'elType'   => 'container',
        'settings' => [
            'content_width'    => 'full',
            'flex_direction'   => 'row',
            'flex_align_items' => 'baseline',
            'flex_gap'         => [ 'unit' => 'px', 'size' => 6 ],
            'padding'          => [ 'unit' => 'px', 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'isLinked' => true ],
        ],
        'elements' => [
            [
                'id'         => "{$id_prefix}_big_time",
                'elType'     => 'widget',
                'widgetType' => 'heading',
                'settings'   => [
                    'title'                  => $time_str,
                    'header_size'            => 'h3',
                    'title_color'            => $color,
                    '__globals__'            => [
                        'title_color' => $global_color,
                    ],
                    'typography_typography'  => 'custom',
                    'typography_font_family' => 'Bricolage Grotesque',
                    'typography_font_size'   => [ 'unit' => 'px', 'size' => 44 ],
                    'typography_font_weight' => '700',
                    'typography_line_height' => [ 'unit' => 'em', 'size' => 1 ],
                ],
                'isInner'    => false,
                'elements'   => [],
            ],
            [
                'id'         => "{$id_prefix}_am",
                'elType'     => 'widget',
                'widgetType' => 'heading',
                'settings'   => [
                    'title'                  => 'AM',
                    'header_size'            => 'span',
                    'title_color'            => $color,
                    '__globals__'            => [
                        'title_color' => $global_color,
                    ],
                    'typography_typography'  => 'custom',
                    'typography_font_size'   => [ 'unit' => 'px', 'size' => 15 ],
                    'typography_font_weight' => '600',
                    'typography_line_height' => [ 'unit' => 'em', 'size' => 1 ],
                ],
                'isInner'    => false,
                'elements'   => [],
            ],
        ],
        'isInner'  => false,
    ];
}

// 0: Header
$t_col_header =& $t_card['elements'][0];
$t_col_header['elements'][0]['settings']['title'] = 'Sunday worship <span lang="zh-Hant">主日崇拜</span>';
$t_col_header['elements'][1]['settings'] = [
    'editor'                => '<p>[Street address], Monterey Park</p>',
    'text_color'            => '#5C564D',
    '__globals__'           => [ 'text_color' => 'globals/colors?id=ink_soft' ],
    'typography_typography' => 'custom',
    'typography_font_size'  => [ 'unit' => 'px', 'size' => 14 ],
];

// 1: Cantonese
$t_col_canto =& $t_card['elements'][1];
$t_col_canto['settings'] = array_merge( $t_col_canto['settings'], $service_hover_settings, [
    'background_background' => '',
    'background_color'      => '',
] );
$t_col_canto['elements'] = [
    [
        'id'         => 'times_canto_label',
        'elType'     => 'widget',
        'widgetType' => 'heading',
        'settings'   => [
            'title'                  => 'Cantonese <span lang="zh-Hant">粵語</span>',
            'header_size'            => 'h4',
            'title_color'            => '#5C564D',
            '__globals__'            => [ 'title_color' => 'globals/colors?id=ink_soft' ],
            'typography_typography'  => 'custom',
            'typography_font_size'   => [ 'unit' => 'px', 'size' => 15 ],
            'typography_font_weight' => '700',
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
    create_time_row( '9:30', 'times_canto' ),
    [
        'id'         => 'times_canto_live_btn',
        'elType'     => 'widget',
        'widgetType' => 'button',
        'settings'   => [
            'text'                  => 'Live now · Watch',
            'link'                  => [ 'url' => '/watch/' ],
            'button_text_color'     => '#ffffff',
            'background_color'      => '#D92D27',
            '__globals__'           => [ 'background_color' => 'globals/colors?id=live' ],
            'border_radius'         => [
                'unit' => 'px', 'top' => 999, 'right' => 999, 'bottom' => 999, 'left' => 999, 'isLinked' => true
            ],
            'typography_typography' => 'custom',
            'typography_font_size'  => [ 'unit' => 'px', 'size' => 12 ],
            'typography_font_weight'=> '700',
            'padding'               => [
                'unit' => 'px', 'top' => 4, 'right' => 12, 'bottom' => 4, 'left' => 12, 'isLinked' => false
            ],
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
];

// 2: English
$t_col_en =& $t_card['elements'][2];
$t_col_en['settings'] = array_merge( $t_col_en['settings'], $service_hover_settings, [
    'background_background' => '',
    'background_color'      => '',
] );
$t_col_en['elements'] = [
    [
        'id'         => 'times_en_label',
        'elType'     => 'widget',
        'widgetType' => 'heading',
        'settings'   => [
            'title'                  => 'English',
            'header_size'            => 'h4',
            'title_color'            => '#16140F',
            '__globals__'            => [ 'title_color' => 'globals/colors?id=text' ],
            'typography_typography'  => 'custom',
            'typography_font_size'   => [ 'unit' => 'px', 'size' => 15 ],
            'typography_font_weight' => '700',
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
    create_time_row( '11:00', 'times_en', '#16140F', 'globals/colors?id=text' ),
    [
        'id'         => 'times_en_status',
        'elType'     => 'widget',
        'widgetType' => 'text-editor',
        'settings'   => [
            'editor'                => '<p>Starts in 1 hr</p>',
            'text_color'            => '#5C564D',
            '__globals__'           => [ 'text_color' => 'globals/colors?id=ink_soft' ],
            'typography_typography' => 'custom',
            'typography_font_size'  => [ 'unit' => 'px', 'size' => 13 ],
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
];

// 3: Mandarin
$t_col_mando =& $t_card['elements'][3];
$t_col_mando['settings'] = array_merge( $t_col_mando['settings'], $service_hover_settings, [
    'background_background' => '',
    'background_color'      => '',
] );
$t_col_mando['elements'] = [
    [
        'id'         => 'times_mandarin_label',
        'elType'     => 'widget',
        'widgetType' => 'heading',
        'settings'   => [
            'title'                  => 'Mandarin <span lang="zh-Hant">國語</span>',
            'header_size'            => 'h4',
            'title_color'            => '#5C564D',
            '__globals__'            => [ 'title_color' => 'globals/colors?id=ink_soft' ],
            'typography_typography'  => 'custom',
            'typography_font_size'   => [ 'unit' => 'px', 'size' => 15 ],
            'typography_font_weight' => '700',
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
    create_time_row( '11:30', 'times_mandarin' ),
    [
        'id'         => 'times_mandarin_status',
        'elType'     => 'widget',
        'widgetType' => 'text-editor',
        'settings'   => [
            'editor'                => '<p>Starts in 2 hr</p>',
            'text_color'            => '#5C564D',
            '__globals__'           => [ 'text_color' => 'globals/colors?id=ink_soft' ],
            'typography_typography' => 'custom',
            'typography_font_size'  => [ 'unit' => 'px', 'size' => 13 ],
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
];

// 4: Kids
$t_col_kids =& $t_card['elements'][4];
$t_col_kids['settings'] = array_merge( $t_col_kids['settings'], $service_hover_settings, [
    'background_background' => '',
    'background_color'      => '',
] );
$t_col_kids['elements'] = [
    [
        'id'         => 'times_kids_label',
        'elType'     => 'widget',
        'widgetType' => 'heading',
        'settings'   => [
            'title'                  => 'Kids’ worship',
            'header_size'            => 'h4',
            'title_color'            => '#16140F',
            '__globals__'            => [ 'title_color' => 'globals/colors?id=text' ],
            'typography_typography'  => 'custom',
            'typography_font_size'   => [ 'unit' => 'px', 'size' => 15 ],
            'typography_font_weight' => '700',
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
    create_time_row( '11:00', 'times_kids' ),
    [
        'id'         => 'times_kids_status',
        'elType'     => 'widget',
        'widgetType' => 'text-editor',
        'settings'   => [
            'editor'                => '<p>Classes in English</p>',
            'text_color'            => '#5C564D',
            '__globals__'           => [ 'text_color' => 'globals/colors?id=ink_soft' ],
            'typography_typography' => 'custom',
            'typography_font_size'  => [ 'unit' => 'px', 'size' => 13 ],
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
];

// 5: Links
$t_col_links =& $t_card['elements'][5];
$t_col_links['elements'][0]['settings'] = [
    'editor'                => '<p><a href="#">Directions</a></p>',
    'text_color'            => '#0E6B5C',
    '__globals__'           => [ 'text_color' => 'globals/colors?id=primary' ],
    'typography_typography' => 'custom',
    'typography_font_size'  => [ 'unit' => 'px', 'size' => 15 ],
    'typography_font_weight'=> '600',
];
$t_col_links['elements'][1]['settings'] = [
    'editor'                => '<p><a href="#">Add to calendar</a></p>',
    'text_color'            => '#0E6B5C',
    '__globals__'           => [ 'text_color' => 'globals/colors?id=primary' ],
    'typography_typography' => 'custom',
    'typography_font_size'  => [ 'unit' => 'px', 'size' => 15 ],
    'typography_font_weight'=> '600',
];

file_put_contents( $times_json_path, json_encode( $times, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Updated Sunday Times Card.\n";

// 4.3 KIDS SECTION (home-kids.json)
$kids_json_path = 'C:/Projects/mpcbc/wordpress/elementor/home-kids.json';
$kids = json_decode( file_get_contents( $kids_json_path ), true );
$k_root  =& $kids['content'][0];
$k_inner =& $k_root['elements'][0];
$k_col_c =& $k_inner['elements'][0];
$k_col_p =& $k_inner['elements'][1];

// kids lead (element 1)
$k_col_c['elements'][1]['settings'] = [
    'editor'                => '<p>Safe, fun classes for every age while you’re in the service.</p>',
    'text_color'            => '#16140F',
    '__globals__'           => [ 'text_color' => 'globals/colors?id=text' ],
    'typography_typography' => 'custom',
    'typography_font_size'  => [ 'unit' => 'px', 'size' => 21 ],
    'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.55 ],
];

// Helper to make a tile container
function create_kids_tile( $id, $title, $subtitle ) {
    return [
        'id'       => $id,
        'elType'   => 'container',
        'settings' => [
            'width'                => [ 'unit' => '%', 'size' => 31 ],
            'width_tablet'         => [ 'unit' => '%', 'size' => 31 ],
            'width_mobile'         => [ 'unit' => '%', 'size' => 100 ],
            'background_background'=> 'classic',
            'background_color'     => '#FAF7F2',
            '__globals__'          => [ 'background_color' => 'globals/colors?id=paper' ],
            'border_radius'        => [
                'unit' => 'px', 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16, 'isLinked' => true
            ],
            'padding'              => [
                'unit' => 'px', 'top' => 22, 'right' => 20, 'bottom' => 22, 'left' => 20, 'isLinked' => false
            ],
            'flex_direction'       => 'column',
            'flex_gap'             => [ 'unit' => 'px', 'size' => 4 ],
            'content_width'        => 'full',
        ],
        'elements' => [
            [
                'id'         => "{$id}_title",
                'elType'     => 'widget',
                'widgetType' => 'heading',
                'settings'   => [
                    'title'                  => $title,
                    'header_size'            => 'h4',
                    'title_color'            => '#16140F',
                    '__globals__'            => [ 'title_color' => 'globals/colors?id=text' ],
                    'typography_typography'  => 'custom',
                    'typography_font_size'   => [ 'unit' => 'px', 'size' => 19 ],
                    'typography_font_weight' => '700',
                ],
                'isInner'    => false,
                'elements'   => [],
            ],
            [
                'id'         => "{$id}_sub",
                'elType'     => 'widget',
                'widgetType' => 'text-editor',
                'settings'   => [
                    'editor'                => "<p>{$subtitle}</p>",
                    'text_color'            => '#5C564D',
                    '__globals__'           => [ 'text_color' => 'globals/colors?id=ink_soft' ],
                    'typography_typography' => 'custom',
                    'typography_font_size'  => [ 'unit' => 'px', 'size' => 15 ],
                ],
                'isInner'    => false,
                'elements'   => [],
            ],
        ],
        'isInner'  => false,
    ];
}

// Replace tiles in kids_tiles_row (element 2)
$k_col_c['elements'][2]['elements'] = [
    create_kids_tile( 'tile_nursery', 'Nursery', 'Ages 0–3 · all morning' ),
    create_kids_tile( 'tile_kids', 'Kids', 'Ages 4–11 · 11:00 AM' ),
    create_kids_tile( 'tile_youth', 'Youth', 'Ages 12–18 · Fridays 7:30 PM' ),
];

// Chinese note (element 4): wrap in container
$k_col_c['elements'][4] = [
    'id'       => 'kids_zh_container',
    'elType'   => 'container',
    'settings' => [
        'content_width'         => 'full',
        'background_background' => 'classic',
        'background_color'      => 'rgba(250,247,242,0.6)',
        'border_radius'         => [
            'unit' => 'px', 'top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12, 'isLinked' => true
        ],
        'padding'               => [
            'unit' => 'px', 'top' => 12, 'right' => 18, 'bottom' => 12, 'left' => 18, 'isLinked' => false
        ],
    ],
    'elements' => [
        [
            'id'         => 'kids_zh_text',
            'elType'     => 'widget',
            'widgetType' => 'text-editor',
            'settings'   => [
                'editor'                => '<p lang="zh-Hant">課堂以英語進行，登記處有中文同工協助家長。</p>',
                'text_color'            => '#16140F',
                '__globals__'           => [ 'text_color' => 'globals/colors?id=text' ],
                'typography_typography' => 'custom',
                'typography_font_size'  => [ 'unit' => 'px', 'size' => 18 ],
                'typography_font_weight'=> '500',
                'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.7 ],
            ],
            'isInner'    => false,
            'elements'   => [],
        ],
    ],
    'isInner'  => false,
];

// 3-Photo Collage in kids_col_photos
$k_col_p['settings']['position'] = 'relative';
$k_col_p['settings']['min_height'] = [ 'unit' => 'px', 'size' => 660 ];
$k_col_p['settings']['content_width'] = 'full';

$photo_craft_id   = $media_map['photo-kids-craft.png']['id'] ?? 84;
$photo_craft_url  = $media_map['photo-kids-craft.png']['url'] ?? '';
$photo_vbs_id     = $media_map['photo-kids-vbs.png']['id'] ?? 86;
$photo_vbs_url    = $media_map['photo-kids-vbs.png']['url'] ?? '';
$photo_checkin_id = $media_map['photo-kids-checkin.png']['id'] ?? 83;
$photo_checkin_url= $media_map['photo-kids-checkin.png']['url'] ?? '';

$k_col_p['elements'] = [
    [
        'id'         => 'kids_photo_craft',
        'elType'     => 'widget',
        'widgetType' => 'image',
        'settings'   => [
            'image'                             => [ 'url' => $photo_craft_url, 'id' => $photo_craft_id ],
            '_position'                         => 'absolute',
            '_offset_orientation_h'             => 'start',
            '_offset_x'                         => [ 'unit' => 'px', 'size' => 0 ],
            '_offset_orientation_v'             => 'start',
            '_offset_y'                         => [ 'unit' => 'px', 'size' => 40 ],
            'width'                             => [ 'unit' => 'px', 'size' => 340 ],
            'height'                            => [ 'unit' => 'px', 'size' => 420 ],
            'image_border_radius'               => [
                'unit' => 'px', 'top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'isLinked' => true
            ],
            '_transform_rotate_z'               => [ 'unit' => 'deg', 'size' => -3 ],
            'image_box_shadow_box_shadow_type'  => 'yes',
            'image_box_shadow_box_shadow'       => [
                'horizontal' => 0, 'vertical' => 24, 'blur' => 48, 'spread' => 0, 'color' => 'rgba(22,20,15,0.25)'
            ],
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
    [
        'id'         => 'kids_photo_vbs',
        'elType'     => 'widget',
        'widgetType' => 'image',
        'settings'   => [
            'image'                             => [ 'url' => $photo_vbs_url, 'id' => $photo_vbs_id ],
            '_position'                         => 'absolute',
            '_offset_orientation_h'             => 'end',
            '_offset_x'                         => [ 'unit' => 'px', 'size' => 10 ],
            '_offset_orientation_v'             => 'start',
            '_offset_y'                         => [ 'unit' => 'px', 'size' => 10 ],
            'width'                             => [ 'unit' => 'px', 'size' => 280 ],
            'height'                            => [ 'unit' => 'px', 'size' => 320 ],
            'image_border_radius'               => [
                'unit' => 'px', 'top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'isLinked' => true
            ],
            '_transform_rotate_z'               => [ 'unit' => 'deg', 'size' => 2.5 ],
            'image_box_shadow_box_shadow_type'  => 'yes',
            'image_box_shadow_box_shadow'       => [
                'horizontal' => 0, 'vertical' => 24, 'blur' => 48, 'spread' => 0, 'color' => 'rgba(22,20,15,0.25)'
            ],
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
    [
        'id'         => 'kids_photo_checkin',
        'elType'     => 'widget',
        'widgetType' => 'image',
        'settings'   => [
            'image'                             => [ 'url' => $photo_checkin_url, 'id' => $photo_checkin_id ],
            '_position'                         => 'absolute',
            '_offset_orientation_h'             => 'end',
            '_offset_x'                         => [ 'unit' => 'px', 'size' => 40 ],
            '_offset_orientation_v'             => 'end',
            '_offset_y'                         => [ 'unit' => 'px', 'size' => 20 ],
            'width'                             => [ 'unit' => 'px', 'size' => 300 ],
            'height'                            => [ 'unit' => 'px', 'size' => 220 ],
            'image_border_radius'               => [
                'unit' => 'px', 'top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'isLinked' => true
            ],
            '_transform_rotate_z'               => [ 'unit' => 'deg', 'size' => -1.5 ],
            'image_box_shadow_box_shadow_type'  => 'yes',
            'image_box_shadow_box_shadow'       => [
                'horizontal' => 0, 'vertical' => 24, 'blur' => 48, 'spread' => 0, 'color' => 'rgba(22,20,15,0.25)'
            ],
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
];

file_put_contents( $kids_json_path, json_encode( $kids, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Updated Kids section.\n";

// 4.4 COMING UP & LATEST MESSAGES (home-events-messages.json)
$em_json_path = 'C:/Projects/mpcbc/wordpress/elementor/home-events-messages.json';
$em = json_decode( file_get_contents( $em_json_path ), true );
$em_root  =& $em['content'][0];
$em_inner =& $em_root['elements'][0];
$em_col_events   =& $em_inner['elements'][0];
$em_col_messages =& $em_inner['elements'][1];

// Helper to make an event row container
function create_event_row( $idx, $mon, $day, $title, $time_place ) {
    return [
        'id'       => "event_row_{$idx}",
        'elType'   => 'container',
        'settings' => [
            'content_width'    => 'full',
            'flex_direction'   => 'row',
            'flex_align_items' => 'center',
            'flex_gap'         => [ 'unit' => 'px', 'size' => 24 ],
            'border_border'    => 'solid',
            'border_width'     => [
                'unit' => 'px', 'top' => 1, 'right' => 0, 'bottom' => 0, 'left' => 0, 'isLinked' => false
            ],
            'border_color'     => '#D6CCBC',
            'padding'          => [
                'unit' => 'px', 'top' => 22, 'right' => 0, 'bottom' => 22, 'left' => 0, 'isLinked' => false
            ],
        ],
        'elements' => [
            // Date Box
            [
                'id'       => "event_date_box_{$idx}",
                'elType'   => 'container',
                'settings' => [
                    'width'                => [ 'unit' => 'px', 'size' => 88 ],
                    'background_background'=> 'classic',
                    'background_color'     => '#FAF7F2',
                    '__globals__'          => [ 'background_color' => 'globals/colors?id=paper' ],
                    'border_radius'        => [
                        'unit' => 'px', 'top' => 14, 'right' => 14, 'bottom' => 14, 'left' => 14, 'isLinked' => true
                    ],
                    'padding'              => [
                        'unit' => 'px', 'top' => 10, 'right' => 0, 'bottom' => 10, 'left' => 0, 'isLinked' => false
                    ],
                    'flex_direction'       => 'column',
                    'flex_align_items'     => 'center',
                ],
                'elements' => [
                    [
                        'id'         => "event_date_mon_{$idx}",
                        'elType'     => 'widget',
                        'widgetType' => 'heading',
                        'settings'   => [
                            'title'                  => $mon,
                            'header_size'            => 'span',
                            'title_color'            => '#5C564D',
                            '__globals__'            => [ 'title_color' => 'globals/colors?id=ink_soft' ],
                            'typography_typography'  => 'custom',
                            'typography_font_size'   => [ 'unit' => 'px', 'size' => 14 ],
                            'typography_font_weight' => '600',
                            'align'                  => 'center',
                        ],
                        'isInner'    => false,
                        'elements'   => [],
                    ],
                    [
                        'id'         => "event_date_day_{$idx}",
                        'elType'     => 'widget',
                        'widgetType' => 'heading',
                        'settings'   => [
                            'title'                  => $day,
                            'header_size'            => 'span',
                            'title_color'            => '#16140F',
                            '__globals__'            => [ 'title_color' => 'globals/colors?id=text' ],
                            'typography_typography'  => 'custom',
                            'typography_font_family' => 'Bricolage Grotesque',
                            'typography_font_size'   => [ 'unit' => 'px', 'size' => 36 ],
                            'typography_font_weight' => '700',
                            'typography_line_height' => [ 'unit' => 'em', 'size' => 1 ],
                            'align'                  => 'center',
                        ],
                        'isInner'    => false,
                        'elements'   => [],
                    ],
                ],
                'isInner'  => false,
            ],
            // Event Details
            [
                'id'       => "event_details_{$idx}",
                'elType'   => 'container',
                'settings' => [
                    'content_width' => 'full',
                    'flex_direction'=> 'column',
                    'flex_gap'      => [ 'unit' => 'px', 'size' => 4 ],
                ],
                'elements' => [
                    [
                        'id'         => "event_title_{$idx}",
                        'elType'     => 'widget',
                        'widgetType' => 'heading',
                        'settings'   => [
                            'title'                  => $title,
                            'header_size'            => 'h3',
                            'title_color'            => '#16140F',
                            '__globals__'            => [ 'title_color' => 'globals/colors?id=text' ],
                            'typography_typography'  => 'custom',
                            'typography_font_size'   => [ 'unit' => 'px', 'size' => 21 ],
                            'typography_font_weight' => '600',
                            'link'                   => [ 'url' => '/coming-soon/' ],
                        ],
                        'isInner'    => false,
                        'elements'   => [],
                    ],
                    [
                        'id'         => "event_meta_{$idx}",
                        'elType'     => 'widget',
                        'widgetType' => 'text-editor',
                        'settings'   => [
                            'editor'                => "<p>{$time_place}</p>",
                            'text_color'            => '#5C564D',
                            '__globals__'           => [ 'text_color' => 'globals/colors?id=ink_soft' ],
                            'typography_typography' => 'custom',
                            'typography_font_size'  => [ 'unit' => 'px', 'size' => 16 ],
                        ],
                        'isInner'    => false,
                        'elements'   => [],
                    ],
                ],
                'isInner'  => false,
            ],
        ],
        'isInner'  => false,
    ];
}

$events_title = $em_col_events['elements'][0];
$em_col_events['elements'] = [
    $events_title,
    create_event_row( 1, 'OCT', '12', 'Fall Family Festival', 'Saturday 3:00 PM · Church lawn' ),
    create_event_row( 2, 'OCT', '19', 'Baptism Service', 'Sunday 11:00 AM · Sanctuary' ),
    create_event_row( 3, 'OCT', '26', 'Community Thanksgiving Dinner', 'Saturday 5:30 PM · Fellowship Hall' ),
    [
        'id'         => 'coming_up_link',
        'elType'     => 'widget',
        'widgetType' => 'text-editor',
        'settings'   => [
            'editor'                => '<p><a href="/coming-soon/">See all events</a></p>',
            'text_color'            => '#0E6B5C',
            '__globals__'           => [ 'text_color' => 'globals/colors?id=primary' ],
            'typography_typography' => 'custom',
            'typography_font_size'  => [ 'unit' => 'px', 'size' => 16 ],
            'typography_font_weight'=> '600',
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
];

// Helper to make a sermon message row container
function create_sermon_row( $idx, $image_file, $title, $speaker, $media_map ) {
    $img_id  = $media_map[ $image_file ]['id'] ?? 0;
    $img_url = $media_map[ $image_file ]['url'] ?? '';

    return [
        'id'       => "sermon_row_{$idx}",
        'elType'   => 'container',
        'settings' => [
            'content_width'    => 'full',
            'flex_direction'   => 'row',
            'flex_align_items' => 'center',
            'flex_gap'         => [ 'unit' => 'px', 'size' => 24 ],
            'border_border'    => 'solid',
            'border_width'     => [
                'unit' => 'px', 'top' => 1, 'right' => 0, 'bottom' => 0, 'left' => 0, 'isLinked' => false
            ],
            'border_color'     => '#D6CCBC',
            'padding'          => [
                'unit' => 'px', 'top' => 18, 'right' => 0, 'bottom' => 18, 'left' => 0, 'isLinked' => false
            ],
        ],
        'elements' => [
            // Thumbnail
            [
                'id'         => "sermon_thumb_{$idx}",
                'elType'     => 'widget',
                'widgetType' => 'image',
                'settings'   => [
                    'image'               => [ 'url' => $img_url, 'id' => $img_id ],
                    'width'               => [ 'unit' => 'px', 'size' => 148 ],
                    'height'              => [ 'unit' => 'px', 'size' => 84 ],
                    'image_border_radius' => [
                        'unit' => 'px', 'top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12, 'isLinked' => true
                    ],
                    'link_to'             => 'custom',
                    'link'                => [ 'url' => '/watch/' ],
                ],
                'isInner'    => false,
                'elements'   => [],
            ],
            // Sermon Details
            [
                'id'       => "sermon_details_{$idx}",
                'elType'   => 'container',
                'settings' => [
                    'content_width' => 'full',
                    'flex_direction'=> 'column',
                    'flex_gap'      => [ 'unit' => 'px', 'size' => 4 ],
                ],
                'elements' => [
                    [
                        'id'         => "sermon_title_{$idx}",
                        'elType'     => 'widget',
                        'widgetType' => 'heading',
                        'settings'   => [
                            'title'                  => $title,
                            'header_size'            => 'h3',
                            'title_color'            => '#16140F',
                            '__globals__'            => [ 'title_color' => 'globals/colors?id=text' ],
                            'typography_typography'  => 'custom',
                            'typography_font_size'   => [ 'unit' => 'px', 'size' => 20 ],
                            'typography_font_weight' => '600',
                            'link'                   => [ 'url' => '/watch/' ],
                        ],
                        'isInner'    => false,
                        'elements'   => [],
                    ],
                    [
                        'id'         => "sermon_speaker_{$idx}",
                        'elType'     => 'widget',
                        'widgetType' => 'text-editor',
                        'settings'   => [
                            'editor'                => "<p>{$speaker}</p>",
                            'text_color'            => '#5C564D',
                            '__globals__'           => [ 'text_color' => 'globals/colors?id=ink_soft' ],
                            'typography_typography' => 'custom',
                            'typography_font_size'  => [ 'unit' => 'px', 'size' => 16 ],
                        ],
                        'isInner'    => false,
                        'elements'   => [],
                    ],
                ],
                'isInner'  => false,
            ],
        ],
        'isInner'  => false,
    ];
}

$messages_title = $em_col_messages['elements'][0];
$em_col_messages['elements'] = [
    $messages_title,
    create_sermon_row( 1, 'sermon-english.png', 'Living with Unshakable Hope', 'English · Pastor John Doe', $media_map ),
    create_sermon_row( 2, 'sermon-cantonese.png', '堅守盼望', 'Cantonese · 講員姓名', $media_map ),
    create_sermon_row( 3, 'sermon-mandarin.png', '活出豐盛生命', 'Mandarin · 講員姓名', $media_map ),
    [
        'id'         => 'latest_messages_link',
        'elType'     => 'widget',
        'widgetType' => 'text-editor',
        'settings'   => [
            'editor'                => '<p><a href="/watch/">All sermons &amp; messages →</a></p>',
            'text_color'            => '#0E6B5C',
            '__globals__'           => [ 'text_color' => 'globals/colors?id=primary' ],
            'typography_typography' => 'custom',
            'typography_font_size'  => [ 'unit' => 'px', 'size' => 16 ],
            'typography_font_weight'=> '600',
        ],
        'isInner'    => false,
        'elements'   => [],
    ],
];

file_put_contents( $em_json_path, json_encode( $em, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Updated Events & Messages section.\n";

// 4.5 STAY IN THE LOOP (home-stay-in-loop.json)
$loop_json_path = 'C:/Projects/mpcbc/wordpress/elementor/home-stay-in-loop.json';
$loop = json_decode( file_get_contents( $loop_json_path ), true );
$l_root  =& $loop['content'][0];
$l_info  =& $l_root['elements'][0];
$l_form  =& $l_root['elements'][1];

// Update social links to native icon-list widget
$l_info['elements'][1] = [
    'id'         => 'stay_loop_socials',
    'elType'     => 'widget',
    'widgetType' => 'icon-list',
    'settings'   => [
        'view'                   => 'inline',
        'icon_list'              => [
            [
                'text'          => 'Instagram @mpcbc',
                'link'          => [ 'url' => '#' ],
                'selected_icon' => [ 'value' => 'fab fa-instagram', 'library' => 'fa-brands' ],
            ],
            [
                'text'          => 'YouTube',
                'link'          => [ 'url' => '#' ],
                'selected_icon' => [ 'value' => 'fab fa-youtube', 'library' => 'fa-brands' ],
            ],
            [
                'text'          => 'WeChat 微信群',
                'link'          => [ 'url' => '#' ],
                'selected_icon' => [ 'value' => 'fab fa-weixin', 'library' => 'fa-brands' ],
            ],
        ],
        'space_between'          => [ 'unit' => 'px', 'size' => 28 ],
        'icon_color'             => '#0E6B5C',
        'text_color'             => '#0E6B5C',
        '__globals__'            => [
            'icon_color' => 'globals/colors?id=primary',
            'text_color' => 'globals/colors?id=primary',
        ],
        'typography_typography'  => 'custom',
        'typography_font_size'   => [ 'unit' => 'px', 'size' => 17 ],
        'typography_font_weight' => '600',
    ],
    'isInner'    => false,
    'elements'   => [],
];

// In l_form, delete stay_loop_form_label!
// Elements currently: [ stay_loop_form_label, stay_loop_form_embed ]
$form_embed_widget = null;
foreach ($l_form['elements'] as $el) {
    if ($el['id'] === 'stay_loop_form_embed') {
        $form_embed_widget = $el;
        break;
    }
}
$l_form['elements'] = [ $form_embed_widget ];

file_put_contents( $loop_json_path, json_encode( $loop, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Updated Stay in the Loop section.\n";

// -------------------------------------------------------------
// 5. MERGE HOME AND UPDATE POST 21
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

$home_full_path = 'C:/Projects/mpcbc/wordpress/elementor/home-full.json';
file_put_contents( $home_full_path, json_encode( $home_full, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

update_post_meta( 21, '_elementor_data', wp_slash( json_encode( $merged_content ) ) );
echo "Merged and updated Home page (ID 21).\n";

// Clear Elementor CSS cache
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Cleared Elementor cache.\n";

echo "Rebuild script finished successfully.\n";

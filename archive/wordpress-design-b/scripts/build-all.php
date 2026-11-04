<?php
/**
 * Master Builder Script for MPCBC Design B
 * Constructs all pages, sections, templates and navigation menus.
 * Strictly complies with:
 * - 0 inline style= attributes
 * - 0 layout <div> tags
 * - Native Elementor containers and widgets
 * - Exports each section to wordpress-design-b/elementor/*.json
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit(1);
}

wp_set_current_user( 1 );

$mediaMap = json_decode( file_get_contents( 'C:/Projects/mpcbc/wordpress-design-b/scripts/media-map.json' ), true );
$GLOBALS['mediaMap'] = $mediaMap;
$elemDir = 'C:/Projects/mpcbc/wordpress-design-b/elementor';

// Helper to generate consistent element IDs
function el_id($seed) {
    return substr( md5( $seed ), 0, 8 );
}

// Elementor Helper: Container
function make_c($seed, $settings, $elements = []) {
    return [
        'id'       => el_id($seed),
        'elType'   => 'container',
        'isInner'  => false,
        'settings' => $settings,
        'elements' => $elements,
    ];
}

// Elementor Helper: Heading
function make_h($seed, $title, $tag = 'h2', $settings = []) {
    $defaults = [
        'title'        => $title,
        'header_size'  => $tag,
    ];
    return [
        'id'         => el_id($seed),
        'elType'     => 'widget',
        'isInner'    => false,
        'widgetType' => 'heading',
        'settings'   => array_merge($defaults, $settings),
        'elements'   => [],
    ];
}

// Elementor Helper: Text Editor (plain paragraphs only, NO style= and NO <div)
function make_t($seed, $content, $settings = []) {
    $defaults = [
        'editor' => $content,
    ];
    return [
        'id'         => el_id($seed),
        'elType'     => 'widget',
        'isInner'    => false,
        'widgetType' => 'text-editor',
        'settings'   => array_merge($defaults, $settings),
        'elements'   => [],
    ];
}

// Elementor Helper: Button
function make_b($seed, $text, $url = '#', $settings = []) {
    $defaults = [
        'text' => $text,
        'link' => [
            'url'         => $url,
            'is_external' => false,
            'nofollow'    => false,
        ],
        'border_radius' => [
            'unit'     => 'px',
            'top'      => '999',
            'right'    => '999',
            'bottom'   => '999',
            'left'     => '999',
            'isLinked' => true,
        ],
    ];
    return [
        'id'         => el_id($seed),
        'elType'     => 'widget',
        'isInner'    => false,
        'widgetType' => 'button',
        'settings'   => array_merge($defaults, $settings),
        'elements'   => [],
    ];
}

// Elementor Helper: Image
function make_img($seed, $mediaKey, $settings = []) {
    global $mediaMap;
    $media = isset($mediaMap[$mediaKey]) ? $mediaMap[$mediaKey] : null;
    $defaults = [
        'image' => [
            'url' => $media ? $media['url'] : '',
            'id'  => $media ? $media['id'] : 0,
        ],
    ];
    return [
        'id'         => el_id($seed),
        'elType'     => 'widget',
        'isInner'    => false,
        'widgetType' => 'image',
        'settings'   => array_merge($defaults, $settings),
        'elements'   => [],
    ];
}

// Elementor Helper: Tabs
function make_tabs($seed, $tabs, $settings = []) {
    $defaults = [
        'tabs' => $tabs,
    ];
    return [
        'id'         => el_id($seed),
        'elType'     => 'widget',
        'isInner'    => false,
        'widgetType' => 'tabs',
        'settings'   => array_merge($defaults, $settings),
        'elements'   => [],
    ];
}

// Elementor Helper: Shortcode
function make_shortcode($seed, $shortcode, $settings = []) {
    $defaults = [
        'shortcode' => $shortcode,
    ];
    return [
        'id'         => el_id($seed),
        'elType'     => 'widget',
        'isInner'    => false,
        'widgetType' => 'shortcode',
        'settings'   => array_merge($defaults, $settings),
        'elements'   => [],
    ];
}

echo "Building sections for Design B...\n";

// =============================================================
// SECTION 1: HERO (#top)
// =============================================================
$hero_video_media = $mediaMap['hero-1080.mp4'];
$hero_poster_media = $mediaMap['hero-poster.jpg'];
$home_hero = make_c('sec_hero', [
    '_element_id'           => 'top',
    'css_id'                => 'top',
    'content_width'         => 'full',
    'css_classes'           => 'hero-section',
    'min_height'            => [ 'unit' => 'vh', 'size' => 100 ],
    'min_height_tablet'     => [ 'unit' => 'vh', 'size' => 100 ],
    'min_height_mobile'     => [ 'unit' => 'custom', 'size' => '100svh' ],
    'background_background' => 'video',
    'background_video_link' => $hero_video_media['url'],
    'background_play_once'  => '',
    'background_play_on_mobile' => 'yes',
    'background_video_fallback' => [
        'url' => $hero_poster_media['url'],
        'id'  => $hero_poster_media['id'],
    ],
    'background_overlay_background'     => 'gradient',
    'background_overlay_color'          => 'rgba(27,16,20,0.86)',
    'background_overlay_color_b'        => 'rgba(27,16,20,0.4)',
    'background_overlay_gradient_type'  => 'linear',
    'background_overlay_gradient_angle' => [ 'unit' => 'deg', 'size' => 180 ],
    'padding'               => [ 'unit' => 'px', 'top' => '80', 'right' => '20', 'bottom' => '64', 'left' => '20', 'isLinked' => false ],
    'padding_tablet'        => [ 'unit' => 'px', 'top' => '64', 'right' => '20', 'bottom' => '48', 'left' => '20', 'isLinked' => false ],
    'padding_mobile'        => [ 'unit' => 'px', 'top' => '48', 'right' => '20', 'bottom' => '40', 'left' => '20', 'isLinked' => false ],
    'flex_direction'        => 'column',
    'flex_justify_content'  => 'flex-end',
    'flex_align_items'      => 'center',
], [
    // Inner container (max 1080px)
    make_c('hero_inner', [
        'content_width' => 'boxed',
        'boxed_width'   => [ 'unit' => 'px', 'size' => 1080 ],
        'flex_direction' => 'column',
        'flex_gap'      => [ 'unit' => 'px', 'size' => 24 ],
    ], [
        make_h('hero_h1', 'One church.<br />Short Slogan.', 'h1', [
            'title_color' => '#FFFFFF',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Geist',
            'typography_font_size' => [ 'unit' => 'px', 'size' => 72 ],
            'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 44 ],
            'typography_font_weight' => '700',
            'typography_line_height' => [ 'unit' => 'em', 'size' => 1.02 ],
            'typography_letter_spacing' => [ 'unit' => 'px', 'size' => -3.2 ],
        ]),
        make_h('hero_subtitle', '一間教會，三種語言', 'p', [
            'title_color' => '#FFFFFF',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Noto Serif TC',
            'typography_font_size' => [ 'unit' => 'px', 'size' => 24 ],
            'typography_font_weight' => '600',
        ]),
        make_c('hero_actions', [
            'content_width' => 'full',
            'flex_direction' => 'row',
            'flex_wrap' => 'wrap',
            'flex_align_items' => 'center',
            'flex_gap' => [ 'unit' => 'px', 'size' => 20 ],
        ], [
            make_b('hero_btn_sunday', 'Join us this Sunday →', '#sunday', [
                'background_color' => '#FFFFFF',
                'button_text_color' => '#221419',
                'typography_typography' => 'custom',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 17 ],
                'typography_font_weight' => '600',
                'padding' => [ 'unit' => 'px', 'top' => '14', 'right' => '28', 'bottom' => '14', 'left' => '28', 'isLinked' => false ],
            ]),
            make_b('hero_btn_connect', "Tell us you're coming", '#connect', [
                'background_color' => 'transparent',
                'button_text_color' => '#FFFFFF',
                'border_border' => 'solid',
                'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                'border_color' => 'rgba(255,255,255,0.5)',
                'typography_typography' => 'custom',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 17 ],
                'typography_font_weight' => '600',
                'padding' => [ 'unit' => 'px', 'top' => '14', 'right' => '26', 'bottom' => '14', 'left' => '26', 'isLinked' => false ],
            ]),
            make_t('hero_times_text', '<p><strong>粵語 9:30 · ENGLISH 11:00 · 國語 11:30</strong></p>', [
                'text_color' => 'rgba(255,255,255,0.88)',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Inter',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'typography_font_weight' => '700',
                'typography_letter_spacing' => [ 'unit' => 'px', 'size' => 0.8 ],
            ]),
        ]),
    ]),
]);

// =============================================================
// SECTION 2: SUNDAY SERVICES (#sunday)
// =============================================================
$home_services = make_c('sec_sunday', [
    '_element_id'    => 'sunday',
    'css_id'         => 'sunday',
    'content_width'  => 'boxed',
    'boxed_width'    => [ 'unit' => 'px', 'size' => 1080 ],
    'padding'        => [ 'unit' => 'px', 'top' => '96', 'right' => '20', 'bottom' => '96', 'left' => '20', 'isLinked' => false ],
    'padding_tablet' => [ 'unit' => 'px', 'top' => '64', 'right' => '20', 'bottom' => '64', 'left' => '20', 'isLinked' => false ],
    'padding_mobile' => [ 'unit' => 'px', 'top' => '48', 'right' => '20', 'bottom' => '48', 'left' => '20', 'isLinked' => false ],
    'flex_direction' => 'column',
    'flex_gap'       => [ 'unit' => 'px', 'size' => 36 ],
], [
    // Header row
    make_c('sunday_header_row', [
        'content_width' => 'full',
        'flex_direction' => 'row',
        'flex_wrap' => 'wrap',
        'flex_justify_content' => 'space-between',
        'flex_align_items' => 'flex-end',
        'flex_gap' => [ 'unit' => 'px', 'size' => 20 ],
    ], [
        make_c('sunday_hdr_left', [
            'content_width' => 'full',
            'flex_direction' => 'column',
            'flex_gap' => [ 'unit' => 'px', 'size' => 8 ],
        ], [
            make_t('sunday_kicker', '<p>― THIS SUNDAY 本主日</p>', [
                'text_color' => '#943759',
                '__globals__' => [ 'text_color' => 'globals/colors?id=primary' ],
                'typography_typography' => 'custom',
                'typography_font_family' => 'Inter',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'typography_font_weight' => '700',
            ]),
            make_h('sunday_h2', 'Three congregations, one morning.', 'h2', [
                'title_color' => '#221419',
                '__globals__' => [ 'title_color' => 'globals/colors?id=text' ],
                'typography_typography' => 'custom',
                'typography_font_family' => 'Geist',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ],
                'typography_font_weight' => '600',
                'typography_letter_spacing' => [ 'unit' => 'px', 'size' => -1.4 ],
            ]),
        ]),
        make_t('sunday_hdr_right', '<p>Each congregation streams on its own channel. Live links update automatically from YouTube — nobody edits this weekly.</p>', [
            'text_color' => '#6B5560',
            '__globals__' => [ 'text_color' => 'globals/colors?id=muted' ],
            'typography_typography' => 'custom',
            'typography_font_family' => 'Inter',
            'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
            'typography_font_weight' => '600',
            'typography_line_height' => [ 'unit' => 'em', 'size' => 1.7 ],
        ]),
    ]),

    // 3 Cards Row
    make_c('sunday_cards_row', [
        'content_width' => 'full',
        'flex_direction' => 'row',
        'flex_wrap' => 'wrap',
        'flex_gap' => [ 'unit' => 'px', 'size' => 24 ],
    ], [
        // Card 1: Cantonese
        make_c('card_cantonese', [
            'content_width' => 'full',
            'width' => [ 'unit' => '%', 'size' => 31 ],
            'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
            'border_color' => '#E3D3D8',
            'border_radius' => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
            'padding' => [ 'unit' => 'px', 'top' => '16', 'right' => '16', 'bottom' => '20', 'left' => '16', 'isLinked' => false ],
            'flex_direction' => 'column',
            'flex_gap' => [ 'unit' => 'px', 'size' => 12 ],
        ], [
            make_img('img_canto', 'thumb-cantonese.png', [
                'image_border_border' => 'solid',
                'image_border_radius' => [ 'unit' => 'px', 'top' => '16', 'right' => '16', 'bottom' => '16', 'left' => '16', 'isLinked' => true ],
                'hide_mobile'         => 'hidden-mobile',
            ]),
            make_h('h3_canto', '粵語主日崇拜', 'h3', [
                'title_color' => '#221419',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Noto Serif TC',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 20 ],
                'typography_font_weight' => '600',
            ]),
            make_t('txt_canto_kicker', '<p><strong>CANTONESE · 9:30 AM</strong></p>', [
                'text_color' => '#943759',
                '__globals__' => [ 'text_color' => 'globals/colors?id=primary' ],
                'typography_typography' => 'custom',
                'typography_font_family' => 'Inter',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'typography_font_weight' => '700',
            ]),
            make_t('txt_canto_desc', '<p>Our founding congregation. Preaching in Cantonese, tea afterwards in the courtyard.</p>', [
                'text_color' => '#6B5560',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
            ]),
            make_c('canto_btns', [
                'content_width' => 'full',
                'flex_direction' => 'row',
                'flex_wrap' => 'wrap',
                'flex_gap' => [ 'unit' => 'px', 'size' => 8 ],
            ], [
                make_b('btn_canto_remind', 'Set a reminder', '/#sunday', [
                    'background_color' => 'transparent',
                    'button_text_color' => '#221419',
                    'border_border' => 'solid',
                    'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                    'border_color' => '#E3D3D8',
                    'padding' => [ 'unit' => 'px', 'top' => '8', 'right' => '14', 'bottom' => '8', 'left' => '14', 'isLinked' => false ],
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                ]),
                make_b('btn_canto_archive', 'Archive 存檔', '/#sunday', [
                    'background_color' => 'transparent',
                    'button_text_color' => '#221419',
                    'border_border' => 'solid',
                    'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                    'border_color' => '#E3D3D8',
                    'padding' => [ 'unit' => 'px', 'top' => '8', 'right' => '14', 'bottom' => '8', 'left' => '14', 'isLinked' => false ],
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                ]),
            ]),
        ]),

        // Card 2: English (Active / Live Now)
        make_c('card_english', [
            'content_width' => 'full',
            'width' => [ 'unit' => '%', 'size' => 31 ],
            'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
            'background_background' => 'classic',
            'background_color' => '#F8F3F4',
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
            'border_color' => '#943759',
            '__globals__' => [ 'border_color' => 'globals/colors?id=primary', 'background_color' => 'globals/colors?id=card_bg' ],
            'border_radius' => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
            'padding' => [ 'unit' => 'px', 'top' => '16', 'right' => '16', 'bottom' => '20', 'left' => '16', 'isLinked' => false ],
            'flex_direction' => 'column',
            'flex_gap' => [ 'unit' => 'px', 'size' => 12 ],
        ], [
            make_img('img_eng', 'thumb-english.png', [
                'image_border_border' => 'solid',
                'image_border_radius' => [ 'unit' => 'px', 'top' => '16', 'right' => '16', 'bottom' => '16', 'left' => '16', 'isLinked' => true ],
            ]),
            make_h('h3_eng', 'English Worship', 'h3', [
                'title_color' => '#221419',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Geist',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 20 ],
                'typography_font_weight' => '600',
            ]),
            make_t('txt_eng_kicker', '<p><span class="live-badge-dot"></span> <strong>LIVE NOW · 11:00 AM</strong></p>', [
                'text_color' => '#C2410C',
                '__globals__' => [ 'text_color' => 'globals/colors?id=accent' ],
                'typography_typography' => 'custom',
                'typography_font_family' => 'Inter',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'typography_font_weight' => '700',
            ]),
            make_t('txt_eng_desc', '<p>Mostly the children and grandchildren of the founding families. Sunday school runs at the same hour.</p>', [
                'text_color' => '#6B5560',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
            ]),
            make_c('eng_btns', [
                'content_width' => 'full',
                'flex_direction' => 'row',
                'flex_wrap' => 'wrap',
                'flex_gap' => [ 'unit' => 'px', 'size' => 8 ],
            ], [
                make_b('btn_eng_watch', 'Watch now', '/#sunday', [
                    'background_color' => '#943759',
                    '__globals__' => [ 'background_color' => 'globals/colors?id=primary' ],
                    'button_text_color' => '#FFFFFF',
                    'padding' => [ 'unit' => 'px', 'top' => '8', 'right' => '16', 'bottom' => '8', 'left' => '16', 'isLinked' => false ],
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                ]),
                make_b('btn_eng_archive', 'Archive', '/#sunday', [
                    'background_color' => '#FFFFFF',
                    'button_text_color' => '#221419',
                    'border_border' => 'solid',
                    'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                    'border_color' => '#E3D3D8',
                    'padding' => [ 'unit' => 'px', 'top' => '8', 'right' => '14', 'bottom' => '8', 'left' => '14', 'isLinked' => false ],
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                ]),
            ]),
        ]),

        // Card 3: Mandarin
        make_c('card_mandarin', [
            'content_width' => 'full',
            'width' => [ 'unit' => '%', 'size' => 31 ],
            'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
            'border_color' => '#E3D3D8',
            'border_radius' => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
            'padding' => [ 'unit' => 'px', 'top' => '16', 'right' => '16', 'bottom' => '20', 'left' => '16', 'isLinked' => false ],
            'flex_direction' => 'column',
            'flex_gap' => [ 'unit' => 'px', 'size' => 12 ],
        ], [
            make_img('img_mando', 'thumb-mandarin.png', [
                'image_border_border' => 'solid',
                'image_border_radius' => [ 'unit' => 'px', 'top' => '16', 'right' => '16', 'bottom' => '16', 'left' => '16', 'isLinked' => true ],
                'hide_mobile'         => 'hidden-mobile',
            ]),
            make_h('h3_mando', '國語主日崇拜', 'h3', [
                'title_color' => '#221419',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Noto Serif TC',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 20 ],
                'typography_font_weight' => '600',
            ]),
            make_t('txt_mando_kicker', '<p><strong>MANDARIN · 11:30 AM</strong></p>', [
                'text_color' => '#943759',
                '__globals__' => [ 'text_color' => 'globals/colors?id=primary' ],
                'typography_typography' => 'custom',
                'typography_font_family' => 'Inter',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'typography_font_weight' => '700',
            ]),
            make_t('txt_mando_desc', '<p>Our newest congregation, with traditional-character subtitles. Sunday school at the same hour.</p>', [
                'text_color' => '#6B5560',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
            ]),
            make_c('mando_btns', [
                'content_width' => 'full',
                'flex_direction' => 'row',
                'flex_wrap' => 'wrap',
                'flex_gap' => [ 'unit' => 'px', 'size' => 8 ],
            ], [
                make_b('btn_mando_channel', 'Go to channel', '/#sunday', [
                    'background_color' => 'transparent',
                    'button_text_color' => '#221419',
                    'border_border' => 'solid',
                    'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                    'border_color' => '#E3D3D8',
                    'padding' => [ 'unit' => 'px', 'top' => '8', 'right' => '14', 'bottom' => '8', 'left' => '14', 'isLinked' => false ],
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                ]),
                make_b('btn_mando_archive', 'Archive 存檔', '/#sunday', [
                    'background_color' => 'transparent',
                    'button_text_color' => '#221419',
                    'border_border' => 'solid',
                    'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                    'border_color' => '#E3D3D8',
                    'padding' => [ 'unit' => 'px', 'top' => '8', 'right' => '14', 'bottom' => '8', 'left' => '14', 'isLinked' => false ],
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                ]),
            ]),
        ]),
    ]),

    // Bottom Links Bar
    make_c('sunday_bottom_bar', [
        'content_width' => 'full',
        'border_border' => 'solid',
        'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
        'border_color' => '#E3D3D8',
        'padding' => [ 'unit' => 'px', 'top' => '20', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
        'flex_direction' => 'row',
        'flex_wrap' => 'wrap',
        'flex_justify_content' => 'space-between',
        'flex_align_items' => 'center',
        'flex_gap' => [ 'unit' => 'px', 'size' => 16 ],
    ], [
        make_t('sunday_bot_txt', '<p>Watching from home? Open the player for your congregation. Every service is posted to the sermon archive within a day or two.</p>', [
            'text_color' => '#6B5560',
            'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
        ]),
        make_c('sunday_bot_btns', [
            'content_width' => 'full',
            'flex_direction' => 'row',
            'flex_wrap' => 'wrap',
            'flex_gap' => [ 'unit' => 'px', 'size' => 8 ],
        ], [
            make_b('btn_all_sermons', 'All sermons', '/coming-soon/', [
                'background_color' => 'transparent',
                'button_text_color' => '#221419',
                'border_border' => 'solid',
                'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                'border_color' => '#E3D3D8',
                'padding' => [ 'unit' => 'px', 'top' => '8', 'right' => '16', 'bottom' => '8', 'left' => '16', 'isLinked' => false ],
                'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
            ]),
            make_b('btn_all_yt', 'Our YouTube channels', '/coming-soon/', [
                'background_color' => 'transparent',
                'button_text_color' => '#221419',
                'border_border' => 'solid',
                'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                'border_color' => '#E3D3D8',
                'padding' => [ 'unit' => 'px', 'top' => '8', 'right' => '16', 'bottom' => '8', 'left' => '16', 'isLinked' => false ],
                'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
            ]),
        ]),
    ]),
]);

// =============================================================
// SECTION 3: ANNOUNCEMENTS (#news) WITH WORKING SLIDER (.mp-slider)
// =============================================================
$home_announcements = make_c('sec_news', [
    '_element_id'           => 'news',
    'css_id'                => 'news',
    'content_width'         => 'boxed',
    'boxed_width'           => [ 'unit' => 'px', 'size' => 1080 ],
    'background_background' => 'classic',
    'background_color'      => '#F8F3F4',
    '__globals__'           => [ 'background_color' => 'globals/colors?id=card_bg' ],
    'border_border'         => 'solid',
    'border_width'          => [ 'unit' => 'px', 'top' => '1', 'right' => '0', 'bottom' => '1', 'left' => '0', 'isLinked' => false ],
    'border_color'          => '#E3D3D8',
    'padding'               => [ 'unit' => 'px', 'top' => '88', 'right' => '20', 'bottom' => '88', 'left' => '20', 'isLinked' => false ],
    'padding_tablet'        => [ 'unit' => 'px', 'top' => '64', 'right' => '20', 'bottom' => '64', 'left' => '20', 'isLinked' => false ],
    'padding_mobile'        => [ 'unit' => 'px', 'top' => '48', 'right' => '20', 'bottom' => '48', 'left' => '20', 'isLinked' => false ],
    'flex_direction'        => 'column',
    'flex_gap'              => [ 'unit' => 'px', 'size' => 20 ],
], [
    make_t('news_kicker', '<p>― ANNOUNCEMENTS 最新消息</p>', [
        'text_color' => '#943759',
        '__globals__' => [ 'text_color' => 'globals/colors?id=primary' ],
        'typography_typography' => 'custom',
        'typography_font_family' => 'Inter',
        'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
        'typography_font_weight' => '700',
    ]),

    // Parent .mp-slider container
    make_c('ann_slider_wrap', [
        'content_width' => 'full',
        'css_classes'   => 'mp-slider',
        'flex_direction' => 'column',
    ], [
        // Slide 1: Bible in 365 days
        make_c('slide_1', [
            'content_width' => 'full',
            'background_background' => 'classic',
            'background_color' => '#FFFFFF',
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
            'border_color' => '#E3D3D8',
            'border_radius' => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
            'padding' => [ 'unit' => 'px', 'top' => '48', 'right' => '48', 'bottom' => '48', 'left' => '48', 'isLinked' => true ],
            'padding_tablet' => [ 'unit' => 'px', 'top' => '32', 'right' => '32', 'bottom' => '32', 'left' => '32', 'isLinked' => true ],
            'padding_mobile' => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
            'flex_direction' => 'column',
            'flex_gap' => [ 'unit' => 'px', 'size' => 24 ],
        ], [
            // Slide 1 Header (Kicker + Nav mount)
            make_c('s1_hdr', [
                'content_width' => 'full',
                'flex_direction' => 'row',
                'flex_justify_content' => 'space-between',
                'flex_align_items' => 'center',
            ], [
                make_t('s1_kicker', '<p><strong>NEWSLETTER · SEPTEMBER 2026</strong></p>', [
                    '_element_width' => 'auto',
                    'text_color' => '#6B5560',
                    'typography_font_family' => 'Inter',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                    'typography_font_weight' => '700',
                ]),
                make_c('s1_mount', [ 'content_width' => 'auto', '_element_width' => 'auto', 'css_classes' => 'mp-slider-nav-mount' ]),
            ]),
            // Slide 1 Columns
            make_c('s1_cols', [
                'content_width' => 'full',
                'flex_direction' => 'row',
                'flex_wrap' => 'wrap',
                'flex_gap' => [ 'unit' => 'px', 'size' => 40 ],
                'flex_align_items' => 'flex-start',
            ], [
                // Left
                make_c('s1_left', [
                    'content_width' => 'full',
                    'width' => [ 'unit' => '%', 'size' => 60 ],
                    'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
                    'flex_direction' => 'column',
                    'flex_gap' => [ 'unit' => 'px', 'size' => 16 ],
                ], [
                    make_h('s1_h2', 'Read the Bible together in 365 days.', 'h2', [
                        'title_color' => '#221419',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'Geist',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ],
                        'typography_font_weight' => '600',
                        'typography_letter_spacing' => [ 'unit' => 'px', 'size' => -1.8 ],
                    ]),
                    make_h('s1_zh', '一年讀完聖經 · 全教會一同開始', 'p', [
                        'title_color' => '#6B5560',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'Noto Serif TC',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 20 ],
                    ]),
                    make_t('s1_body', '<p>Starting Sunday, October 4, all three congregations read the same passage each day. There is a printed plan in the foyer, a daily email, and a short discussion question for small groups — in Cantonese, Mandarin, and English.</p>', [
                        'text_color' => '#6B5560',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                    ]),
                    make_b('s1_btn', 'Join the reading plan →', '/coming-soon/', [
                        'background_color' => '#943759',
                        '__globals__' => [ 'background_color' => 'globals/colors?id=primary' ],
                        'button_text_color' => '#FFFFFF',
                        'padding' => [ 'unit' => 'px', 'top' => '14', 'right' => '24', 'bottom' => '14', 'left' => '24', 'isLinked' => false ],
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                        'typography_font_weight' => '600',
                    ]),
                ]),
                // Right
                make_c('s1_right', [
                    'content_width' => 'full',
                    'width' => [ 'unit' => '%', 'size' => 34 ],
                    'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
                    'flex_direction' => 'column',
                    'flex_gap' => [ 'unit' => 'px', 'size' => 16 ],
                ], [
                    make_t('s1_meta', '<p><strong>STARTS:</strong> Sunday, October 4<br><br><strong>PACE:</strong> About 15 minutes a day<br><br><strong>LANGUAGES:</strong> 粵語 · 國語 · English</p>', [
                        'text_color' => '#221419',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                    ]),
                    make_t('s1_note', '<p class="nanum-script">day one is Genesis 1 — catch up anytime</p>'),
                ]),
            ]),
        ]),

        // Slide 2: 50th Anniversary
        make_c('slide_2', [
            'content_width' => 'full',
            'background_background' => 'classic',
            'background_color' => '#FFFFFF',
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
            'border_color' => '#E3D3D8',
            'border_radius' => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
            'padding' => [ 'unit' => 'px', 'top' => '48', 'right' => '48', 'bottom' => '48', 'left' => '48', 'isLinked' => true ],
            'padding_tablet' => [ 'unit' => 'px', 'top' => '32', 'right' => '32', 'bottom' => '32', 'left' => '32', 'isLinked' => true ],
            'padding_mobile' => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
            'flex_direction' => 'column',
            'flex_gap' => [ 'unit' => 'px', 'size' => 24 ],
        ], [
            make_c('s2_hdr', [
                'content_width' => 'full',
                'flex_direction' => 'row',
                'flex_justify_content' => 'space-between',
                'flex_align_items' => 'center',
            ], [
                make_t('s2_kicker', '<p><strong>COMING UP · OCTOBER 2026</strong></p>', [
                    '_element_width' => 'auto',
                    'text_color' => '#6B5560',
                    'typography_font_family' => 'Inter',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                    'typography_font_weight' => '700',
                ]),
                make_c('s2_mount', [ 'content_width' => 'auto', '_element_width' => 'auto', 'css_classes' => 'mp-slider-nav-mount' ]),
            ]),
            make_c('s2_cols', [
                'content_width' => 'full',
                'flex_direction' => 'row',
                'flex_wrap' => 'wrap',
                'flex_gap' => [ 'unit' => 'px', 'size' => 40 ],
                'flex_align_items' => 'flex-start',
            ], [
                make_c('s2_left', [
                    'content_width' => 'full',
                    'width' => [ 'unit' => '%', 'size' => 60 ],
                    'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
                    'flex_direction' => 'column',
                    'flex_gap' => [ 'unit' => 'px', 'size' => 16 ],
                ], [
                    make_h('s2_h2', '50th Anniversary celebration', 'h2', [
                        'title_color' => '#221419',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'Geist',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ],
                        'typography_font_weight' => '600',
                        'typography_letter_spacing' => [ 'unit' => 'px', 'size' => -1.8 ],
                    ]),
                    make_h('s2_zh', '五十週年感恩崇拜 · 三堂聯合', 'p', [
                        'title_color' => '#6B5560',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'Noto Serif TC',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 20 ],
                    ]),
                    make_t('s2_body', '<p>A combined trilingual service marking fifty years, followed by lunch together.</p>', [
                        'text_color' => '#6B5560',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                    ]),
                    make_b('s2_btn', "See what's coming up →", '/#events', [
                        'background_color' => '#943759',
                        '__globals__' => [ 'background_color' => 'globals/colors?id=primary' ],
                        'button_text_color' => '#FFFFFF',
                        'padding' => [ 'unit' => 'px', 'top' => '14', 'right' => '24', 'bottom' => '14', 'left' => '24', 'isLinked' => false ],
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                        'typography_font_weight' => '600',
                    ]),
                ]),
                make_c('s2_right', [
                    'content_width' => 'full',
                    'width' => [ 'unit' => '%', 'size' => 34 ],
                    'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
                    'flex_direction' => 'column',
                    'flex_gap' => [ 'unit' => 'px', 'size' => 16 ],
                ], [
                    make_t('s2_meta', '<p><strong>WHEN:</strong> Sunday, October 18, 10:00 AM<br><br><strong>WHERE:</strong> Sanctuary<br><br><strong>LUNCH:</strong> Fellowship Hall</p>', [
                        'text_color' => '#221419',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                    ]),
                    make_t('s2_note', '<p class="nanum-script">one service, three languages</p>'),
                ]),
            ]),
        ]),

        // Slide 3: Thanksgiving Notice
        make_c('slide_3', [
            'content_width' => 'full',
            'background_background' => 'classic',
            'background_color' => '#FFFFFF',
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
            'border_color' => '#E3D3D8',
            'border_radius' => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
            'padding' => [ 'unit' => 'px', 'top' => '48', 'right' => '48', 'bottom' => '48', 'left' => '48', 'isLinked' => true ],
            'padding_tablet' => [ 'unit' => 'px', 'top' => '32', 'right' => '32', 'bottom' => '32', 'left' => '32', 'isLinked' => true ],
            'padding_mobile' => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
            'flex_direction' => 'column',
            'flex_gap' => [ 'unit' => 'px', 'size' => 24 ],
        ], [
            make_c('s3_hdr', [
                'content_width' => 'full',
                'flex_direction' => 'row',
                'flex_justify_content' => 'space-between',
                'flex_align_items' => 'center',
            ], [
                make_t('s3_kicker', '<p><strong>NOTICE · NOVEMBER 2026</strong></p>', [
                    '_element_width' => 'auto',
                    'text_color' => '#6B5560',
                    'typography_font_family' => 'Inter',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                    'typography_font_weight' => '700',
                ]),
                make_c('s3_mount', [ 'content_width' => 'auto', '_element_width' => 'auto', 'css_classes' => 'mp-slider-nav-mount' ]),
            ]),
            make_c('s3_cols', [
                'content_width' => 'full',
                'flex_direction' => 'row',
                'flex_wrap' => 'wrap',
                'flex_gap' => [ 'unit' => 'px', 'size' => 40 ],
                'flex_align_items' => 'flex-start',
            ], [
                make_c('s3_left', [
                    'content_width' => 'full',
                    'width' => [ 'unit' => '%', 'size' => 60 ],
                    'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
                    'flex_direction' => 'column',
                    'flex_gap' => [ 'unit' => 'px', 'size' => 16 ],
                ], [
                    make_h('s3_h2', 'Office closed for Thanksgiving', 'h2', [
                        'title_color' => '#221419',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'Geist',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ],
                        'typography_font_weight' => '600',
                        'typography_letter_spacing' => [ 'unit' => 'px', 'size' => -1.8 ],
                    ]),
                    make_h('s3_zh', '感恩節辦公室休息', 'p', [
                        'title_color' => '#6B5560',
                        'typography_typography' => 'custom',
                        'typography_font_family' => 'Noto Serif TC',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 20 ],
                    ]),
                    make_t('s3_body', '<p>The church office is closed Thursday and Friday, November 26–27. Sunday services that weekend run at their usual times.</p>', [
                        'text_color' => '#6B5560',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                    ]),
                ]),
                make_c('s3_right', [
                    'content_width' => 'full',
                    'width' => [ 'unit' => '%', 'size' => 34 ],
                    'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
                    'flex_direction' => 'column',
                    'flex_gap' => [ 'unit' => 'px', 'size' => 16 ],
                ], [
                    make_t('s3_meta', '<p><strong>CLOSED:</strong> Thu–Fri, Nov 26–27<br><br><strong>REOPENS:</strong> Monday, 9:00 AM<br><br><strong>SERVICES:</strong> Unaffected</p>', [
                        'text_color' => '#221419',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                    ]),
                ]),
            ]),
        ]),
    ]),
]);

// =============================================================
// SECTION 4: WELCOME NOTE (#welcome)
// =============================================================
$home_welcome = make_c('sec_welcome', [
    '_element_id'    => 'welcome',
    'css_id'         => 'welcome',
    'content_width'  => 'boxed',
    'boxed_width'    => [ 'unit' => 'px', 'size' => 1080 ],
    'padding'        => [ 'unit' => 'px', 'top' => '104', 'right' => '20', 'bottom' => '104', 'left' => '20', 'isLinked' => false ],
    'padding_tablet' => [ 'unit' => 'px', 'top' => '72', 'right' => '20', 'bottom' => '72', 'left' => '20', 'isLinked' => false ],
    'padding_mobile' => [ 'unit' => 'px', 'top' => '56', 'right' => '20', 'bottom' => '56', 'left' => '20', 'isLinked' => false ],
    'flex_direction' => 'row',
    'flex_wrap'      => 'wrap',
    'flex_gap'       => [ 'unit' => 'px', 'size' => 48 ],
    'flex_align_items' => 'flex-start',
], [
    // Left: Pastor Portrait
    make_c('wel_left', [
        'content_width' => 'full',
        'width' => [ 'unit' => '%', 'size' => 25 ],
        'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
        'flex_direction' => 'column',
        'flex_align_items' => 'center',
        'flex_gap' => [ 'unit' => 'px', 'size' => 16 ],
    ], [
        make_img('img_pastor_chan', 'pastor-portrait.png', [
            'width' => [ 'unit' => 'px', 'size' => 224 ],
            'image_border_radius' => [ 'unit' => 'px', 'top' => '999', 'right' => '999', 'bottom' => '999', 'left' => '999', 'isLinked' => true ],
        ]),
        make_t('wel_pastor_info', '<p><strong>Rev. Chan 陳牧師</strong><br>Senior Pastor</p>', [
            'align' => 'center',
            'text_color' => '#221419',
        ]),
    ]),
    // Right: Narrative
    make_c('wel_right', [
        'content_width' => 'full',
        'width' => [ 'unit' => '%', 'size' => 68 ],
        'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
        'flex_direction' => 'column',
        'flex_gap' => [ 'unit' => 'px', 'size' => 16 ],
    ], [
        make_t('wel_kicker', '<p>― A NOTE FROM OUR PASTOR 牧者的話</p>', [
            'text_color' => '#943759',
            '__globals__' => [ 'text_color' => 'globals/colors?id=primary' ],
            'typography_typography' => 'custom',
            'typography_font_family' => 'Inter',
            'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
            'typography_font_weight' => '700',
        ]),
        make_h('wel_h2', 'You are welcome here, whichever language you pray in.', 'h2', [
            'title_color' => '#221419',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Geist',
            'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ],
            'typography_font_weight' => '600',
            'typography_letter_spacing' => [ 'unit' => 'px', 'size' => -1.4 ],
        ]),
        make_t('wel_body_en', '<p>We began in 1976 with a handful of families. Today three congregations share this building, and many of our members are the children and grandchildren of the people who started it.</p>', [
            'text_color' => '#221419',
            'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
        ]),
        make_t('wel_body_zh', '<p class="zh-serif">若您初次來訪，請放心前來，隨意入座。我們不會請您站起來自我介紹。門口的同工會為您指引兒童教室與後方停車場。</p>', [
            'text_color' => '#6B5560',
            'typography_typography' => 'custom',
            'typography_font_family' => 'Noto Serif TC',
            'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
        ]),
        make_b('wel_btn', 'Read our welcome page →', '/coming-soon/', [
            'background_color' => 'transparent',
            'button_text_color' => '#221419',
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
            'border_color' => '#E3D3D8',
            'padding' => [ 'unit' => 'px', 'top' => '12', 'right' => '24', 'bottom' => '12', 'left' => '24', 'isLinked' => false ],
            'typography_font_size' => [ 'unit' => 'px', 'size' => 15 ],
            'typography_font_weight' => '600',
        ]),
    ]),
]);

// =============================================================
// SECTION 5: CONNECT & PRAYER (#connect) WITH TABS & FLUENT FORMS
// =============================================================
$home_connect = make_c('sec_connect', [
    '_element_id'           => 'connect',
    'css_id'                => 'connect',
    'content_width'         => 'full',
    'background_background' => 'classic',
    'background_color'      => '#1B1014',
    '__globals__'           => [ 'background_color' => 'globals/colors?id=secondary' ],
    'padding'               => [ 'unit' => 'px', 'top' => '96', 'right' => '20', 'bottom' => '96', 'left' => '20', 'isLinked' => false ],
    'padding_tablet'        => [ 'unit' => 'px', 'top' => '72', 'right' => '20', 'bottom' => '72', 'left' => '20', 'isLinked' => false ],
    'padding_mobile'        => [ 'unit' => 'px', 'top' => '56', 'right' => '20', 'bottom' => '56', 'left' => '20', 'isLinked' => false ],
    'flex_direction'        => 'column',
    'flex_align_items'      => 'center',
], [
    make_c('connect_inner', [
        'content_width' => 'boxed',
        'boxed_width'   => [ 'unit' => 'px', 'size' => 1080 ],
        'flex_direction' => 'row',
        'flex_wrap' => 'wrap',
        'flex_gap' => [ 'unit' => 'px', 'size' => 48 ],
        'flex_align_items' => 'flex-start',
    ], [
        // Left
        make_c('con_left', [
            'content_width' => 'full',
            'width' => [ 'unit' => '%', 'size' => 45 ],
            'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
            'flex_direction' => 'column',
            'flex_gap' => [ 'unit' => 'px', 'size' => 16 ],
        ], [
            make_t('con_kicker', '<p>SAY HELLO 與我們聯絡</p>', [
                'text_color' => 'rgba(255,255,255,0.6)',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Inter',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'typography_font_weight' => '700',
            ]),
            make_h('con_h2', 'We would rather hear from you than guess.', 'h2', [
                'title_color' => '#FFFFFF',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Geist',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ],
                'typography_font_weight' => '600',
                'typography_letter_spacing' => [ 'unit' => 'px', 'size' => -1.8 ],
            ]),
            make_t('con_body_en', '<p>Two cards, both read by a person. A connect card reaches the pastor of the congregation you name, usually within two days. A prayer request goes to the pastors and, if you allow it, to the Wednesday prayer meeting.</p>', [
                'text_color' => 'rgba(255,255,255,0.75)',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
            ]),
            make_t('con_body_zh', '<p class="zh-serif">您也可以致電教會辦公室，我們有會說粵語、國語及英語的同工。</p>', [
                'text_color' => 'rgba(255,255,255,0.75)',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
            ]),
        ]),
        // Right: White card with Tabs
        make_c('con_card', [
            'content_width' => 'full',
            'width' => [ 'unit' => '%', 'size' => 50 ],
            'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
            'background_background' => 'classic',
            'background_color' => '#FFFFFF',
            'border_radius' => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
            'padding'        => [ 'unit' => 'px', 'top' => '32', 'right' => '32', 'bottom' => '32', 'left' => '32', 'isLinked' => true ],
            'padding_tablet' => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
            'padding_mobile' => [ 'unit' => 'px', 'top' => '20', 'right' => '20', 'bottom' => '20', 'left' => '20', 'isLinked' => true ],
            'flex_direction' => 'column',
        ], [
            make_tabs('con_tabs', [
                [
                    'tab_title'   => 'Connect card 聯絡卡',
                    'tab_content' => '[fluentform id="3"]',
                ],
                [
                    'tab_title'   => 'Prayer request 代禱',
                    'tab_content' => '[fluentform id="4"]',
                ],
            ], [
                'css_classes' => 'connect-tabs',
            ]),
        ]),
    ]),
]);

// =============================================================
// SECTION 6: CHILDREN & FAMILIES (#children)
// =============================================================
$home_children = make_c('sec_children', [
    '_element_id'    => 'children',
    'css_id'         => 'children',
    'content_width'  => 'boxed',
    'boxed_width'    => [ 'unit' => 'px', 'size' => 1080 ],
    'padding'        => [ 'unit' => 'px', 'top' => '104', 'right' => '20', 'bottom' => '104', 'left' => '20', 'isLinked' => false ],
    'padding_tablet' => [ 'unit' => 'px', 'top' => '72', 'right' => '20', 'bottom' => '72', 'left' => '20', 'isLinked' => false ],
    'padding_mobile' => [ 'unit' => 'px', 'top' => '56', 'right' => '20', 'bottom' => '56', 'left' => '20', 'isLinked' => false ],
    'flex_direction' => 'column',
], [
    make_c('kids_card', [
        'content_width'  => 'full',
        'background_background' => 'classic',
        'background_color'      => '#F8F3F4',
        '__globals__'    => [ 'background_color' => 'globals/colors?id=card_bg' ],
        'border_border'  => 'solid',
        'border_width'   => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
        'border_color'   => '#E3D3D8',
        'border_radius'  => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
        'padding'        => [ 'unit' => 'px', 'top' => '48', 'right' => '48', 'bottom' => '48', 'left' => '48', 'isLinked' => true ],
        'padding_tablet' => [ 'unit' => 'px', 'top' => '32', 'right' => '32', 'bottom' => '32', 'left' => '32', 'isLinked' => true ],
        'padding_mobile' => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
        'flex_direction' => 'column',
        'flex_gap'       => [ 'unit' => 'px', 'size' => 32 ],
    ], [
        // Top row
        make_c('kids_top', [
            'content_width' => 'full',
            'flex_direction' => 'row',
            'flex_wrap' => 'wrap',
            'flex_gap' => [ 'unit' => 'px', 'size' => 40 ],
        ], [
            // Left narrative
            make_c('kids_narrative', [
                'content_width' => 'full',
                'width' => [ 'unit' => '%', 'size' => 50 ],
                'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
                'width_mobile' => [ 'unit' => '%', 'size' => 100 ],
                'flex_direction' => 'column',
                'flex_gap' => [ 'unit' => 'px', 'size' => 14 ],
            ], [
                make_h('kids_h2', 'Children & families', 'h2', [
                    'title_color' => '#221419',
                    'typography_typography' => 'custom',
                    'typography_font_family' => 'Geist',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ],
                    'typography_font_weight' => '600',
                ]),
                make_h('kids_zh', '兒童及家庭事工', 'p', [
                    'title_color' => '#6B5560',
                    'typography_typography' => 'custom',
                    'typography_font_family' => 'Noto Serif TC',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 20 ],
                ]),
                make_t('kids_p1', '<p>Children are welcome in every service. Sunday school runs during the 11:00 hour in English and the 11:30 hour in Mandarin, and a staffed nursery is open all morning for the youngest.</p>', [
                    'text_color' => '#221419',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 15 ],
                ]),
                make_t('kids_p2', '<p>Every volunteer who works with children has completed a background check and our safeguarding training.</p>', [
                    'text_color' => '#6B5560',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 15 ],
                ]),
                make_b('kids_btn', 'Children’s ministry 兒童事工 →', '/coming-soon/', [
                    'background_color' => '#943759',
                    '__globals__' => [ 'background_color' => 'globals/colors?id=primary' ],
                    'button_text_color' => '#FFFFFF',
                    'padding' => [ 'unit' => 'px', 'top' => '12', 'right' => '24', 'bottom' => '12', 'left' => '24', 'isLinked' => false ],
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 15 ],
                    'typography_font_weight' => '600',
                ]),
            ]),
            // Right Photos Grid
            make_c('kids_photos', [
                'content_width' => 'full',
                'width' => [ 'unit' => '%', 'size' => 45 ],
                'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
                'width_mobile' => [ 'unit' => '%', 'size' => 100 ],
                'flex_direction' => 'column',
                'flex_gap' => [ 'unit' => 'px', 'size' => 14 ],
            ], [
                make_img('img_kids_room', 'kids-room.png', [
                    'image_border_radius' => [ 'unit' => 'px', 'top' => '16', 'right' => '16', 'bottom' => '16', 'left' => '16', 'isLinked' => true ],
                ]),
                make_c('kids_sub_photos', [
                    'content_width' => 'full',
                    'flex_direction' => 'row',
                    'flex_direction_mobile' => 'row',
                    'flex_gap' => [ 'unit' => 'px', 'size' => 12 ],
                    'flex_gap_mobile' => [ 'unit' => 'px', 'size' => 12 ],
                ], [
                    make_img('img_craft', 'kids-craft.png', [
                        'image_border_radius' => [ 'unit' => 'px', 'top' => '16', 'right' => '16', 'bottom' => '16', 'left' => '16', 'isLinked' => true ],
                        '_element_width' => 'custom',
                        '_element_custom_width' => [ 'unit' => '%', 'size' => 50 ],
                        '_element_width_mobile' => 'custom',
                        '_element_custom_width_mobile' => [ 'unit' => '%', 'size' => 50 ],
                    ]),
                    make_img('img_nursery', 'kids-nursery.png', [
                        'image_border_radius' => [ 'unit' => 'px', 'top' => '16', 'right' => '16', 'bottom' => '16', 'left' => '16', 'isLinked' => true ],
                        '_element_width' => 'custom',
                        '_element_custom_width' => [ 'unit' => '%', 'size' => 50 ],
                        '_element_width_mobile' => 'custom',
                        '_element_custom_width_mobile' => [ 'unit' => '%', 'size' => 50 ],
                    ]),
                ]),
            ]),
        ]),
        // Bottom ministry tiles
        make_c('kids_bottom', [
            'content_width' => 'full',
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
            'border_color' => '#E3D3D8',
            'padding' => [ 'unit' => 'px', 'top' => '24', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
            'flex_direction' => 'row',
            'flex_wrap' => 'wrap',
            'flex_gap' => [ 'unit' => 'px', 'size' => 24 ],
        ], [
            make_c('tile_nursery', [ 'content_width' => 'full', 'width' => [ 'unit' => '%', 'size' => 30 ], 'width_tablet' => [ 'unit' => '%', 'size' => 100 ] ], [
                make_t('txt_tile_nursery', '<p><strong>Nursery · 0–3</strong><br>Open 9:15 AM – 12:45 PM, hallway by the courtyard.</p>', [
                    'text_color' => '#221419',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                ]),
            ]),
            make_c('tile_ss', [ 'content_width' => 'full', 'width' => [ 'unit' => '%', 'size' => 30 ], 'width_tablet' => [ 'unit' => '%', 'size' => 100 ] ], [
                make_t('txt_tile_ss', '<p><strong>Sunday school · 4–11</strong><br>During the 11:00 and 11:30 services, two rooms upstairs.</p>', [
                    'text_color' => '#221419',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                ]),
            ]),
            make_c('tile_youth', [ 'content_width' => 'full', 'width' => [ 'unit' => '%', 'size' => 30 ], 'width_tablet' => [ 'unit' => '%', 'size' => 100 ] ], [
                make_t('txt_tile_youth', '<p><strong>Youth · 12–18</strong><br>Friday evenings in the Fellowship Hall, with supper.</p>', [
                    'text_color' => '#221419',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                ]),
            ]),
        ]),
    ]),
]);

// =============================================================
// SECTION 7: COMING UP / EVENTS (#events)
// =============================================================
$home_events = make_c('sec_events', [
    '_element_id'    => 'events',
    'css_id'         => 'events',
    'content_width'  => 'boxed',
    'boxed_width'    => [ 'unit' => 'px', 'size' => 1080 ],
    'padding'        => [ 'unit' => 'px', 'top' => '0', 'right' => '20', 'bottom' => '104', 'left' => '20', 'isLinked' => false ],
    'padding_tablet' => [ 'unit' => 'px', 'top' => '0', 'right' => '20', 'bottom' => '72', 'left' => '20', 'isLinked' => false ],
    'padding_mobile' => [ 'unit' => 'px', 'top' => '0', 'right' => '20', 'bottom' => '56', 'left' => '20', 'isLinked' => false ],
    'flex_direction' => 'column',
    'flex_gap'       => [ 'unit' => 'px', 'size' => 24 ],
], [
    // Header
    make_c('ev_hdr_row', [
        'content_width' => 'full',
        'flex_direction' => 'row',
        'flex_wrap' => 'wrap',
        'flex_justify_content' => 'space-between',
        'flex_align_items' => 'flex-end',
        'flex_gap' => [ 'unit' => 'px', 'size' => 16 ],
    ], [
        make_c('ev_hdr_left', [ 'content_width' => 'full', 'flex_direction' => 'column', 'flex_gap' => [ 'unit' => 'px', 'size' => 8 ] ], [
            make_t('ev_kicker', '<p>― COMING UP 近期活動</p>', [
                'text_color' => '#943759',
                '__globals__' => [ 'text_color' => 'globals/colors?id=primary' ],
                'typography_typography' => 'custom',
                'typography_font_family' => 'Inter',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'typography_font_weight' => '700',
            ]),
            make_h('ev_h2', 'Gatherings and small groups', 'h2', [
                'title_color' => '#221419',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Geist',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ],
                'typography_font_weight' => '600',
            ]),
        ]),
        make_c('ev_hdr_btns', [ 'content_width' => 'full', 'flex_direction' => 'row', 'flex_wrap' => 'wrap', 'flex_gap' => [ 'unit' => 'px', 'size' => 8 ] ], [
            make_b('btn_ev_cal', 'Open the church calendar', '/coming-soon/', [
                'background_color' => 'transparent',
                'button_text_color' => '#221419',
                'border_border' => 'solid',
                'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                'border_color' => '#E3D3D8',
                'padding' => [ 'unit' => 'px', 'top' => '8', 'right' => '16', 'bottom' => '8', 'left' => '16', 'isLinked' => false ],
                'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
            ]),
            make_b('btn_ev_sub', 'Subscribe in your app', '/coming-soon/', [
                'background_color' => 'transparent',
                'button_text_color' => '#221419',
                'border_border' => 'solid',
                'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                'border_color' => '#E3D3D8',
                'padding' => [ 'unit' => 'px', 'top' => '8', 'right' => '16', 'bottom' => '8', 'left' => '16', 'isLinked' => false ],
                'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
            ]),
        ]),
    ]),
    make_t('ev_subtext', '<p>Pulled live from the church Google Calendar. Add or change an event there and it appears here.</p>', [
        'text_color' => '#6B5560',
        'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
    ]),

    // 5 Rows List
    make_c('ev_list', [
        'content_width' => 'full',
        'border_border' => 'solid',
        'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
        'border_color' => '#E3D3D8',
        'flex_direction' => 'column',
    ], [
        // Row 1
        make_c('ev_row_1', [
            'content_width' => 'full',
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '1', 'left' => '0', 'isLinked' => false ],
            'border_color' => '#E3D3D8',
            'padding' => [ 'unit' => 'px', 'top' => '18', 'right' => '0', 'bottom' => '18', 'left' => '0', 'isLinked' => false ],
            'flex_direction' => 'row',
            'flex_wrap' => 'wrap',
            'flex_justify_content' => 'start',
            'flex_align_items' => 'baseline',
            'flex_gap' => [ 'unit' => 'px', 'size' => 24 ],
        ], [
            make_t('ev1_d', '<p><strong>SEP 4</strong></p>', [
                'text_color' => '#943759',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                '_element_width' => 'custom',
                '_element_custom_width' => [ 'unit' => 'px', 'size' => 140 ],
                '_flex_grow' => 0,
                '_flex_shrink' => 0,
            ]),
            make_t('ev1_t', '<p><strong>Friday fellowship 週五團契</strong><br>Singing, teaching, and supper. All ages welcome.</p>', [
                'text_color' => '#221419',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                '_flex_grow' => 1,
                '_flex_shrink' => 1,
            ]),
            make_t('ev1_m', '<p>7:30 PM · Fellowship Hall</p>', [
                'text_color' => '#6B5560',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'align' => 'right',
                '_flex_grow' => 0,
                '_flex_shrink' => 0,
            ]),
        ]),
        // Row 2
        make_c('ev_row_2', [
            'content_width' => 'full',
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '1', 'left' => '0', 'isLinked' => false ],
            'border_color' => '#E3D3D8',
            'padding' => [ 'unit' => 'px', 'top' => '18', 'right' => '0', 'bottom' => '18', 'left' => '0', 'isLinked' => false ],
            'flex_direction' => 'row',
            'flex_wrap' => 'wrap',
            'flex_justify_content' => 'start',
            'flex_align_items' => 'baseline',
            'flex_gap' => [ 'unit' => 'px', 'size' => 24 ],
        ], [
            make_t('ev2_d', '<p><strong>SEP 6</strong></p>', [
                'text_color' => '#943759',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                '_element_width' => 'custom',
                '_element_custom_width' => [ 'unit' => 'px', 'size' => 140 ],
                '_flex_grow' => 0,
                '_flex_shrink' => 0,
            ]),
            make_t('ev2_t', '<p><strong>Church picnic 教會野餐</strong><br>Barnes Park after the 11:30 service. Bring a dish to share.</p>', [
                'text_color' => '#221419',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                '_flex_grow' => 1,
                '_flex_shrink' => 1,
            ]),
            make_t('ev2_m', '<p>1:00 PM · Barnes Park</p>', [
                'text_color' => '#6B5560',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'align' => 'right',
                '_flex_grow' => 0,
                '_flex_shrink' => 0,
            ]),
        ]),
        // Row 3
        make_c('ev_row_3', [
            'content_width' => 'full',
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '1', 'left' => '0', 'isLinked' => false ],
            'border_color' => '#E3D3D8',
            'padding' => [ 'unit' => 'px', 'top' => '18', 'right' => '0', 'bottom' => '18', 'left' => '0', 'isLinked' => false ],
            'flex_direction' => 'row',
            'flex_wrap' => 'wrap',
            'flex_justify_content' => 'start',
            'flex_align_items' => 'baseline',
            'flex_gap' => [ 'unit' => 'px', 'size' => 24 ],
        ], [
            make_t('ev3_d', '<p><strong>SEP 12</strong></p>', [
                'text_color' => '#943759',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                '_element_width' => 'custom',
                '_element_custom_width' => [ 'unit' => 'px', 'size' => 140 ],
                '_flex_grow' => 0,
                '_flex_shrink' => 0,
            ]),
            make_t('ev3_t', '<p><strong>Small groups begin 小組開始</strong><br>Six groups across three languages, meeting in homes.</p>', [
                'text_color' => '#221419',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                '_flex_grow' => 1,
                '_flex_shrink' => 1,
            ]),
            make_t('ev3_m', '<p>Weekly · Various</p>', [
                'text_color' => '#6B5560',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'align' => 'right',
                '_flex_grow' => 0,
                '_flex_shrink' => 0,
            ]),
        ]),
        // Row 4
        make_c('ev_row_4', [
            'content_width' => 'full',
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '1', 'left' => '0', 'isLinked' => false ],
            'border_color' => '#E3D3D8',
            'padding' => [ 'unit' => 'px', 'top' => '18', 'right' => '0', 'bottom' => '18', 'left' => '0', 'isLinked' => false ],
            'flex_direction' => 'row',
            'flex_wrap' => 'wrap',
            'flex_justify_content' => 'start',
            'flex_align_items' => 'baseline',
            'flex_gap' => [ 'unit' => 'px', 'size' => 24 ],
        ], [
            make_t('ev4_d', '<p><strong>SEP 20</strong></p>', [
                'text_color' => '#943759',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                '_element_width' => 'custom',
                '_element_custom_width' => [ 'unit' => 'px', 'size' => 140 ],
                '_flex_grow' => 0,
                '_flex_shrink' => 0,
            ]),
            make_t('ev4_t', '<p><strong>Children’s teacher training 兒童教師訓練</strong><br>Safeguarding refresher for all Sunday school volunteers.</p>', [
                'text_color' => '#221419',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                '_flex_grow' => 1,
                '_flex_shrink' => 1,
            ]),
            make_t('ev4_m', '<p>9:00 AM · Room 2</p>', [
                'text_color' => '#6B5560',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'align' => 'right',
                '_flex_grow' => 0,
                '_flex_shrink' => 0,
            ]),
        ]),
        // Row 5
        make_c('ev_row_5', [
            'content_width' => 'full',
            'border_border' => 'solid',
            'border_width' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '1', 'left' => '0', 'isLinked' => false ],
            'border_color' => '#E3D3D8',
            'padding' => [ 'unit' => 'px', 'top' => '18', 'right' => '0', 'bottom' => '18', 'left' => '0', 'isLinked' => false ],
            'flex_direction' => 'row',
            'flex_wrap' => 'wrap',
            'flex_justify_content' => 'start',
            'flex_align_items' => 'baseline',
            'flex_gap' => [ 'unit' => 'px', 'size' => 24 ],
        ], [
            make_t('ev5_d', '<p><strong>OCT 18</strong></p>', [
                'text_color' => '#943759',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                '_element_width' => 'custom',
                '_element_custom_width' => [ 'unit' => 'px', 'size' => 140 ],
                '_flex_grow' => 0,
                '_flex_shrink' => 0,
            ]),
            make_t('ev5_t', '<p><strong>50th Anniversary celebration</strong><br>A combined trilingual service marking fifty years, followed by lunch.</p>', [
                'text_color' => '#221419',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                '_flex_grow' => 1,
                '_flex_shrink' => 1,
            ]),
            make_t('ev5_m', '<p>10:00 AM · Sanctuary</p>', [
                'text_color' => '#6B5560',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'align' => 'right',
                '_flex_grow' => 0,
                '_flex_shrink' => 0,
            ]),
        ]),
    ]),
]);

// =============================================================
// SECTION 8: PASTORS & LEADERS (#leaders)
// =============================================================
$home_leaders = make_c('sec_leaders', [
    '_element_id'           => 'leaders',
    'css_id'                => 'leaders',
    'content_width'         => 'boxed',
    'boxed_width'           => [ 'unit' => 'px', 'size' => 1080 ],
    'background_background' => 'classic',
    'background_color'      => '#F8F3F4',
    '__globals__'           => [ 'background_color' => 'globals/colors?id=card_bg' ],
    'border_border'         => 'solid',
    'border_width'          => [ 'unit' => 'px', 'top' => '1', 'right' => '0', 'bottom' => '1', 'left' => '0', 'isLinked' => false ],
    'border_color'          => '#E3D3D8',
    'padding'               => [ 'unit' => 'px', 'top' => '96', 'right' => '20', 'bottom' => '96', 'left' => '20', 'isLinked' => false ],
    'padding_tablet'        => [ 'unit' => 'px', 'top' => '72', 'right' => '20', 'bottom' => '72', 'left' => '20', 'isLinked' => false ],
    'padding_mobile'        => [ 'unit' => 'px', 'top' => '56', 'right' => '20', 'bottom' => '56', 'left' => '20', 'isLinked' => false ],
    'flex_direction'        => 'column',
    'flex_gap'              => [ 'unit' => 'px', 'size' => 32 ],
], [
    make_t('lead_kicker', '<p>― OUR PASTORS 教牧同工</p>', [
        'text_color' => '#943759',
        '__globals__' => [ 'text_color' => 'globals/colors?id=primary' ],
        'typography_typography' => 'custom',
        'typography_font_family' => 'Inter',
        'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
        'typography_font_weight' => '700',
    ]),
    make_h('lead_h2', 'Know who you are walking in to meet.', 'h2', [
        'title_color' => '#221419',
        'typography_typography' => 'custom',
        'typography_font_family' => 'Geist',
        'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ],
        'typography_font_weight' => '600',
    ]),
    make_t('lead_sub', '<p>Each congregation has its own pastor, and all three share the same eldership. Any of them will meet you for tea without an appointment.</p>', [
        'text_color' => '#6B5560',
        'typography_font_size' => [ 'unit' => 'px', 'size' => 15 ],
    ]),

    // 3 Cards
    make_c('lead_cards', [
        'content_width' => 'full',
        'flex_direction' => 'row',
        'flex_wrap' => 'wrap',
        'flex_gap' => [ 'unit' => 'px', 'size' => 28 ],
    ], [
        // Pastor 1: Cantonese
        make_c('p_canto', [
            'content_width' => 'full',
            'width' => [ 'unit' => '%', 'size' => 31 ],
            'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
            'flex_direction' => 'column',
            'flex_direction_mobile' => 'row',
            'flex_gap' => [ 'unit' => 'px', 'size' => 12 ],
            'flex_gap_mobile' => [ 'unit' => 'px', 'size' => 16 ],
            'flex_align_items_mobile' => 'flex-start',
        ], [
            make_img('img_p_canto', 'pastor-cantonese.png', [
                'image_border_radius' => [ 'unit' => 'px', 'top' => '18', 'right' => '18', 'bottom' => '18', 'left' => '18', 'isLinked' => true ],
                '_element_width_mobile' => 'custom',
                '_element_custom_width_mobile' => [ 'unit' => 'px', 'size' => 112 ],
            ]),
            make_c('p_canto_info', [
                'content_width' => 'full',
                'flex_direction' => 'column',
                'flex_gap' => [ 'unit' => 'px', 'size' => 8 ],
                '_flex_grow' => 1,
            ], [
                make_t('p_canto_role', '<p><strong>SENIOR PASTOR · CANTONESE</strong></p>', [
                    'text_color' => '#943759',
                    '__globals__' => [ 'text_color' => 'globals/colors?id=primary' ],
                    'typography_font_family' => 'Inter',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 12 ],
                    'typography_font_weight' => '700',
                ]),
                make_h('p_canto_zh', '陳志明 牧師', 'h3', [
                    'title_color' => '#221419',
                    'typography_typography' => 'custom',
                    'typography_font_family' => 'Noto Serif TC',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 22 ],
                    'typography_font_weight' => '600',
                ]),
                make_t('p_canto_en', '<p><strong>Rev. Peter Chan</strong></p>', [
                    'text_color' => '#221419',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                ]),
                make_t('p_canto_bio', '<p>Has preached here since 1998. Writes the pastor’s note in the weekly bulletin and visits the hospital every Tuesday.</p>', [
                    'text_color' => '#6B5560',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                ]),
            ]),
        ]),

        // Pastor 2: English
        make_c('p_eng', [
            'content_width' => 'full',
            'width' => [ 'unit' => '%', 'size' => 31 ],
            'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
            'flex_direction' => 'column',
            'flex_direction_mobile' => 'row',
            'flex_gap' => [ 'unit' => 'px', 'size' => 12 ],
            'flex_gap_mobile' => [ 'unit' => 'px', 'size' => 16 ],
            'flex_align_items_mobile' => 'flex-start',
        ], [
            make_img('img_p_eng', 'pastor-english.png', [
                'image_border_radius' => [ 'unit' => 'px', 'top' => '18', 'right' => '18', 'bottom' => '18', 'left' => '18', 'isLinked' => true ],
                '_element_width_mobile' => 'custom',
                '_element_custom_width_mobile' => [ 'unit' => 'px', 'size' => 112 ],
            ]),
            make_c('p_eng_info', [
                'content_width' => 'full',
                'flex_direction' => 'column',
                'flex_gap' => [ 'unit' => 'px', 'size' => 8 ],
                '_flex_grow' => 1,
            ], [
                make_t('p_eng_role', '<p><strong>PASTOR · ENGLISH</strong></p>', [
                    'text_color' => '#943759',
                    '__globals__' => [ 'text_color' => 'globals/colors?id=primary' ],
                    'typography_font_family' => 'Inter',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 12 ],
                    'typography_font_weight' => '700',
                ]),
                make_h('p_eng_name', 'Rev. David Lam', 'h3', [
                    'title_color' => '#221419',
                    'typography_typography' => 'custom',
                    'typography_font_family' => 'Geist',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 22 ],
                    'typography_font_weight' => '600',
                ]),
                make_t('p_eng_zh', '<p class="zh-serif">林大偉 牧師</p>', [
                    'text_color' => '#6B5560',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                ]),
                make_t('p_eng_bio', '<p>Grew up in this church’s Sunday school. Leads the English congregation and the youth fellowship with his wife, Amy.</p>', [
                    'text_color' => '#6B5560',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                ]),
            ]),
        ]),

        // Pastor 3: Mandarin
        make_c('p_mando', [
            'content_width' => 'full',
            'width' => [ 'unit' => '%', 'size' => 31 ],
            'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
            'flex_direction' => 'column',
            'flex_direction_mobile' => 'row',
            'flex_gap' => [ 'unit' => 'px', 'size' => 12 ],
            'flex_gap_mobile' => [ 'unit' => 'px', 'size' => 16 ],
            'flex_align_items_mobile' => 'flex-start',
        ], [
            make_img('img_p_mando', 'pastor-mandarin.png', [
                'image_border_radius' => [ 'unit' => 'px', 'top' => '18', 'right' => '18', 'bottom' => '18', 'left' => '18', 'isLinked' => true ],
                '_element_width_mobile' => 'custom',
                '_element_custom_width_mobile' => [ 'unit' => 'px', 'size' => 112 ],
            ]),
            make_c('p_mando_info', [
                'content_width' => 'full',
                'flex_direction' => 'column',
                'flex_gap' => [ 'unit' => 'px', 'size' => 8 ],
                '_flex_grow' => 1,
            ], [
                make_t('p_mando_role', '<p><strong>PASTOR · MANDARIN</strong></p>', [
                    'text_color' => '#943759',
                    '__globals__' => [ 'text_color' => 'globals/colors?id=primary' ],
                    'typography_font_family' => 'Inter',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 12 ],
                    'typography_font_weight' => '700',
                ]),
                make_h('p_mando_zh', '王慧珍 牧師', 'h3', [
                    'title_color' => '#221419',
                    'typography_typography' => 'custom',
                    'typography_font_family' => 'Noto Serif TC',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 22 ],
                    'typography_font_weight' => '600',
                ]),
                make_t('p_mando_en', '<p><strong>Rev. Grace Wang</strong></p>', [
                    'text_color' => '#221419',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                ]),
                make_t('p_mando_bio', '<p>Planted our Mandarin congregation in 2011. Teaches the newcomers’ class and coordinates the Friday small groups.</p>', [
                    'text_color' => '#6B5560',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                ]),
            ]),
        ]),
    ]),
    make_t('lead_footer_note', '<p>Deacons, ministry staff, and the church office are listed on the <a href="/coming-soon/">full staff page</a>.</p>', [
        'text_color' => '#6B5560',
        'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
    ]),
]);

// =============================================================
// SECTION 9: GIVING (#give)
// =============================================================
$home_giving = make_c('sec_giving', [
    '_element_id'    => 'give',
    'css_id'         => 'give',
    'content_width'  => 'boxed',
    'boxed_width'    => [ 'unit' => 'px', 'size' => 1080 ],
    'padding'        => [ 'unit' => 'px', 'top' => '96', 'right' => '20', 'bottom' => '104', 'left' => '20', 'isLinked' => false ],
    'padding_tablet' => [ 'unit' => 'px', 'top' => '72', 'right' => '20', 'bottom' => '72', 'left' => '20', 'isLinked' => false ],
    'padding_mobile' => [ 'unit' => 'px', 'top' => '56', 'right' => '20', 'bottom' => '56', 'left' => '20', 'isLinked' => false ],
    'flex_direction' => 'column',
], [
    make_c('give_card', [
        'content_width'  => 'full',
        'background_background' => 'classic',
        'background_color'      => '#1B1014',
        '__globals__'    => [ 'background_color' => 'globals/colors?id=secondary' ],
        'border_radius'  => [ 'unit' => 'px', 'top' => '24', 'right' => '24', 'bottom' => '24', 'left' => '24', 'isLinked' => true ],
        'padding'        => [ 'unit' => 'px', 'top' => '51', 'right' => '51', 'bottom' => '51', 'left' => '51', 'isLinked' => true ],
        'padding_tablet' => [ 'unit' => 'px', 'top' => '32', 'right' => '32', 'bottom' => '32', 'left' => '32', 'isLinked' => true ],
        'padding_mobile' => [ 'unit' => 'px', 'top' => '26', 'right' => '26', 'bottom' => '26', 'left' => '26', 'isLinked' => true ],
        'flex_direction' => 'row',
        'flex_wrap'      => 'wrap',
        'flex_gap'       => [ 'unit' => 'px', 'size' => 48 ],
    ], [
        // Left
        make_c('give_left', [
            'content_width' => 'full',
            'width' => [ 'unit' => '%', 'size' => 55 ],
            'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
            'flex_direction' => 'column',
            'flex_gap' => [ 'unit' => 'px', 'size' => 16 ],
        ], [
            make_t('give_kicker', '<p>GIVING 奉獻</p>', [
                'text_color' => 'rgba(255,255,255,0.6)',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Inter',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'typography_font_weight' => '700',
            ]),
            make_h('give_h2', 'Giving here is worship, not a subscription.', 'h2', [
                'title_color' => '#FFFFFF',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Geist',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 36 ],
                'typography_font_weight' => '600',
                'typography_letter_spacing' => [ 'unit' => 'px', 'size' => -1.6 ],
            ]),
            make_t('give_p1', '<p>Fifty years of offerings built this building, sent our first missionaries, and keep the food pantry open on Saturdays. We do not pass a plate at visitors, and no one is asked for a pledge.</p>', [
                'text_color' => 'rgba(255,255,255,0.75)',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
            ]),
            make_t('give_p2', '<p class="zh-serif">奉獻是感恩的回應，不是責任。</p>', [
                'text_color' => 'rgba(255,255,255,0.75)',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
            ]),
        ]),
        // Right
        make_c('give_right', [
            'content_width' => 'full',
            'width' => [ 'unit' => '%', 'size' => 38 ],
            'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
            'flex_direction' => 'column',
            'flex_gap' => [ 'unit' => 'px', 'size' => 14 ],
        ], [
            make_b('btn_give_online', 'Give online →', '/coming-soon/', [
                'background_color' => '#FFFFFF',
                'button_text_color' => '#221419',
                'padding' => [ 'unit' => 'px', 'top' => '16', 'right' => '24', 'bottom' => '16', 'left' => '24', 'isLinked' => false ],
                'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                'typography_font_weight' => '600',
            ]),
            make_b('btn_give_monthly', 'Set up monthly giving →', '/coming-soon/', [
                'background_color' => 'transparent',
                'button_text_color' => '#FFFFFF',
                'border_border' => 'solid',
                'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                'border_color' => 'rgba(255,255,255,0.35)',
                'padding' => [ 'unit' => 'px', 'top' => '16', 'right' => '24', 'bottom' => '16', 'left' => '24', 'isLinked' => false ],
                'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                'typography_font_weight' => '600',
            ]),
            make_b('btn_give_mail', 'Mail a check or give by mail →', '/coming-soon/', [
                'background_color' => 'transparent',
                'button_text_color' => '#FFFFFF',
                'border_border' => 'solid',
                'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                'border_color' => 'rgba(255,255,255,0.35)',
                'padding' => [ 'unit' => 'px', 'top' => '16', 'right' => '24', 'bottom' => '16', 'left' => '24', 'isLinked' => false ],
                'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
                'typography_font_weight' => '600',
            ]),
            make_t('give_tax_note', '<p>A 501(c)(3) receipt is mailed every January. Designated gifts — missions, the building fund, benevolence — can be chosen at checkout.</p>', [
                'text_color' => 'rgba(255,255,255,0.6)',
                'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                'typography_line_height' => [ 'unit' => 'em', 'size' => 1.7 ],
            ]),
        ]),
    ]),
]);

// =============================================================
// FOOTER TEMPLATE ("MPCBC – Footer")
// =============================================================
$footer_content = [
    make_c('sec_footer', [
        'content_width'         => 'full',
        'background_background' => 'classic',
        'background_color'      => '#1B1014',
        '__globals__'           => [ 'background_color' => 'globals/colors?id=secondary' ],
        'padding'               => [ 'unit' => 'px', 'top' => '64', 'right' => '20', 'bottom' => '64', 'left' => '20', 'isLinked' => false ],
        'padding_tablet'        => [ 'unit' => 'px', 'top' => '48', 'right' => '20', 'bottom' => '48', 'left' => '20', 'isLinked' => false ],
        'padding_mobile'        => [ 'unit' => 'px', 'top' => '40', 'right' => '20', 'bottom' => '40', 'left' => '20', 'isLinked' => false ],
        'flex_direction'        => 'column',
        'flex_align_items'      => 'center',
    ], [
        make_c('footer_inner', [
            'content_width' => 'boxed',
            'boxed_width'   => [ 'unit' => 'px', 'size' => 1080 ],
            'flex_direction' => 'column',
            'flex_gap' => [ 'unit' => 'px', 'size' => 40 ],
        ], [
            // 3 Columns Row
            make_c('footer_cols_row', [
                'content_width' => 'full',
                'flex_direction' => 'row',
                'flex_wrap' => 'wrap',
                'flex_justify_content' => 'space-between',
                'flex_gap' => [ 'unit' => 'px', 'size' => 40 ],
            ], [
                // Col 1: Logo & Info
                make_c('ft_col_1', [
                    'content_width' => 'full',
                    'width' => [ 'unit' => '%', 'size' => 32 ],
                    'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
                    'flex_direction' => 'column',
                    'flex_gap' => [ 'unit' => 'px', 'size' => 12 ],
                ], [
                    make_img('ft_logo', 'logo.png', [
                        'align' => 'left',
                        'align_tablet' => 'left',
                        'align_mobile' => 'left',
                        'width' => [ 'unit' => 'px', 'size' => 180 ],
                    ]),
                    make_t('ft_addr', '<p>000 Garvey Avenue<br>Monterey Park, CA 91754</p>', [
                        'text_color' => 'rgba(255,255,255,0.7)',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                    ]),
                    make_b('ft_btn_dir', 'Get directions 交通指引 ↗', 'https://maps.google.com/?q=Monterey+Park+Chinese+Baptist+Church', [
                        'background_color' => 'transparent',
                        'button_text_color' => '#FFFFFF',
                        'border_border' => 'solid',
                        'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                        'border_color' => 'rgba(255,255,255,0.35)',
                        'padding' => [ 'unit' => 'px', 'top' => '8', 'right' => '16', 'bottom' => '8', 'left' => '16', 'isLinked' => false ],
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                    ]),
                    make_t('ft_parking', '<p>Free parking in the lot off the alley, and on the street after 9 AM Sunday.</p>', [
                        'text_color' => 'rgba(255,255,255,0.7)',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                    ]),
                    make_t('ft_contact', '<p><a href="tel:+16265550100">(626) 555-0100</a><br><a href="mailto:office@mpcbc.org">office@mpcbc.org</a></p>', [
                        'text_color' => '#FFFFFF',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                    ]),
                    make_c('ft_social_row', [
                        'content_width' => 'full',
                        'flex_direction' => 'row',
                        'flex_wrap' => 'wrap',
                        'flex_gap' => [ 'unit' => 'px', 'size' => 8 ],
                    ], [
                        make_b('ft_fb', 'Facebook', '#', [
                            'background_color' => 'transparent',
                            'button_text_color' => '#FFFFFF',
                            'border_border' => 'solid',
                            'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                            'border_color' => 'rgba(255,255,255,0.3)',
                            'padding' => [ 'unit' => 'px', 'top' => '6', 'right' => '12', 'bottom' => '6', 'left' => '12', 'isLinked' => false ],
                            'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                        ]),
                        make_b('ft_yt', 'YouTube', '#', [
                            'background_color' => 'transparent',
                            'button_text_color' => '#FFFFFF',
                            'border_border' => 'solid',
                            'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                            'border_color' => 'rgba(255,255,255,0.3)',
                            'padding' => [ 'unit' => 'px', 'top' => '6', 'right' => '12', 'bottom' => '6', 'left' => '12', 'isLinked' => false ],
                            'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                        ]),
                        make_b('ft_ig', 'Instagram', '#', [
                            'background_color' => 'transparent',
                            'button_text_color' => '#FFFFFF',
                            'border_border' => 'solid',
                            'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'isLinked' => true ],
                            'border_color' => 'rgba(255,255,255,0.3)',
                            'padding' => [ 'unit' => 'px', 'top' => '6', 'right' => '12', 'bottom' => '6', 'left' => '12', 'isLinked' => false ],
                            'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                        ]),
                    ]),
                ]),

                // Col 2: Services & Hours
                make_c('ft_col_2', [
                    'content_width' => 'full',
                    'width' => [ 'unit' => '%', 'size' => 28 ],
                    'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
                    'flex_direction' => 'column',
                    'flex_gap' => [ 'unit' => 'px', 'size' => 14 ],
                ], [
                    make_t('ft_services_kicker', '<p><strong>SUNDAY SERVICES</strong></p>', [
                        'text_color' => 'rgba(255,255,255,0.6)',
                        'typography_font_family' => 'Inter',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                        'typography_font_weight' => '700',
                    ]),
                    make_t('ft_services_list', '<p>9:30 AM — 粵語 Cantonese<br>11:00 AM — English<br>11:30 AM — 國語 Mandarin</p>', [
                        'text_color' => '#FFFFFF',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                    ]),
                    make_t('ft_hours_kicker', '<p><strong>OFFICE HOURS</strong></p>', [
                        'text_color' => 'rgba(255,255,255,0.6)',
                        'typography_font_family' => 'Inter',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                        'typography_font_weight' => '700',
                    ]),
                    make_t('ft_hours_desc', '<p>Tue – Fri, 9:00 AM – 4:00 PM<br>Closed Monday</p>', [
                        'text_color' => 'rgba(255,255,255,0.7)',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                    ]),
                ]),

                // Col 3: Newsletter
                make_c('ft_col_3', [
                    'content_width' => 'full',
                    'width' => [ 'unit' => '%', 'size' => 32 ],
                    'width_tablet' => [ 'unit' => '%', 'size' => 100 ],
                    'flex_direction' => 'column',
                    'flex_gap' => [ 'unit' => 'px', 'size' => 14 ],
                ], [
                    make_t('ft_news_kicker', '<p><strong>WEEKLY EMAIL 每週通訊</strong></p>', [
                        'text_color' => 'rgba(255,255,255,0.6)',
                        'typography_font_family' => 'Inter',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                        'typography_font_weight' => '700',
                    ]),
                    make_t('ft_news_desc', '<p>Announcements, service notes, and prayer requests. One email, Thursday mornings.</p>', [
                        'text_color' => 'rgba(255,255,255,0.7)',
                        'typography_font_size' => [ 'unit' => 'px', 'size' => 14 ],
                    ]),
                    make_shortcode('ft_form', '[fluentform id="5"]'),
                ]),
            ]),

            // Bottom bar
            make_c('ft_bot_bar', [
                'content_width' => 'full',
                'border_border' => 'solid',
                'border_width' => [ 'unit' => 'px', 'top' => '1', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
                'border_color' => 'rgba(255,255,255,0.2)',
                'padding' => [ 'unit' => 'px', 'top' => '20', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ],
                'flex_direction' => 'row',
                'flex_wrap' => 'wrap',
                'flex_justify_content' => 'space-between',
                'flex_gap' => [ 'unit' => 'px', 'size' => 16 ],
            ], [
                make_t('ft_copyright', '<p>© 2026 Monterey Park Chinese Baptist Church 蒙迪園華人浸信會</p>', [
                    'text_color' => 'rgba(255,255,255,0.6)',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                ]),
                make_t('ft_disclaimer', '<p>Live links update automatically from YouTube. Nobody edits this weekly.</p>', [
                    'text_color' => 'rgba(255,255,255,0.6)',
                    'typography_font_size' => [ 'unit' => 'px', 'size' => 13 ],
                ]),
            ]),
        ]),
    ]),
];

// =============================================================
// COMING SOON PAGE (/coming-soon/)
// =============================================================
$coming_soon_content = [
    make_c('sec_coming_soon', [
        'content_width' => 'boxed',
        'boxed_width'   => [ 'unit' => 'px', 'size' => 720 ],
        'min_height'    => [ 'unit' => 'vh', 'size' => 60 ],
        'padding'       => [ 'unit' => 'px', 'top' => '100', 'right' => '20', 'bottom' => '100', 'left' => '20', 'isLinked' => false ],
        'flex_direction'=> 'column',
        'flex_align_items' => 'center',
        'flex_justify_content' => 'center',
        'flex_gap'      => [ 'unit' => 'px', 'size' => 20 ],
    ], [
        make_h('cs_h1', 'Coming Soon · 敬請期待', 'h1', [
            'align' => 'center',
            'title_color' => '#943759',
            '__globals__' => [ 'title_color' => 'globals/colors?id=primary' ],
            'typography_typography' => 'custom',
            'typography_font_family' => 'Geist',
            'typography_font_size' => [ 'unit' => 'px', 'size' => 48 ],
            'typography_font_weight' => '700',
        ]),
        make_t('cs_desc', '<p>This page is currently being prepared for our new website launch.<br>此頁面正在準備中，即將推出。</p>', [
            'align' => 'center',
            'text_color' => '#6B5560',
            'typography_font_size' => [ 'unit' => 'px', 'size' => 18 ],
        ]),
        make_b('cs_btn_home', 'Return to Homepage 返回首頁', '/', [
            'background_color' => '#943759',
            '__globals__' => [ 'background_color' => 'globals/colors?id=primary' ],
            'button_text_color' => '#FFFFFF',
            'padding' => [ 'unit' => 'px', 'top' => '14', 'right' => '28', 'bottom' => '14', 'left' => '28', 'isLinked' => false ],
            'typography_font_size' => [ 'unit' => 'px', 'size' => 16 ],
            'typography_font_weight' => '600',
        ]),
    ]),
];

// =============================================================
// ASSEMBLE FULL HOME PAGE
// =============================================================
$home_content = [
    $home_hero,
    $home_services,
    $home_announcements,
    $home_welcome,
    $home_connect,
    $home_children,
    $home_events,
    $home_leaders,
    $home_giving,
];

// Function to save JSON template
function export_json($filename, $title, $type, $content) {
    $dir = 'C:/Projects/mpcbc/wordpress-design-b/elementor';
    $data = [
        'version' => '0.4',
        'title' => $title,
        'type' => $type,
        'page_settings' => [],
        'content' => $content,
    ];
    file_put_contents( "$dir/$filename", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) );
    echo "Saved $filename to $dir\n";
}

// Export individual sections to JSON
export_json('home-hero.json', 'Home - Hero', 'section', [$home_hero]);
export_json('home-services.json', 'Home - Sunday Services', 'section', [$home_services]);
export_json('home-announcements.json', 'Home - Announcements', 'section', [$home_announcements]);
export_json('home-welcome.json', 'Home - Welcome', 'section', [$home_welcome]);
export_json('home-connect.json', 'Home - Connect & Prayer', 'section', [$home_connect]);
export_json('home-children.json', 'Home - Children & Families', 'section', [$home_children]);
export_json('home-events.json', 'Home - Events', 'section', [$home_events]);
export_json('home-leaders.json', 'Home - Pastors & Leaders', 'section', [$home_leaders]);
export_json('home-giving.json', 'Home - Giving', 'section', [$home_giving]);
export_json('home-full.json', 'Home', 'page', $home_content);
export_json('footer-template.json', 'MPCBC – Footer', 'container', $footer_content);
export_json('coming-soon-page.json', 'Coming soon', 'page', $coming_soon_content);

// =============================================================
// INSERT / UPDATE POSTS IN WORDPRESS
// =============================================================
function save_or_update_wp_post($title, $slug, $post_type, $content, $template_type = 'container', $page_template = 'elementor_header_footer') {
    if ($post_type === 'page') {
        $existing = get_page_by_path($slug);
    } else {
        $q = new WP_Query([
            'post_type' => 'elementor_library',
            'title' => $title,
            'posts_per_page' => 1,
            'post_status' => 'any',
        ]);
        $existing = $q->have_posts() ? $q->posts[0] : null;
    }

    $post_data = [
        'post_title' => $title,
        'post_status' => 'publish',
        'post_type' => $post_type,
    ];
    if ($slug) {
        $post_data['post_name'] = $slug;
    }

    if ($existing) {
        $post_data['ID'] = $existing->ID;
        $post_id = wp_update_post($post_data);
        echo "Updated $post_type '$title' (ID: $post_id)\n";
    } else {
        $post_id = wp_insert_post($post_data);
        echo "Created $post_type '$title' (ID: $post_id)\n";
    }

    update_post_meta($post_id, '_elementor_edit_mode', 'builder');
    update_post_meta($post_id, '_elementor_version', ELEMENTOR_VERSION);
    update_post_meta($post_id, '_elementor_data', wp_slash(json_encode($content, JSON_UNESCAPED_UNICODE)));

    if ($post_type === 'page') {
        update_post_meta($post_id, '_wp_page_template', $page_template);
        update_post_meta($post_id, '_elementor_template_type', 'wp-page');
    } else {
        update_post_meta($post_id, '_elementor_template_type', $template_type);
        wp_set_object_terms($post_id, $template_type, 'elementor_library_type');
    }

    try {
        if (class_exists('Elementor\Core\Files\CSS\Post')) {
            $css_file = Elementor\Core\Files\CSS\Post::create($post_id);
            $css_file->update();
        }
    } catch (Exception $e) {}

    return $post_id;
}

// 1. Footer template
$footer_id = save_or_update_wp_post('MPCBC – Footer', 'mpcbc-footer', 'elementor_library', $footer_content, 'container');

// 2. Coming soon page
$coming_soon_id = save_or_update_wp_post('Coming soon', 'coming-soon', 'page', $coming_soon_content);

// 3. Home page
$home_id = save_or_update_wp_post('Home', 'home', 'page', $home_content);

// Set Home as Front Page
update_option('show_on_front', 'page');
update_option('page_on_front', $home_id);
echo "Set Home (ID: $home_id) as front page!\n";

// Clear Elementor CSS cache
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Elementor cache cleared.\n";

<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

wp_set_current_user( 1 );

$map_file = 'C:/Projects/mpcbc/wordpress/scripts/media-map.json';
$map = json_decode( file_get_contents( $map_file ), true ) ?: [];

$eng_thumb = $map['live-thumb-english.png'] ?? ['id' => 91, 'url' => 'http://mpcbc.local/wp-content/uploads/2026/10/live-thumb-english.png'];
$zh_thumb  = $map['live-thumb-chinese.png'] ?? ['id' => 92, 'url' => 'http://mpcbc.local/wp-content/uploads/2026/10/live-thumb-chinese.png'];

$live_section = [
    'id'       => 'live_section_root',
    'elType'   => 'container',
    'isInner'  => false,
    'settings' => [
        '_element_id'           => 'live',
        'content_width'         => 'full',
        'background_background' => 'classic',
        'background_color'      => '#12110E',
        '__globals__'           => [
            'background_color' => 'globals/colors?id=secondary',
        ],
        'padding'               => [
            'unit'     => 'px',
            'top'      => '96',
            'right'    => '56',
            'bottom'   => '96',
            'left'     => '56',
            'isLinked' => false,
        ],
        'padding_tablet'        => [
            'unit'     => 'px',
            'top'      => '80',
            'right'    => '40',
            'bottom'   => '80',
            'left'     => '40',
            'isLinked' => false,
        ],
        'padding_mobile'        => [
            'unit'     => 'px',
            'top'      => '64',
            'right'    => '20',
            'bottom'   => '64',
            'left'     => '20',
            'isLinked' => false,
        ],
        'flex_direction'        => 'column',
        'flex_align_items'      => 'center',
    ],
    'elements' => [
        [
            'id'       => 'live_inner',
            'elType'   => 'container',
            'isInner'  => true,
            'settings' => [
                'content_width'   => 'boxed',
                'boxed_width'     => [
                    'unit' => 'px',
                    'size' => 1328,
                ],
                'width'           => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'flex_direction'  => 'column',
                'flex_gap'        => [
                    'unit' => 'px',
                    'size' => 48,
                ],
                'flex_gap_mobile' => [
                    'unit' => 'px',
                    'size' => 36,
                ],
            ],
            'elements' => [
                // Header group: "Watch live" + "線上直播"
                [
                    'id'       => 'live_header_group',
                    'elType'   => 'container',
                    'isInner'  => true,
                    'settings' => [
                        'content_width'    => 'full',
                        'flex_direction'   => 'column',
                        'flex_gap'         => [
                            'unit' => 'px',
                            'size' => 8,
                        ],
                        'flex_align_items' => 'flex-start',
                        'width'            => [
                            'unit' => '%',
                            'size' => 100,
                        ],
                    ],
                    'elements' => [
                        [
                            'id'         => 'live_header_title',
                            'elType'     => 'widget',
                            'widgetType' => 'heading',
                            'isInner'    => false,
                            'settings'   => [
                                'title'                        => 'Watch live',
                                'header_size'                  => 'h2',
                                'title_color'                  => '#ffffff',
                                'typography_typography'        => 'custom',
                                'typography_font_family'       => 'Bricolage Grotesque',
                                'typography_font_size'         => [
                                    'unit' => 'px',
                                    'size' => 44,
                                ],
                                'typography_font_size_tablet'  => [
                                    'unit' => 'px',
                                    'size' => 36,
                                ],
                                'typography_font_size_mobile'  => [
                                    'unit' => 'px',
                                    'size' => 30,
                                ],
                                'typography_font_weight'       => '700',
                                'typography_line_height'       => [
                                    'unit' => 'em',
                                    'size' => 1.15,
                                ],
                            ],
                            'elements'   => [],
                        ],
                        [
                            'id'         => 'live_header_zh',
                            'elType'     => 'widget',
                            'widgetType' => 'heading',
                            'isInner'    => false,
                            'settings'   => [
                                'title'                       => '線上直播',
                                'header_size'                 => 'p',
                                'title_color'                 => 'rgba(255, 255, 255, 0.7)',
                                'typography_typography'       => 'custom',
                                'typography_font_family'      => 'Noto Sans TC',
                                'typography_font_size'        => [
                                    'unit' => 'px',
                                    'size' => 20,
                                ],
                                'typography_font_size_mobile' => [
                                    'unit' => 'px',
                                    'size' => 18,
                                ],
                                'typography_font_weight'      => '500',
                            ],
                            'elements'   => [],
                        ],
                    ],
                ],
                // Two Columns: English (first) and Chinese (second)
                [
                    'id'       => 'live_columns_row',
                    'elType'   => 'container',
                    'isInner'  => true,
                    'settings' => [
                        'content_width'          => 'full',
                        'flex_direction'         => 'row',
                        'flex_direction_tablet'  => 'column',
                        'flex_direction_mobile'  => 'column',
                        'flex_gap'               => [
                            'unit' => 'px',
                            'size' => 48,
                        ],
                        'flex_gap_tablet'        => [
                            'unit' => 'px',
                            'size' => 40,
                        ],
                        'flex_gap_mobile'        => [
                            'unit' => 'px',
                            'size' => 36,
                        ],
                        'width'                  => [
                            'unit' => '%',
                            'size' => 100,
                        ],
                    ],
                    'elements' => [
                        // Column 1: English
                        [
                            'id'       => 'live_col_english',
                            'elType'   => 'container',
                            'isInner'  => true,
                            'settings' => [
                                'width'            => [
                                    'unit' => '%',
                                    'size' => 50,
                                ],
                                'width_tablet'     => [
                                    'unit' => '%',
                                    'size' => 100,
                                ],
                                'width_mobile'     => [
                                    'unit' => '%',
                                    'size' => 100,
                                ],
                                'flex_direction'   => 'column',
                                'flex_gap'         => [
                                    'unit' => 'px',
                                    'size' => 18,
                                ],
                                'flex_align_items' => 'flex-start',
                            ],
                            'elements' => [
                                // Video widget
                                [
                                    'id'         => 'live_video_english',
                                    'elType'     => 'widget',
                                    'widgetType' => 'video',
                                    'isInner'    => false,
                                    'settings'   => [
                                        'video_type'         => 'youtube',
                                        'youtube_url'        => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
                                        'show_image_overlay' => 'yes',
                                        'image_overlay'      => [
                                            'id'  => $eng_thumb['id'],
                                            'url' => $eng_thumb['url'],
                                        ],
                                        'image_overlay_size' => 'full',
                                        'show_play_icon'     => 'yes',
                                        'aspect_ratio'       => '169',
                                        '_border_radius'     => [
                                            'unit'     => 'px',
                                            'top'      => '16',
                                            'right'    => '16',
                                            'bottom'   => '16',
                                            'left'     => '16',
                                            'isLinked' => true,
                                        ],
                                    ],
                                    'elements'   => [],
                                ],
                                // Heading "English"
                                [
                                    'id'         => 'live_heading_en',
                                    'elType'     => 'widget',
                                    'widgetType' => 'heading',
                                    'isInner'    => false,
                                    'settings'   => [
                                        'title'                  => 'English',
                                        'header_size'            => 'h3',
                                        'title_color'            => '#ffffff',
                                        'typography_typography'  => 'custom',
                                        'typography_font_family' => 'Bricolage Grotesque',
                                        'typography_font_size'   => [
                                            'unit' => 'px',
                                            'size' => 24,
                                        ],
                                        'typography_font_weight' => '700',
                                    ],
                                    'elements'   => [],
                                ],
                                // Text line "Sundays 11:00 AM"
                                [
                                    'id'         => 'live_text_en',
                                    'elType'     => 'widget',
                                    'widgetType' => 'text-editor',
                                    'isInner'    => false,
                                    'settings'   => [
                                        'editor'                 => '<p>Sundays 11:00 AM</p>',
                                        'text_color'             => 'rgba(255, 255, 255, 0.75)',
                                        'typography_typography'  => 'custom',
                                        'typography_font_family' => 'Geist',
                                        'typography_font_size'   => [
                                            'unit' => 'px',
                                            'size' => 16,
                                        ],
                                        'typography_font_weight' => '500',
                                    ],
                                    'elements'   => [],
                                ],
                                // Button "Open on YouTube"
                                [
                                    'id'         => 'live_btn_en',
                                    'elType'     => 'widget',
                                    'widgetType' => 'button',
                                    'isInner'    => false,
                                    'settings'   => [
                                        'text'                         => 'Open on YouTube',
                                        'link'                         => [
                                            'url'         => 'https://www.youtube.com/@mpcbc',
                                            'is_external' => true,
                                        ],
                                        'button_text_color'            => '#ffffff',
                                        'background_color'             => 'transparent',
                                        'border_border'                => 'solid',
                                        'border_width'                 => [
                                            'unit'     => 'px',
                                            'top'      => '1.5',
                                            'right'    => '1.5',
                                            'bottom'   => '1.5',
                                            'left'     => '1.5',
                                            'isLinked' => true,
                                        ],
                                        'border_color'                 => 'rgba(255, 255, 255, 0.5)',
                                        'border_radius'                => [
                                            'unit'     => 'px',
                                            'top'      => '10',
                                            'right'    => '10',
                                            'bottom'   => '10',
                                            'left'     => '10',
                                            'isLinked' => true,
                                        ],
                                        'typography_typography'        => 'custom',
                                        'typography_font_family'       => 'Geist',
                                        'typography_font_size'         => [
                                            'unit' => 'px',
                                            'size' => 15,
                                        ],
                                        'typography_font_weight'       => '600',
                                        'padding'                      => [
                                            'unit'     => 'px',
                                            'top'      => '10',
                                            'right'    => '20',
                                            'bottom'   => '10',
                                            'left'     => '20',
                                            'isLinked' => false,
                                        ],
                                        'hover_color'                  => '#ffffff',
                                        'button_background_hover_color'=> 'rgba(255, 255, 255, 0.12)',
                                        'border_hover_color'           => '#ffffff',
                                    ],
                                    'elements'   => [],
                                ],
                            ],
                        ],
                        // Column 2: Chinese
                        [
                            'id'       => 'live_col_chinese',
                            'elType'   => 'container',
                            'isInner'  => true,
                            'settings' => [
                                'width'            => [
                                    'unit' => '%',
                                    'size' => 50,
                                ],
                                'width_tablet'     => [
                                    'unit' => '%',
                                    'size' => 100,
                                ],
                                'width_mobile'     => [
                                    'unit' => '%',
                                    'size' => 100,
                                ],
                                'flex_direction'   => 'column',
                                'flex_gap'         => [
                                    'unit' => 'px',
                                    'size' => 18,
                                ],
                                'flex_align_items' => 'flex-start',
                            ],
                            'elements' => [
                                // Video widget
                                [
                                    'id'         => 'live_video_chinese',
                                    'elType'     => 'widget',
                                    'widgetType' => 'video',
                                    'isInner'    => false,
                                    'settings'   => [
                                        'video_type'         => 'youtube',
                                        'youtube_url'        => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
                                        'show_image_overlay' => 'yes',
                                        'image_overlay'      => [
                                            'id'  => $zh_thumb['id'],
                                            'url' => $zh_thumb['url'],
                                        ],
                                        'image_overlay_size' => 'full',
                                        'show_play_icon'     => 'yes',
                                        'aspect_ratio'       => '169',
                                        '_border_radius'     => [
                                            'unit'     => 'px',
                                            'top'      => '16',
                                            'right'    => '16',
                                            'bottom'   => '16',
                                            'left'     => '16',
                                            'isLinked' => true,
                                        ],
                                    ],
                                    'elements'   => [],
                                ],
                                // Heading "中文 粵語 · 國語"
                                [
                                    'id'         => 'live_heading_zh',
                                    'elType'     => 'widget',
                                    'widgetType' => 'heading',
                                    'isInner'    => false,
                                    'settings'   => [
                                        'title'                  => '中文 粵語 · 國語',
                                        'header_size'            => 'h3',
                                        'title_color'            => '#ffffff',
                                        'typography_typography'  => 'custom',
                                        'typography_font_family' => 'Noto Sans TC',
                                        'typography_font_size'   => [
                                            'unit' => 'px',
                                            'size' => 24,
                                        ],
                                        'typography_font_weight' => '700',
                                    ],
                                    'elements'   => [],
                                ],
                                // Text line "粵語 9:30 AM · 國語 11:30 AM"
                                [
                                    'id'         => 'live_text_zh',
                                    'elType'     => 'widget',
                                    'widgetType' => 'text-editor',
                                    'isInner'    => false,
                                    'settings'   => [
                                        'editor'                 => '<p lang="zh-Hant">粵語 9:30 AM · 國語 11:30 AM</p>',
                                        'text_color'             => 'rgba(255, 255, 255, 0.75)',
                                        'typography_typography'  => 'custom',
                                        'typography_font_family' => 'Noto Sans TC',
                                        'typography_font_size'   => [
                                            'unit' => 'px',
                                            'size' => 16,
                                        ],
                                        'typography_font_weight' => '500',
                                    ],
                                    'elements'   => [],
                                ],
                                // Button "Open on YouTube"
                                [
                                    'id'         => 'live_btn_zh',
                                    'elType'     => 'widget',
                                    'widgetType' => 'button',
                                    'isInner'    => false,
                                    'settings'   => [
                                        'text'                         => 'Open on YouTube',
                                        'link'                         => [
                                            'url'         => 'https://www.youtube.com/@mpcbc',
                                            'is_external' => true,
                                        ],
                                        'button_text_color'            => '#ffffff',
                                        'background_color'             => 'transparent',
                                        'border_border'                => 'solid',
                                        'border_width'                 => [
                                            'unit'     => 'px',
                                            'top'      => '1.5',
                                            'right'    => '1.5',
                                            'bottom'   => '1.5',
                                            'left'     => '1.5',
                                            'isLinked' => true,
                                        ],
                                        'border_color'                 => 'rgba(255, 255, 255, 0.5)',
                                        'border_radius'                => [
                                            'unit'     => 'px',
                                            'top'      => '10',
                                            'right'    => '10',
                                            'bottom'   => '10',
                                            'left'     => '10',
                                            'isLinked' => true,
                                        ],
                                        'typography_typography'        => 'custom',
                                        'typography_font_family'       => 'Geist',
                                        'typography_font_size'         => [
                                            'unit' => 'px',
                                            'size' => 15,
                                        ],
                                        'typography_font_weight'       => '600',
                                        'padding'                      => [
                                            'unit'     => 'px',
                                            'top'      => '10',
                                            'right'    => '20',
                                            'bottom'   => '10',
                                            'left'     => '20',
                                            'isLinked' => false,
                                        ],
                                        'hover_color'                  => '#ffffff',
                                        'button_background_hover_color'=> 'rgba(255, 255, 255, 0.12)',
                                        'border_hover_color'           => '#ffffff',
                                    ],
                                    'elements'   => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];

// Load current Home data
$raw = get_post_meta( 21, '_elementor_data', true );
$data = json_decode( $raw, true );

// Update Hero Watch Live button link to #live
function update_hero_watch_link( &$elements ) {
    foreach ( $elements as &$el ) {
        if ( ( $el['widgetType'] ?? '' ) === 'button' && ( $el['settings']['text'] ?? '' ) === 'Watch live' ) {
            $el['settings']['link']['url'] = '#live';
            echo "Updated Hero Watch live button link to #live\n";
        }
        if ( ! empty( $el['elements'] ) ) {
            update_hero_watch_link( $el['elements'] );
        }
    }
}
update_hero_watch_link( $data );

// Update Times Card "Live now · Watch" pill link to #live
function update_times_pill_link( &$elements ) {
    foreach ( $elements as &$el ) {
        if ( ( $el['widgetType'] ?? '' ) === 'button' && ( $el['settings']['text'] ?? '' ) === 'Live now · Watch' ) {
            $el['settings']['link']['url'] = '#live';
            echo "Updated Times card Live now · Watch pill link to #live\n";
        }
        if ( ! empty( $el['elements'] ) ) {
            update_times_pill_link( $el['elements'] );
        }
    }
}
update_times_pill_link( $data );

// Find index of times_outer_section
$insert_idx = -1;
foreach ( $data as $idx => $sec ) {
    if ( ( $sec['id'] ?? '' ) === 'times_outer_section' ) {
        $insert_idx = $idx + 1;
        break;
    }
}

// Remove any existing live_section_root if already present
$filtered = [];
foreach ( $data as $sec ) {
    if ( ( $sec['id'] ?? '' ) !== 'live_section_root' && ( $sec['settings']['_element_id'] ?? '' ) !== 'live' ) {
        $filtered[] = $sec;
    }
}

// Find insert index in filtered array
$insert_idx = -1;
foreach ( $filtered as $idx => $sec ) {
    if ( ( $sec['id'] ?? '' ) === 'times_outer_section' ) {
        $insert_idx = $idx + 1;
        break;
    }
}

if ( $insert_idx === -1 ) {
    $insert_idx = 2; // fallback
}

array_splice( $filtered, $insert_idx, 0, [ $live_section ] );

echo "Inserted live_section_root at position $insert_idx\n";

// Save to DB
update_post_meta( 21, '_elementor_data', wp_slash( json_encode( $filtered, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) );
update_post_meta( 21, '_elementor_version', ELEMENTOR_VERSION );

// Clear CSS cache
\Elementor\Plugin::$instance->files_manager->clear_cache();

echo "Home post 21 updated and Elementor cache cleared!\n";

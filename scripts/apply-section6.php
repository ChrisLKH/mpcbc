<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';
require_once __DIR__ . '/visual-pass-helpers.php';

$raw = get_post_meta(21, '_elementor_data', true);
$data = json_decode($raw, true);

// Section 6 is index 6
$sec = &$data[6];

// 1. Style outer section: Paper ground, 120/88/64 padding
style_outer_section($sec, GV_PAPER);

// Inner container
$inner = &$sec['elements'][0];
ensure_class_id($inner);
set_variant_props($inner, 'desktop', [
    'max-width' => p_size(1328),
    'width' => p_size(100, '%'),
    'margin' => p_dimensions(0, 'auto', 0, 'auto'),
    'padding' => p_size(0),
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'gap' => p_size(64),
    'align-items' => p_string('flex-start')
]);
set_variant_props($inner, 'tablet', [
    'flex-direction' => p_string('column'),
    'gap' => p_size(48)
]);
set_variant_props($inner, 'mobile', [
    'flex-direction' => p_string('column'),
    'gap' => p_size(40)
]);

// Left column: Coming up (72a78a1e)
$colLeft = &$inner['elements'][0];
ensure_class_id($colLeft);
set_variant_props($colLeft, 'desktop', [
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(1),
            'flexShrink' => p_number(1),
            'flexBasis' => p_size(0, '%')
        ]
    ],
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(24),
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);

// Heading group: Coming up
$headLeft = &$colLeft['elements'][0]; // mpcbc-events_heading_group
ensure_class_id($headLeft);
set_variant_props($headLeft, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(6),
    'padding' => p_size(0)
]);
style_display_heading($headLeft['elements'][0], GV_INK);
set_variant_props($headLeft['elements'][0], 'desktop', ['font-size' => p_size(56)]);
set_variant_props($headLeft['elements'][0], 'tablet', ['font-size' => p_size(44)]);
set_variant_props($headLeft['elements'][0], 'mobile', ['font-size' => p_size(36)]);

style_chinese_subtitle($headLeft['elements'][1], GV_INK, false);
style_chinese_subtitle($headLeft['elements'][2], GV_INK, true);

// Events rows container
$eventsRows = &$colLeft['elements'][1]; // mpcbc-events_rows
ensure_class_id($eventsRows);
set_variant_props($eventsRows, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(16),
    'padding' => p_size(0),
    'width' => p_size(100, '%')
]);

foreach ($eventsRows['elements'] as &$row) {
    ensure_class_id($row);
    set_variant_props($row, 'desktop', [
        'display' => p_string('flex'),
        'flex-direction' => p_string('row'),
        'gap' => p_size(20),
        'align-items' => p_string('center'),
        'padding' => p_dimensions(0, 0, 16, 0),
        'border-style' => p_string('solid'),
        'border-width' => p_dimensions(0, 0, 1, 0),
        'border-color' => p_global_color(GV_LINE),
        'width' => p_size(100, '%')
    ]);

    // Chip
    $chip = &$row['elements'][0];
    reset_box($chip);
    set_variant_props($chip, 'desktop', [
        'width' => p_size(60),
        'height' => p_size(60),
        'flex' => [
            '$$type' => 'flex',
            'value' => [
                'flexGrow' => p_number(0),
                'flexShrink' => p_number(0),
                'flexBasis' => p_size(60)
            ]
        ],
        'background' => p_bg_color(GV_WHITE),
        'border-radius' => p_radius(0),
        'border-style' => p_string('solid'),
        'border-width' => p_size(1),
        'border-color' => p_global_color(GV_LINE),
        'display' => p_string('flex'),
        'flex-direction' => p_string('column'),
        'align-items' => p_string('center'),
        'justify-content' => p_string('center'),
        'gap' => p_size(2),
        'padding' => p_size(4)
    ]);
    // Chip month
    ensure_class_id($chip['elements'][0]);
    set_variant_props($chip['elements'][0], 'desktop', [
        'font-family' => p_global_font(GV_FONT_TEXT),
        'font-size' => p_size(12),
        'font-weight' => p_string('600'),
        'color' => p_global_color(GV_MUTED),
        'line-height' => p_size(1.0, 'em'),
        'margin' => p_size(0)
    ]);
    // Chip day
    ensure_class_id($chip['elements'][1]);
    set_variant_props($chip['elements'][1], 'desktop', [
        'font-family' => p_global_font(GV_FONT_DISPLAY),
        'font-size' => p_size(20),
        'font-weight' => p_string('700'),
        'color' => p_global_color(GV_INK),
        'line-height' => p_size(1.0, 'em'),
        'margin' => p_size(0)
    ]);

    // Text
    $text = &$row['elements'][1];
    ensure_class_id($text);
    set_variant_props($text, 'desktop', [
        'display' => p_string('flex'),
        'flex-direction' => p_string('column'),
        'gap' => p_size(4),
        'flex' => [
            '$$type' => 'flex',
            'value' => [
                'flexGrow' => p_number(1),
                'flexShrink' => p_number(1),
                'flexBasis' => p_size(0, '%')
            ]
        ],
        'padding' => p_size(0)
    ]);
    // Title
    ensure_class_id($text['elements'][0]);
    set_variant_props($text['elements'][0], 'desktop', [
        'font-family' => p_global_font(GV_FONT_TEXT),
        'font-size' => p_size(16),
        'font-weight' => p_string('600'),
        'color' => p_global_color(GV_INK),
        'margin' => p_size(0)
    ]);
    // Meta
    style_small_meta($text['elements'][1], GV_MUTED);
}
unset($row);

// Events link
ensure_class_id($colLeft['elements'][2]);
set_variant_props($colLeft['elements'][2], 'desktop', [
    'font-family' => p_global_font(GV_FONT_TEXT),
    'font-size' => p_size(16),
    'font-weight' => p_string('600'),
    'color' => p_global_color(GV_HARBOR),
    'text-decoration' => p_string('underline'),
    'margin' => p_size(0)
]);

// Right column: Recent sermons (31cd63ab)
$colRight = &$inner['elements'][1];
ensure_class_id($colRight);
set_variant_props($colRight, 'desktop', [
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(1),
            'flexShrink' => p_number(1),
            'flexBasis' => p_size(0, '%')
        ]
    ],
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(24),
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);

// Heading group: Recent sermons
$headRight = &$colRight['elements'][0]; // mpcbc-sermons_heading_group
ensure_class_id($headRight);
set_variant_props($headRight, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(6),
    'padding' => p_size(0)
]);
style_display_heading($headRight['elements'][0], GV_INK);
set_variant_props($headRight['elements'][0], 'desktop', ['font-size' => p_size(56)]);
set_variant_props($headRight['elements'][0], 'tablet', ['font-size' => p_size(44)]);
set_variant_props($headRight['elements'][0], 'mobile', ['font-size' => p_size(36)]);

style_chinese_subtitle($headRight['elements'][1], GV_INK, false);
style_chinese_subtitle($headRight['elements'][2], GV_INK, true);

// Sermon rows container
$sermonRows = &$colRight['elements'][1]; // mpcbc-sermon_rows
ensure_class_id($sermonRows);
set_variant_props($sermonRows, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(16),
    'padding' => p_size(0),
    'width' => p_size(100, '%')
]);

foreach ($sermonRows['elements'] as &$row) {
    ensure_class_id($row);
    set_variant_props($row, 'desktop', [
        'display' => p_string('flex'),
        'flex-direction' => p_string('row'),
        'gap' => p_size(20),
        'align-items' => p_string('center'),
        'padding' => p_dimensions(0, 0, 16, 0),
        'border-style' => p_string('solid'),
        'border-width' => p_dimensions(0, 0, 1, 0),
        'border-color' => p_global_color(GV_LINE),
        'width' => p_size(100, '%')
    ]);

    // Thumbnail
    $thumb = &$row['elements'][0];
    reset_box($thumb);
    set_variant_props($thumb, 'desktop', [
        'width' => p_size(120),
        'flex' => [
            '$$type' => 'flex',
            'value' => [
                'flexGrow' => p_number(0),
                'flexShrink' => p_number(0),
                'flexBasis' => p_size(120)
            ]
        ],
        'aspect-ratio' => p_string('16 / 9'),
        'object-fit' => p_string('cover'),
        'border-radius' => p_radius(0),
        'display' => p_string('block')
    ]);

    // Text
    $text = &$row['elements'][1];
    ensure_class_id($text);
    set_variant_props($text, 'desktop', [
        'display' => p_string('flex'),
        'flex-direction' => p_string('column'),
        'gap' => p_size(4),
        'flex' => [
            '$$type' => 'flex',
            'value' => [
                'flexGrow' => p_number(1),
                'flexShrink' => p_number(1),
                'flexBasis' => p_size(0, '%')
            ]
        ],
        'padding' => p_size(0)
    ]);

    // Title item (can be paragraph or flexbox with brackets)
    $titleItem = &$text['elements'][0];
    if (($titleItem['widgetType'] ?? $titleItem['elType']) === 'e-paragraph') {
        ensure_class_id($titleItem);
        set_variant_props($titleItem, 'desktop', [
            'font-family' => p_global_font(GV_FONT_TEXT),
            'font-size' => p_size(16),
            'font-weight' => p_string('600'),
            'color' => p_global_color(GV_INK),
            'margin' => p_size(0)
        ]);
    } elseif (($titleItem['widgetType'] ?? $titleItem['elType']) === 'e-flexbox') {
        ensure_class_id($titleItem);
        set_variant_props($titleItem, 'desktop', [
            'display' => p_string('inline-flex'),
            'flex-direction' => p_string('row'),
            'gap' => p_size(2),
            'padding' => p_size(0)
        ]);
        foreach ($titleItem['elements'] as &$subTxt) {
            ensure_class_id($subTxt);
            set_variant_props($subTxt, 'desktop', [
                'font-family' => p_global_font(GV_FONT_TEXT),
                'font-size' => p_size(16),
                'font-weight' => p_string('600'),
                'color' => p_global_color(GV_INK),
                'margin' => p_size(0)
            ]);
        }
        unset($subTxt);
    }

    // Meta
    style_small_meta($text['elements'][1], GV_MUTED);
}
unset($row);

// Sermons link
ensure_class_id($colRight['elements'][2]);
set_variant_props($colRight['elements'][2], 'desktop', [
    'font-family' => p_global_font(GV_FONT_TEXT),
    'font-size' => p_size(16),
    'font-weight' => p_string('600'),
    'color' => p_global_color(GV_HARBOR),
    'text-decoration' => p_string('underline'),
    'margin' => p_size(0)
]);

// Save to post 21
$admins = get_users(['role' => 'administrator', 'number' => 1]);
wp_set_current_user($admins[0]->ID);
$doc = Elementor\Plugin::$instance->documents->get(21);
$settings = get_post_meta(21, '_elementor_page_settings', true) ?: [];
if (!$doc->save(['elements' => $data, 'settings' => $settings])) {
    throw new RuntimeException("Save to post 21 failed");
}
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Section 6 applied and saved successfully.\n";

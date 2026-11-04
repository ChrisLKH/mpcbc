<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';
require_once __DIR__ . '/visual-pass-helpers.php';

$raw = get_post_meta(21, '_elementor_data', true);
$data = json_decode($raw, true);

// Section 2 is index 2
$sec = &$data[2];

// 1. Style outer section: Mist ground, 120/88/64 padding
style_outer_section($sec, GV_MIST);

// Inner container
$inner = &$sec['elements'][0];
ensure_class_id($inner);
set_variant_props($inner, 'desktop', [
    'max-width' => p_size(1328),
    'width' => p_size(100, '%'),
    'margin' => p_dimensions(0, 'auto', 0, 'auto'),
    'padding' => p_size(0),
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(48)
]);
set_variant_props($inner, 'tablet', [
    'gap' => p_size(40)
]);
set_variant_props($inner, 'mobile', [
    'gap' => p_size(32)
]);

// 2. Head row: intro under heading
$headRow = &$inner['elements'][0]; // mpcbc-news_head
ensure_class_id($headRow);
set_variant_props($headRow, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'align-items' => p_string('flex-start'),
    'gap' => p_size(12),
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);

$headGroup = &$headRow['elements'][0]; // mpcbc-news_heading_group
ensure_class_id($headGroup);
set_variant_props($headGroup, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'align-items' => p_string('flex-start'),
    'gap' => p_size(6),
    'padding' => p_size(0)
]);

// H2 "Newsletter"
$h2 = &$headGroup['elements'][0];
style_display_heading($h2, GV_INK);

// Chinese subtitle TC & SC
$subTC = &$headGroup['elements'][1];
style_chinese_subtitle($subTC, GV_INK, false);
$subSC = &$headGroup['elements'][2];
style_chinese_subtitle($subSC, GV_INK, true);

// Intro line
$intro = &$headRow['elements'][1]; // mpcbc-news_lede
style_body($intro, GV_INK);

// 3. Grid: Left card (transparent, text + thumb) & Right card (White panel, no border)
$grid = &$inner['elements'][1]; // mpcbc-news_grid
ensure_class_id($grid);
set_variant_props($grid, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'align-items' => p_string('stretch'),
    'gap' => p_size(36),
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);
set_variant_props($grid, 'tablet', [
    'flex-direction' => p_string('column'),
    'gap' => p_size(32)
]);
set_variant_props($grid, 'mobile', [
    'flex-direction' => p_string('column'),
    'gap' => p_size(28)
]);

// Left card: remove outer white box
$leftCard = &$grid['elements'][0]; // mpcbc-news_card_left
reset_box($leftCard);
set_variant_props($leftCard, 'desktop', [
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(1.35),
            'flexShrink' => p_number(1),
            'flexBasis' => p_size(0, '%')
        ]
    ],
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'gap' => p_size(28),
    'align-items' => p_string('flex-start'),
    'background' => p_bg_transparent(),
    'border-style' => p_string('none'),
    'border-width' => p_size(0),
    'padding' => p_size(0)
]);
set_variant_props($leftCard, 'tablet', [
    'flex-direction' => p_string('row'),
    'gap' => p_size(24)
]);
set_variant_props($leftCard, 'mobile', [
    'flex-direction' => p_string('column-reverse'),
    'gap' => p_size(20)
]);

// Inside left card: text column and photo
$leftTextCol = &$leftCard['elements'][0]; // mpcbc-news_left_text_col
reset_box($leftTextCol);
set_variant_props($leftTextCol, 'desktop', [
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
    'gap' => p_size(12),
    'padding' => p_size(0),
    'background' => p_bg_transparent(),
    'border-style' => p_string('none')
]);

// Reorder left text col children:
// Currently: [0] kicker ("Newsletter · July 2026"), [1] title ("Faith, testimony and mission"), [2] desc en, [3] desc zh-hant, [4] desc zh-hans, [5] btns
// Rule 5: "No labels above headings. Lines like 'Pastor · Cantonese' and 'Newsletter · July 2026' move to below the title as a Small muted meta line."
// So new order: [title, kicker, desc en, desc zh-hant, desc zh-hans, btns]
$kicker = $leftTextCol['elements'][0];
$title = $leftTextCol['elements'][1];
$descEn = $leftTextCol['elements'][2];
$descZH_hant = $leftTextCol['elements'][3];
$descZH_hans = $leftTextCol['elements'][4];
$btns = $leftTextCol['elements'][5];

// Style title: Title 36 / 30 / 28px
ensure_class_id($title);
set_variant_props($title, 'desktop', [
    'font-family' => p_global_font(GV_FONT_DISPLAY),
    'font-size' => p_size(36),
    'font-weight' => p_string('700'),
    'line-height' => p_size(1.15, 'em'),
    'color' => p_global_color(GV_INK),
    'margin' => p_size(0)
]);
set_variant_props($title, 'tablet', [
    'font-size' => p_size(30),
    'line-height' => p_size(1.15, 'em')
]);
set_variant_props($title, 'mobile', [
    'font-size' => p_size(28),
    'line-height' => p_size(1.15, 'em')
]);

// Style kicker: Small 14px, muted grey
style_small_meta($kicker, GV_MUTED);

// Style descriptions: Body 16px, Ink
style_body($descEn, GV_INK);
style_body($descZH_hant, GV_INK, GV_FONT_CHINESE);
style_body($descZH_hans, GV_INK, GV_FONT_CHINESE_SC);

// Style buttons
ensure_class_id($btns);
set_variant_props($btns, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'gap' => p_size(12),
    'align-items' => p_string('center'),
    'flex-wrap' => p_string('wrap'),
    'padding' => p_size(0),
    'margin' => p_dimensions(8, 0, 0, 0)
]);
style_button($btns['elements'][0], true, GV_HARBOR, GV_WHITE);
style_button($btns['elements'][1], false, null, GV_INK);

$leftTextCol['elements'] = [$title, $kicker, $descEn, $descZH_hant, $descZH_hans, $btns];

// Left photo
$leftPhoto = &$leftCard['elements'][1]; // mpcbc-news_left_photo
reset_box($leftPhoto);
set_variant_props($leftPhoto, 'desktop', [
    'width' => p_size(240),
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(0),
            'flexShrink' => p_number(0),
            'flexBasis' => p_size(240)
        ]
    ],
    'border-radius' => p_radius(0),
    'object-fit' => p_string('cover'),
    'display' => p_string('block')
]);
set_variant_props($leftPhoto, 'tablet', [
    'width' => p_size(220),
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(0),
            'flexShrink' => p_number(0),
            'flexBasis' => p_size(220)
        ]
    ]
]);
set_variant_props($leftPhoto, 'mobile', [
    'width' => p_size(100, '%'),
    'max-width' => p_size(260),
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(0),
            'flexShrink' => p_number(0),
            'flexBasis' => p_size('auto', 'custom')
        ]
    ]
]);

// Right card: White panel (tone step, no border), 0px radius, no shadow
$rightCard = &$grid['elements'][1]; // mpcbc-news_card_right
reset_box($rightCard);
set_variant_props($rightCard, 'desktop', [
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(1),
            'flexShrink' => p_number(1),
            'flexBasis' => p_size(0, '%')
        ]
    ],
    'background' => p_bg_color(GV_WHITE),
    'border-style' => p_string('none'),
    'border-width' => p_size(0),
    'border-radius' => p_radius(0),
    'padding' => p_dimensions(32, 32, 32, 32),
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(20)
]);
set_variant_props($rightCard, 'tablet', [
    'padding' => p_dimensions(28, 28, 28, 28)
]);
set_variant_props($rightCard, 'mobile', [
    'padding' => p_dimensions(24, 20, 24, 20)
]);

// Right card title: H3 "Explore more" + Chinese subtitles
style_subtitle($rightCard['elements'][0], GV_INK, GV_FONT_DISPLAY, '700');
style_chinese_subtitle($rightCard['elements'][1], GV_INK, false);
style_chinese_subtitle($rightCard['elements'][2], GV_INK, true);

// Side rows
$sideRows = &$rightCard['elements'][3]; // mpcbc-news_side_rows
ensure_class_id($sideRows);
set_variant_props($sideRows, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(16),
    'padding' => p_size(0)
]);

foreach ($sideRows['elements'] as &$row) {
    ensure_class_id($row);
    set_variant_props($row, 'desktop', [
        'display' => p_string('flex'),
        'flex-direction' => p_string('column'),
        'gap' => p_size(4),
        'padding' => p_dimensions(0, 0, 16, 0),
        'border-style' => p_string('solid'),
        'border-width' => p_dimensions(0, 0, 1, 0),
        'border-color' => p_global_color(GV_LINE)
    ]);
    // Title in row
    ensure_class_id($row['elements'][0]);
    set_variant_props($row['elements'][0], 'desktop', [
        'font-family' => p_global_font(GV_FONT_TEXT),
        'font-size' => p_size(16),
        'font-weight' => p_string('600'),
        'color' => p_global_color(GV_INK),
        'margin' => p_size(0)
    ]);
    // Detail in row: Ink body
    style_body($row['elements'][1], GV_INK);
}
unset($row);

// Form embed block inside right card
$formBlock = &$rightCard['elements'][4]; // mpcbc-embed-news_side_form
ensure_class_id($formBlock);
set_variant_props($formBlock, 'desktop', [
    'padding' => p_size(0),
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
echo "Section 2 applied and saved successfully.\n";

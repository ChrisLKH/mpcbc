<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';
require_once __DIR__ . '/visual-pass-helpers.php';

$raw = get_post_meta(21, '_elementor_data', true);
$data = json_decode($raw, true);

// Section 8 is index 8
$sec = &$data[8];

// 1. Style outer section: Harbor ground, full width, 120/88/64 padding
style_outer_section($sec, GV_HARBOR);

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
    'gap' => p_size(0)
]);

// Strip inner container (mpcbc-give_strip_inner, 2cfdd1d0)
$strip = &$inner['elements'][0];
reset_box($strip);
set_variant_props($strip, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'gap' => p_size(48),
    'align-items' => p_string('center'),
    'width' => p_size(100, '%'),
    'padding' => p_size(0),
    'background' => p_bg_transparent(),
    'border-style' => p_string('none'),
    'border-width' => p_size(0),
    'border-radius' => p_radius(0)
]);
set_variant_props($strip, 'tablet', [
    'flex-direction' => p_string('column'),
    'align-items' => p_string('flex-start'),
    'gap' => p_size(36)
]);
set_variant_props($strip, 'mobile', [
    'flex-direction' => p_string('column'),
    'align-items' => p_string('flex-start'),
    'gap' => p_size(28)
]);

// Photo: left at full band height, square
$photo = &$strip['elements'][0]; // mpcbc-give_photo (22427aa4)
reset_box($photo);
set_variant_props($photo, 'desktop', [
    'width' => p_size(300),
    'height' => p_size(300),
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(0),
            'flexShrink' => p_number(0),
            'flexBasis' => p_size(300)
        ]
    ],
    'aspect-ratio' => p_string('1 / 1'),
    'object-fit' => p_string('cover'),
    'border-radius' => p_radius(0),
    'border-style' => p_string('none'),
    'display' => p_string('block')
]);
set_variant_props($photo, 'tablet', [
    'width' => p_size(240),
    'height' => p_size(240),
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(0),
            'flexShrink' => p_number(0),
            'flexBasis' => p_size(240)
        ]
    ],
    'aspect-ratio' => p_string('1 / 1')
]);
set_variant_props($photo, 'mobile', [
    'width' => p_size(100, '%'),
    'max-width' => p_size(260),
    'height' => p_size('', 'auto'),
    'aspect-ratio' => p_string('1 / 1')
]);

// Text col: mpcbc-give_text_col (55d88c3f)
$textCol = &$strip['elements'][1];
reset_box($textCol);
set_variant_props($textCol, 'desktop', [
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
    'gap' => p_size(10),
    'padding' => p_size(0),
    'background' => p_bg_transparent(),
    'border-style' => p_string('none')
]);

// Heading H2 "Give": Display size, White
$h2 = &$textCol['elements'][0]; // mpcbc-give_heading
style_display_heading($h2, GV_WHITE);
set_variant_props($h2, 'desktop', ['font-size' => p_size(56)]);
set_variant_props($h2, 'tablet', ['font-size' => p_size(44)]);
set_variant_props($h2, 'mobile', ['font-size' => p_size(36)]);

// Chinese subtitles in White
$subTC = &$textCol['elements'][1];
style_chinese_subtitle($subTC, GV_WHITE, false);
$subSC = &$textCol['elements'][2];
style_chinese_subtitle($subSC, GV_WHITE, true);

// Verse lines in White
$verseEn = &$textCol['elements'][3];
style_body($verseEn, GV_WHITE);
$verseTC = &$textCol['elements'][4];
style_body($verseTC, GV_WHITE, GV_FONT_CHINESE);
$verseSC = &$textCol['elements'][5];
style_body($verseSC, GV_WHITE, GV_FONT_CHINESE_SC);

// Actions col: mpcbc-give_actions_col (760b7f01)
$actionsCol = &$strip['elements'][2];
ensure_class_id($actionsCol);
set_variant_props($actionsCol, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(16),
    'align-items' => p_string('flex-start'),
    'padding' => p_size(0)
]);

// Give button: Persimmon with #2B1404 text, 999px radius
$btnOnline = &$actionsCol['elements'][0]; // mpcbc-give_btn_online (flexbox group)
style_button($btnOnline, true, GV_PERSIMMON, GV_ON_PERSIMMON);
foreach ($btnOnline['elements'] as &$pItem) {
    ensure_class_id($pItem);
    set_variant_props($pItem, 'desktop', [
        'font-family' => p_global_font(GV_FONT_TEXT),
        'font-size' => p_size(16),
        'font-weight' => p_string('600'),
        'color' => p_global_color(GV_ON_PERSIMMON),
        'margin' => p_size(0)
    ]);
}
unset($pItem);

// Ways to give text link
$linkOther = &$actionsCol['elements'][1]; // mpcbc-give_link_other (flexbox group)
ensure_class_id($linkOther);
set_variant_props($linkOther, 'desktop', [
    'display' => p_string('inline-flex'),
    'flex-direction' => p_string('row'),
    'gap' => p_size(4),
    'align-items' => p_string('center'),
    'padding' => p_size(0),
    'text-decoration' => p_string('underline'),
    'color' => p_global_color(GV_WHITE)
]);
foreach ($linkOther['elements'] as &$pItem) {
    ensure_class_id($pItem);
    set_variant_props($pItem, 'desktop', [
        'font-family' => p_global_font(GV_FONT_TEXT),
        'font-size' => p_size(15),
        'font-weight' => p_string('500'),
        'color' => p_global_color(GV_WHITE),
        'text-decoration' => p_string('underline'),
        'margin' => p_size(0)
    ]);
}
unset($pItem);

// Save to post 21
$admins = get_users(['role' => 'administrator', 'number' => 1]);
wp_set_current_user($admins[0]->ID);
$doc = Elementor\Plugin::$instance->documents->get(21);
$settings = get_post_meta(21, '_elementor_page_settings', true) ?: [];
if (!$doc->save(['elements' => $data, 'settings' => $settings])) {
    throw new RuntimeException("Save to post 21 failed");
}
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Section 8 applied and saved successfully.\n";

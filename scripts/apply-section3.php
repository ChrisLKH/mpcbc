<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';
require_once __DIR__ . '/visual-pass-helpers.php';

$raw = get_post_meta(21, '_elementor_data', true);
$data = json_decode($raw, true);

// Section 3 is index 3
$sec = &$data[3];

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
$headRow = &$inner['elements'][0]; // mpcbc-pastors_head
ensure_class_id($headRow);
set_variant_props($headRow, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'align-items' => p_string('flex-start'),
    'gap' => p_size(12),
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);

$headGroup = &$headRow['elements'][0]; // mpcbc-pastors_head_group
ensure_class_id($headGroup);
set_variant_props($headGroup, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'align-items' => p_string('flex-start'),
    'gap' => p_size(6),
    'padding' => p_size(0)
]);

// H2 "Meet our pastors"
$h2 = &$headGroup['elements'][0];
style_display_heading($h2, GV_INK);

// Chinese subtitle TC & SC
$subTC = &$headGroup['elements'][1];
style_chinese_subtitle($subTC, GV_INK, false);
$subSC = &$headGroup['elements'][2];
style_chinese_subtitle($subSC, GV_INK, true);

// Intro line
$intro = &$headRow['elements'][1]; // mpcbc-pastors_lede
style_body($intro, GV_INK);

// 3. Cards row: 3 large square portraits, no card box, no border
$cardsRow = &$inner['elements'][1]; // mpcbc-pastors_cards_row
ensure_class_id($cardsRow);
set_variant_props($cardsRow, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'gap' => p_size(36),
    'align-items' => p_string('flex-start'),
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);
set_variant_props($cardsRow, 'tablet', [
    'flex-direction' => p_string('row'),
    'gap' => p_size(24)
]);
set_variant_props($cardsRow, 'mobile', [
    'flex-direction' => p_string('column'),
    'gap' => p_size(36)
]);

for ($i = 0; $i < 3; $i++) {
    $card = &$cardsRow['elements'][$i];
    reset_box($card);
    set_variant_props($card, 'desktop', [
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
        'gap' => p_size(16),
        'background' => p_bg_transparent(),
        'border-style' => p_string('none'),
        'border-width' => p_size(0),
        'padding' => p_size(0)
    ]);
    set_variant_props($card, 'mobile', [
        'width' => p_size(100, '%'),
        'flex' => [
            '$$type' => 'flex',
            'value' => [
                'flexGrow' => p_number(0),
                'flexShrink' => p_number(0),
                'flexBasis' => p_size('auto', 'custom')
            ]
        ]
    ]);

    // Image wrap
    $imgWrap = &$card['elements'][0];
    reset_box($imgWrap);
    set_variant_props($imgWrap, 'desktop', [
        'width' => p_size(100, '%'),
        'aspect-ratio' => p_string('1 / 1'),
        'overflow' => p_string('hidden'),
        'padding' => p_size(0),
        'margin' => p_size(0),
        'background' => p_bg_transparent(),
        'border-style' => p_string('none'),
        'border-radius' => p_radius(0)
    ]);

    // Image
    $img = &$imgWrap['elements'][0];
    reset_box($img);
    set_variant_props($img, 'desktop', [
        'width' => p_size(100, '%'),
        'height' => p_size(100, '%'),
        'aspect-ratio' => p_string('1 / 1'),
        'object-fit' => p_string('cover'),
        'border-radius' => p_radius(0),
        'display' => p_string('block')
    ]);

    // Info container
    $info = &$card['elements'][1];
    reset_box($info);
    set_variant_props($info, 'desktop', [
        'display' => p_string('flex'),
        'flex-direction' => p_string('column'),
        'gap' => p_size(8),
        'padding' => p_size(0),
        'background' => p_bg_transparent(),
        'border-style' => p_string('none')
    ]);

    // Reorder info: name, Chinese name, meta, bio
    // Currently: [0] kicker, [1] name, [2] zh-hant name, [3] zh-hans name, [4] bio
    $kicker = $info['elements'][0];
    $name = $info['elements'][1];
    $nameTC = $info['elements'][2];
    $nameSC = $info['elements'][3];
    $bio = $info['elements'][4];

    // Style name: Subtitle 22 / 20 / 20px
    style_subtitle($name, GV_INK, GV_FONT_DISPLAY, '700');

    // Style Chinese names: Subtitle 22 / 20 / 20px
    style_subtitle($nameTC, GV_INK, GV_FONT_CHINESE, '600');
    style_subtitle($nameSC, GV_INK, GV_FONT_CHINESE_SC, '600');

    // Style kicker (meta line): Small 14px, Muted grey
    style_small_meta($kicker, GV_MUTED);

    // Style bio: Body 16px, Ink
    style_body($bio, GV_INK);

    $info['elements'] = [$name, $nameTC, $nameSC, $kicker, $bio];
}
unset($card);

// Mission statements (TC & SC)
if (isset($inner['elements'][2])) {
    style_body($inner['elements'][2], GV_INK, GV_FONT_CHINESE);
}
if (isset($inner['elements'][3])) {
    style_body($inner['elements'][3], GV_INK, GV_FONT_CHINESE_SC);
}

// About link
if (isset($inner['elements'][4])) {
    $aboutLink = &$inner['elements'][4];
    ensure_class_id($aboutLink);
    set_variant_props($aboutLink, 'desktop', [
        'display' => p_string('inline-flex'),
        'flex-direction' => p_string('row'),
        'gap' => p_size(6),
        'align-items' => p_string('center'),
        'padding' => p_size(0)
    ]);
    foreach ($aboutLink['elements'] as &$pItem) {
        ensure_class_id($pItem);
        set_variant_props($pItem, 'desktop', [
            'font-family' => p_global_font(GV_FONT_TEXT),
            'font-size' => p_size(16),
            'font-weight' => p_string('600'),
            'color' => p_global_color(GV_HARBOR),
            'margin' => p_size(0)
        ]);
    }
    unset($pItem);
}

// Save to post 21
$admins = get_users(['role' => 'administrator', 'number' => 1]);
wp_set_current_user($admins[0]->ID);
$doc = Elementor\Plugin::$instance->documents->get(21);
$settings = get_post_meta(21, '_elementor_page_settings', true) ?: [];
if (!$doc->save(['elements' => $data, 'settings' => $settings])) {
    throw new RuntimeException("Save to post 21 failed");
}
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Section 3 applied and saved successfully.\n";

<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';
require_once __DIR__ . '/visual-pass-helpers.php';

$raw = get_post_meta(21, '_elementor_data', true);
$data = json_decode($raw, true);

// Section 5 is index 5
$sec = &$data[5];

// 1. Style outer section: Soft persimmon ground, full width, 120/88/64 padding
style_outer_section($sec, GV_SOFT_PERSIMMON);

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

// Inside inner container: mpcbc-kids_card_inner (6009f9e0)
// The brief states: "Soft persimmon, full width (not a rounded card inside the page)"
$cardInner = &$inner['elements'][0]; // mpcbc-kids_card_inner
reset_box($cardInner);
set_variant_props($cardInner, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'gap' => p_size(48),
    'align-items' => p_string('stretch'),
    'width' => p_size(100, '%'),
    'padding' => p_size(0),
    'background' => p_bg_transparent(),
    'border-style' => p_string('none'),
    'border-width' => p_size(0),
    'border-radius' => p_radius(0)
]);
set_variant_props($cardInner, 'tablet', [
    'flex-direction' => p_string('column'),
    'gap' => p_size(36)
]);
set_variant_props($cardInner, 'mobile', [
    'flex-direction' => p_string('column'),
    'gap' => p_size(28)
]);

// Left content column: mpcbc-kids_col_content (77cb3fe5)
$colContent = &$cardInner['elements'][0];
reset_box($colContent);
set_variant_props($colContent, 'desktop', [
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(1.15),
            'flexShrink' => p_number(1),
            'flexBasis' => p_size(0, '%')
        ]
    ],
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(24),
    'padding' => p_size(0),
    'background' => p_bg_transparent(),
    'border-style' => p_string('none')
]);
set_variant_props($colContent, 'tablet', [
    'width' => p_size(100, '%')
]);
set_variant_props($colContent, 'mobile', [
    'width' => p_size(100, '%'),
    'gap' => p_size(20)
]);

// Heading: Display 72 / 56 / 44px, Ink
$h2 = &$colContent['elements'][0]; // mpcbc-kids_heading
style_display_heading($h2, GV_INK);

// Lede: Body 16px, Ink
$lede = &$colContent['elements'][1]; // mpcbc-kids_lede
style_body($lede, GV_INK);

// Tiles row (51eea4d1): 3 white tiles, corners 0px radius
$tilesRow = &$colContent['elements'][2]; // mpcbc-kids_tiles_row
ensure_class_id($tilesRow);
set_variant_props($tilesRow, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'gap' => p_size(16),
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);
set_variant_props($tilesRow, 'mobile', [
    'flex-direction' => p_string('column'),
    'gap' => p_size(12)
]);

foreach ($tilesRow['elements'] as &$tile) {
    reset_box($tile);
    set_variant_props($tile, 'desktop', [
        'flex' => [
            '$$type' => 'flex',
            'value' => [
                'flexGrow' => p_number(1),
                'flexShrink' => p_number(1),
                'flexBasis' => p_size(0, '%')
            ]
        ],
        'background' => p_bg_color(GV_WHITE),
        'border-radius' => p_radius(0),
        'border-style' => p_string('none'),
        'padding' => p_dimensions(16, 16, 16, 16),
        'display' => p_string('flex'),
        'flex-direction' => p_string('column'),
        'gap' => p_size(4)
    ]);
    // Tile name
    ensure_class_id($tile['elements'][0]);
    set_variant_props($tile['elements'][0], 'desktop', [
        'font-family' => p_global_font(GV_FONT_DISPLAY),
        'font-size' => p_size(18),
        'font-weight' => p_string('700'),
        'color' => p_global_color(GV_INK),
        'margin' => p_size(0)
    ]);
    // Tile detail
    style_small_meta($tile['elements'][1], GV_MUTED);
}
unset($tile);

// Checks row (52a4950a)
$checksRow = &$colContent['elements'][3]; // mpcbc-kids_checks_row
ensure_class_id($checksRow);
set_variant_props($checksRow, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'gap' => p_size(24),
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);
set_variant_props($checksRow, 'mobile', [
    'flex-direction' => p_string('column'),
    'gap' => p_size(12)
]);

foreach ($checksRow['elements'] as &$col) {
    ensure_class_id($col);
    set_variant_props($col, 'desktop', [
        'display' => p_string('flex'),
        'flex-direction' => p_string('column'),
        'gap' => p_size(8),
        'padding' => p_size(0)
    ]);
    foreach ($col['elements'] as &$chk) {
        style_body($chk, GV_INK);
    }
    unset($chk);
}
unset($col);

// Chinese note (1aa4f122)
$zhNote = &$colContent['elements'][4]; // mpcbc-kids_zh_note
ensure_class_id($zhNote);
set_variant_props($zhNote, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(4),
    'padding' => p_size(0)
]);
style_chinese_subtitle($zhNote['elements'][0], GV_INK, false);
set_variant_props($zhNote['elements'][0], 'desktop', ['font-size' => p_size(15)]);
style_chinese_subtitle($zhNote['elements'][1], GV_INK, true);
set_variant_props($zhNote['elements'][1], 'desktop', ['font-size' => p_size(15)]);

// Button (7432e8d8): Persimmon background with On persimmon text, 999px radius
$btn = &$colContent['elements'][5]; // mpcbc-kids_btn
style_button($btn, true, GV_PERSIMMON, GV_ON_PERSIMMON);

// Right column (5379b124): one large image (Sunday school photo), other 2 removed
$colImage = &$cardInner['elements'][1]; // mpcbc-kids_col_collage
reset_box($colImage);
set_variant_props($colImage, 'desktop', [
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
    'padding' => p_size(0),
    'background' => p_bg_transparent(),
    'border-style' => p_string('none'),
    'border-radius' => p_radius(0),
    'overflow' => p_string('hidden')
]);
set_variant_props($colImage, 'tablet', [
    'width' => p_size(100, '%')
]);
set_variant_props($colImage, 'mobile', [
    'width' => p_size(100, '%')
]);

// Keep ONLY the first image (Sunday school photo, 5fa2e479)
$sundaySchoolImg = $colImage['elements'][0];
reset_box($sundaySchoolImg);
// Remove any transform rotate / absolute positioning from previous collage
$classId = ensure_class_id($sundaySchoolImg);
foreach ($sundaySchoolImg['styles'][$classId]['variants'] as &$v) {
    unset($v['props']['transform']);
    unset($v['props']['box-shadow']);
    unset($v['props']['position']);
    unset($v['props']['top']);
    unset($v['props']['bottom']);
    unset($v['props']['left']);
    unset($v['props']['right']);
}
unset($v);

set_variant_props($sundaySchoolImg, 'desktop', [
    'width' => p_size(100, '%'),
    'height' => p_size(100, '%'),
    'min-height' => p_size(460),
    'aspect-ratio' => p_string('4 / 3'),
    'object-fit' => p_string('cover'),
    'border-radius' => p_radius(0),
    'display' => p_string('block')
]);
set_variant_props($sundaySchoolImg, 'tablet', [
    'width' => p_size(100, '%'),
    'height' => p_size('', 'auto'),
    'aspect-ratio' => p_string('16 / 9')
]);
set_variant_props($sundaySchoolImg, 'mobile', [
    'width' => p_size(100, '%'),
    'height' => p_size('', 'auto'),
    'aspect-ratio' => p_string('16 / 9')
]);

$colImage['elements'] = [$sundaySchoolImg];

// Save to post 21
$admins = get_users(['role' => 'administrator', 'number' => 1]);
wp_set_current_user($admins[0]->ID);
$doc = Elementor\Plugin::$instance->documents->get(21);
$settings = get_post_meta(21, '_elementor_page_settings', true) ?: [];
if (!$doc->save(['elements' => $data, 'settings' => $settings])) {
    throw new RuntimeException("Save to post 21 failed");
}
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Section 5 applied and saved successfully.\n";

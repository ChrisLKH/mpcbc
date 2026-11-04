<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';
require_once __DIR__ . '/visual-pass-helpers.php';

$raw = get_post_meta(21, '_elementor_data', true);
$data = json_decode($raw, true);

// Section 4 is index 4
$sec = &$data[4];

// 1. Style outer section: Deep sea ground, full width, 120/88/64 padding
style_outer_section($sec, GV_DEEP_SEA);

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
$headRow = &$inner['elements'][0]; // mpcbc-community_head
ensure_class_id($headRow);
set_variant_props($headRow, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'align-items' => p_string('flex-start'),
    'gap' => p_size(12),
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);

$headGroup = &$headRow['elements'][0]; // mpcbc-community_heading_group
ensure_class_id($headGroup);
set_variant_props($headGroup, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'align-items' => p_string('flex-start'),
    'gap' => p_size(6),
    'padding' => p_size(0)
]);

// H2 "Community" in White
$h2 = &$headGroup['elements'][0];
style_display_heading($h2, GV_WHITE);

// Chinese subtitles in White
$subTC = &$headGroup['elements'][1];
style_chinese_subtitle($subTC, GV_WHITE, false);
$subSC = &$headGroup['elements'][2];
style_chinese_subtitle($subSC, GV_WHITE, true);

// Intro line in White
$intro = &$headRow['elements'][1]; // mpcbc-community_lede
style_body($intro, GV_WHITE);

// 3. Nested tabs widget
$tabs = &$inner['elements'][1]; // mpcbc-community_nested_tabs
ensure_class_id($tabs);
set_variant_props($tabs, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'gap' => p_size(40),
    'align-items' => p_string('stretch'),
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);
set_variant_props($tabs, 'tablet', [
    'flex-direction' => p_string('column'),
    'gap' => p_size(32)
]);
set_variant_props($tabs, 'mobile', [
    'flex-direction' => p_string('column'),
    'gap' => p_size(24)
]);

// Tabs menu
$tabsMenu = &$tabs['elements'][0]; // e-tabs-menu
ensure_class_id($tabsMenu);
set_variant_props($tabsMenu, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(12),
    'width' => p_size(320),
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(0),
            'flexShrink' => p_number(0),
            'flexBasis' => p_size(320)
        ]
    ],
    'padding' => p_size(0)
]);
set_variant_props($tabsMenu, 'tablet', [
    'width' => p_size(100, '%'),
    'flex-direction' => p_string('row'),
    'flex-wrap' => p_string('wrap'),
    'gap' => p_size(12)
]);
set_variant_props($tabsMenu, 'mobile', [
    'width' => p_size(100, '%'),
    'flex-direction' => p_string('column'),
    'align-items' => p_string('stretch'),
    'gap' => p_size(8)
]);

// Each tab in menu
foreach ($tabsMenu['elements'] as &$tab) {
    reset_box($tab);
    // Desktop unselected
    set_variant_props($tab, 'desktop', [
        'border-radius' => p_radius(0),
        'border-style' => p_string('none'),
        'border-width' => p_size(0),
        'padding' => p_dimensions(16, 20, 16, 20),
        'text-align' => p_string('start'),
        'width' => p_size(100, '%'),
        'background' => p_bg_transparent(),
        'color' => p_global_color(GV_WHITE)
    ]);
    // Active state
    set_variant_props($tab, 'desktop', [
        'background' => p_bg_color(GV_HARBOR),
        'color' => p_global_color(GV_WHITE),
        'border-radius' => p_radius(0)
    ], 'active');

    set_variant_props($tab, 'mobile', [
        'width' => p_size(100, '%'),
        'text-align' => p_string('start'),
        'padding' => p_dimensions(14, 16, 14, 16),
        'border-radius' => p_radius(0)
    ]);

    // Paragraphs inside tab label flexbox
    if (isset($tab['elements'][0]['elements'])) {
        foreach ($tab['elements'][0]['elements'] as &$lbl) {
            ensure_class_id($lbl);
            set_variant_props($lbl, 'desktop', [
                'font-family' => p_global_font(GV_FONT_DISPLAY),
                'font-size' => p_size(20),
                'font-weight' => p_string('600'),
                'color' => p_global_color(GV_WHITE),
                'margin' => p_size(0)
            ]);
        }
        unset($lbl);
    }
}
unset($tab);

// Tabs content area
$contentArea = &$tabs['elements'][1]; // e-tabs-content-area
ensure_class_id($contentArea);
set_variant_props($contentArea, 'desktop', [
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(1),
            'flexShrink' => p_number(1),
            'flexBasis' => p_size(0, '%')
        ]
    ],
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);

// Each tab content panel
foreach ($contentArea['elements'] as &$tabContent) {
    // Inside is the panel container (mpcbc-comm_panel_con_X)
    $panelCon = &$tabContent['elements'][0];
    reset_box($panelCon);

    // Panel container: square aspect-ratio, 0px radius, content at bottom-left
    set_variant_props($panelCon, 'desktop', [
        'aspect-ratio' => p_string('1 / 1'),
        'min-height' => p_size(480),
        'border-radius' => p_radius(0),
        'border-style' => p_string('none'),
        'padding' => p_dimensions(40, 40, 40, 40),
        'display' => p_string('flex'),
        'flex-direction' => p_string('column'),
        'justify-content' => p_string('flex-end'),
        'align-items' => p_string('flex-start'),
        'gap' => p_size(12)
    ]);
    set_variant_props($panelCon, 'tablet', [
        'min-height' => p_size(400),
        'aspect-ratio' => p_string('1 / 1'),
        'padding' => p_dimensions(32, 32, 32, 32)
    ]);
    set_variant_props($panelCon, 'mobile', [
        'min-height' => p_size(360),
        'aspect-ratio' => p_string('1 / 1'),
        'padding' => p_dimensions(24, 20, 24, 20)
    ]);

    // Text elements inside panel:
    // [0] H3 title, [1] H3 TC, [2] H3 SC, [3] description, [4] buttons flexbox
    if (isset($panelCon['elements'][0])) {
        // H3 title
        style_subtitle($panelCon['elements'][0], GV_WHITE, GV_FONT_DISPLAY, '700');
        set_variant_props($panelCon['elements'][0], 'desktop', ['font-size' => p_size(28)]);
    }
    if (isset($panelCon['elements'][1])) {
        // H3 TC
        style_chinese_subtitle($panelCon['elements'][1], GV_WHITE, false);
    }
    if (isset($panelCon['elements'][2])) {
        // H3 SC
        style_chinese_subtitle($panelCon['elements'][2], GV_WHITE, true);
    }
    if (isset($panelCon['elements'][3])) {
        // Description
        style_body($panelCon['elements'][3], GV_WHITE);
    }

    // Buttons
    if (isset($panelCon['elements'][4])) {
        $btnCon = &$panelCon['elements'][4];
        ensure_class_id($btnCon);
        set_variant_props($btnCon, 'desktop', [
            'display' => p_string('flex'),
            'flex-direction' => p_string('row'),
            'gap' => p_size(12),
            'align-items' => p_string('center'),
            'flex-wrap' => p_string('wrap'),
            'padding' => p_size(0),
            'margin' => p_dimensions(8, 0, 0, 0)
        ]);

        foreach ($btnCon['elements'] as &$btnItem) {
            $type = $btnItem['widgetType'] ?? $btnItem['elType'];
            if ($type === 'e-button') {
                style_button($btnItem, true, GV_WHITE, GV_INK);
            } elseif ($type === 'e-flexbox') {
                // Button group (like Cantonese / Mandarin bilingual button)
                style_button($btnItem, true, GV_WHITE, GV_INK);
                foreach ($btnItem['elements'] as &$subTxt) {
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
            } elseif ($type === 'e-paragraph') {
                // Text link (e.g. "More about kids")
                ensure_class_id($btnItem);
                set_variant_props($btnItem, 'desktop', [
                    'font-family' => p_global_font(GV_FONT_TEXT),
                    'font-size' => p_size(16),
                    'font-weight' => p_string('600'),
                    'color' => p_global_color(GV_WHITE),
                    'text-decoration' => p_string('underline'),
                    'margin' => p_size(0)
                ]);
            }
        }
        unset($btnItem);
    }
}
unset($tabContent);

// Save to post 21
$admins = get_users(['role' => 'administrator', 'number' => 1]);
wp_set_current_user($admins[0]->ID);
$doc = Elementor\Plugin::$instance->documents->get(21);
$settings = get_post_meta(21, '_elementor_page_settings', true) ?: [];
if (!$doc->save(['elements' => $data, 'settings' => $settings])) {
    throw new RuntimeException("Save to post 21 failed");
}
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Section 4 applied and saved successfully.\n";

<?php
/**
 * Visual pass helpers for Elementor Atomic V4 elements and styles.
 */

function p_size($size, $unit = 'px') {
    return [
        '$$type' => 'size',
        'value' => [
            'size' => $size,
            'unit' => $unit
        ]
    ];
}

function p_dimensions($top, $right, $bottom, $left, $unit = 'px') {
    return [
        '$$type' => 'dimensions',
        'value' => [
            'block-start' => p_size($top, $unit),
            'inline-end' => p_size($right, $unit),
            'block-end' => p_size($bottom, $unit),
            'inline-start' => p_size($left, $unit),
        ]
    ];
}

function p_radius($r, $unit = 'px') {
    return [
        '$$type' => 'border-radius-v2',
        'value' => [
            'start-start' => p_size($r, $unit),
            'start-end' => p_size($r, $unit),
            'end-end' => p_size($r, $unit),
            'end-start' => p_size($r, $unit),
        ]
    ];
}

function p_string($val) {
    return [
        '$$type' => 'string',
        'value' => (string)$val
    ];
}

function p_number($val) {
    return [
        '$$type' => 'number',
        'value' => (float)$val
    ];
}

function p_global_color($varId) {
    return [
        '$$type' => 'global-color-variable',
        'value' => $varId
    ];
}

function p_global_font($varId) {
    return [
        '$$type' => 'global-font-variable',
        'value' => $varId
    ];
}

function p_bg_color($varId) {
    return [
        '$$type' => 'background',
        'value' => [
            'color' => p_global_color($varId)
        ]
    ];
}

function p_bg_transparent() {
    return [
        '$$type' => 'background',
        'value' => [
            'color' => [
                '$$type' => 'color',
                'value' => 'transparent'
            ]
        ]
    ];
}

// Global variable IDs from brief
define('GV_INK', 'e-gv-c70cdc4');       // #13232A
define('GV_MUTED', 'e-gv-7317e11');     // #4F6168
define('GV_PAPER', 'e-gv-f7cfe2a');     // #F6F7F5
define('GV_MIST', 'e-gv-824ebc4');      // #E3EBEA
define('GV_WHITE', 'e-gv-1984c94');     // #FFFFFF
define('GV_HARBOR', 'e-gv-1771cc2');    // #155E75
define('GV_DEEP_SEA', 'e-gv-5a81035');  // #0F2A33
define('GV_SOFT_PERSIMMON', 'e-gv-428b859'); // #FCE3CC
define('GV_PERSIMMON', 'e-gv-0c168a6'); // #F28C38
define('GV_ON_PERSIMMON', 'e-gv-96b5dde'); // #2B1404
define('GV_LINE_STRONG', 'e-gv-468ea41'); // #BFCDCC
define('GV_LINE', 'e-gv-3cc97d2');      // #D9E2E1

define('GV_FONT_DISPLAY', 'e-gv-d649b5e'); // Bricolage Grotesque
define('GV_FONT_TEXT', 'e-gv-f5301be');    // Geist
define('GV_FONT_CHINESE', 'e-gv-2635213'); // Noto Sans TC
define('GV_FONT_CHINESE_SC', 'e-gv-99d935c'); // Noto Sans SC

/**
 * Ensures an element has a local class ID in settings.classes and styles.
 */
function ensure_class_id(&$element) {
    if (!isset($element['settings'])) {
        $element['settings'] = [];
    }
    if (empty($element['settings']['classes']['value'])) {
        $classId = 'e-' . substr(md5(uniqid($element['id'] ?? 'el', true)), 0, 12);
        $element['settings']['classes'] = [
            '$$type' => 'classes',
            'value' => [$classId]
        ];
    } else {
        $classId = $element['settings']['classes']['value'][0];
    }
    if (!isset($element['styles'])) {
        $element['styles'] = [];
    }
    if (!isset($element['styles'][$classId])) {
        $element['styles'][$classId] = [
            'id' => $classId,
            'label' => 'local',
            'type' => 'class',
            'variants' => []
        ];
    }
    return $classId;
}

/**
 * Set props on a variant (breakpoint: desktop, tablet, mobile).
 */
function set_variant_props(&$element, $breakpoint, $props, $state = null) {
    $classId = ensure_class_id($element);
    $cls = &$element['styles'][$classId];
    $found = false;
    foreach ($cls['variants'] as &$v) {
        if ($v['meta']['breakpoint'] === $breakpoint && ($v['meta']['state'] ?? null) === $state) {
            foreach ($props as $k => $val) {
                if ($val === null) {
                    unset($v['props'][$k]);
                } else {
                    $v['props'][$k] = $val;
                }
            }
            $found = true;
            break;
        }
    }
    unset($v);
    if (!$found) {
        $newVariant = [
            'meta' => [
                'breakpoint' => $breakpoint,
                'state' => $state
            ],
            'props' => [],
            'custom_css' => null
        ];
        foreach ($props as $k => $val) {
            if ($val !== null) {
                $newVariant['props'][$k] = $val;
            }
        }
        $cls['variants'][] = $newVariant;
    }
}

/**
 * Helper to remove box shadow and set border radius 0 on containers/cards/images.
 */
function reset_box(&$element) {
    $classId = ensure_class_id($element);
    foreach ($element['styles'][$classId]['variants'] as &$v) {
        unset($v['props']['box-shadow']);
        $v['props']['border-radius'] = p_radius(0);
    }
    unset($v);
}

/**
 * Helper to style a button fully round (999px) with Body text.
 */
function style_button(&$button, $isPrimary = true, $bgColor = null, $textColor = null) {
    $classId = ensure_class_id($button);
    // Remove box shadow on all variants
    foreach ($button['styles'][$classId]['variants'] as &$v) {
        unset($v['props']['box-shadow']);
    }
    unset($v);

    $desktopProps = [
        'border-radius' => p_radius(999),
        'font-family' => p_global_font(GV_FONT_TEXT),
        'font-size' => p_size(16),
        'font-weight' => p_string('600'),
        'line-height' => p_size(1.4, 'em'),
        'padding' => p_dimensions(12, 24, 12, 24),
        'margin' => p_size(0),
        'width' => p_size('', 'auto'),
        'min-width' => p_size(0),
        'text-decoration' => p_string('none'),
        'display' => p_string('inline-flex'),
        'align-items' => p_string('center'),
        'justify-content' => p_string('center'),
    ];

    if ($isPrimary) {
        $desktopProps['background'] = p_bg_color($bgColor ?: GV_HARBOR);
        $desktopProps['color'] = p_global_color($textColor ?: GV_WHITE);
        $desktopProps['border-style'] = p_string('none');
    } else {
        $desktopProps['background'] = p_bg_transparent();
        $desktopProps['color'] = p_global_color($textColor ?: GV_INK);
        $desktopProps['border-style'] = p_string('solid');
        $desktopProps['border-width'] = p_size(1.5);
        $desktopProps['border-color'] = p_global_color(GV_LINE_STRONG);
    }

    set_variant_props($button, 'desktop', $desktopProps);
    set_variant_props($button, 'tablet', [
        'font-size' => p_size(16),
        'padding' => p_dimensions(12, 22, 12, 22)
    ]);
    set_variant_props($button, 'mobile', [
        'font-size' => p_size(16),
        'padding' => p_dimensions(12, 20, 12, 20)
    ]);
}

/**
 * Helper to style Display heading (72 / 56 / 44px).
 */
function style_display_heading(&$heading, $color = GV_INK) {
    ensure_class_id($heading);
    set_variant_props($heading, 'desktop', [
        'font-family' => p_global_font(GV_FONT_DISPLAY),
        'font-size' => p_size(72),
        'font-weight' => p_string('700'),
        'line-height' => p_size(1.0, 'em'),
        'letter-spacing' => p_size(-0.02, 'em'),
        'color' => p_global_color($color),
        'margin' => p_size(0)
    ]);
    set_variant_props($heading, 'tablet', [
        'font-size' => p_size(56),
        'line-height' => p_size(1.0, 'em')
    ]);
    set_variant_props($heading, 'mobile', [
        'font-size' => p_size(44),
        'line-height' => p_size(1.0, 'em')
    ]);
}

/**
 * Helper to style Chinese section subtitle (22 / 20 / 20px).
 */
function style_chinese_subtitle(&$sub, $color = GV_INK, $isSC = false) {
    ensure_class_id($sub);
    set_variant_props($sub, 'desktop', [
        'font-family' => p_global_font($isSC ? GV_FONT_CHINESE_SC : GV_FONT_CHINESE),
        'font-size' => p_size(22),
        'font-weight' => p_string('500'),
        'line-height' => p_size(1.3, 'em'),
        'color' => p_global_color($color),
        'margin' => p_size(0)
    ]);
    set_variant_props($sub, 'tablet', [
        'font-size' => p_size(20),
        'line-height' => p_size(1.3, 'em')
    ]);
    set_variant_props($sub, 'mobile', [
        'font-size' => p_size(20),
        'line-height' => p_size(1.3, 'em')
    ]);
}

/**
 * Helper to style Subtitle (22 / 20 / 20px).
 */
function style_subtitle(&$el, $color = GV_INK, $font = GV_FONT_DISPLAY, $weight = '700') {
    ensure_class_id($el);
    set_variant_props($el, 'desktop', [
        'font-family' => p_global_font($font),
        'font-size' => p_size(22),
        'font-weight' => p_string($weight),
        'line-height' => p_size(1.2, 'em'),
        'color' => p_global_color($color),
        'margin' => p_size(0)
    ]);
    set_variant_props($el, 'tablet', [
        'font-size' => p_size(20),
        'line-height' => p_size(1.2, 'em')
    ]);
    set_variant_props($el, 'mobile', [
        'font-size' => p_size(20),
        'line-height' => p_size(1.2, 'em')
    ]);
}

/**
 * Helper to style Body text (16px, line height 1.55).
 */
function style_body(&$el, $color = GV_INK, $font = GV_FONT_TEXT) {
    ensure_class_id($el);
    set_variant_props($el, 'desktop', [
        'font-family' => p_global_font($font),
        'font-size' => p_size(16),
        'font-weight' => p_string('400'),
        'line-height' => p_size(1.55, 'em'),
        'color' => p_global_color($color),
        'margin' => p_size(0)
    ]);
    set_variant_props($el, 'tablet', [
        'font-size' => p_size(16),
        'line-height' => p_size(1.55, 'em')
    ]);
    set_variant_props($el, 'mobile', [
        'font-size' => p_size(16),
        'line-height' => p_size(1.55, 'em')
    ]);
}

/**
 * Helper to style Small meta line (14px).
 */
function style_small_meta(&$el, $color = GV_MUTED, $font = GV_FONT_TEXT, $weight = '500') {
    ensure_class_id($el);
    set_variant_props($el, 'desktop', [
        'font-family' => p_global_font($font),
        'font-size' => p_size(14),
        'font-weight' => p_string($weight),
        'line-height' => p_size(1.4, 'em'),
        'color' => p_global_color($color),
        'margin' => p_size(0)
    ]);
    set_variant_props($el, 'tablet', [
        'font-size' => p_size(14),
        'line-height' => p_size(1.4, 'em')
    ]);
    set_variant_props($el, 'mobile', [
        'font-size' => p_size(14),
        'line-height' => p_size(1.4, 'em')
    ]);
}

/**
 * Helper to style outer section padding & ground.
 */
function style_outer_section(&$sec, $groundVarId = GV_PAPER) {
    ensure_class_id($sec);
    set_variant_props($sec, 'desktop', [
        'background' => p_bg_color($groundVarId),
        'padding' => p_dimensions(120, 56, 120, 56),
        'width' => p_size(100, '%'),
        'min-width' => p_size(0),
        'display' => p_string('flex'),
        'flex-direction' => p_string('column'),
        'align-items' => p_string('center')
    ]);
    set_variant_props($sec, 'tablet', [
        'padding' => p_dimensions(88, 32, 88, 32)
    ]);
    set_variant_props($sec, 'mobile', [
        'padding' => p_dimensions(64, 16, 64, 16)
    ]);
}

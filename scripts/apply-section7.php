<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';
require_once __DIR__ . '/visual-pass-helpers.php';

$raw = get_post_meta(21, '_elementor_data', true);
$data = json_decode($raw, true);

// Section 7 is index 7
$sec = &$data[7];

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

// Left column: mpcbc-connect_left_col (47e95ac8)
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

// Heading H2 "We’d love to hear from you."
$h2 = &$colLeft['elements'][0]; // mpcbc-connect_title
style_display_heading($h2, GV_INK);

// Chinese subtitles TC & SC
$subTC = &$colLeft['elements'][1];
style_chinese_subtitle($subTC, GV_INK, false);
$subSC = &$colLeft['elements'][2];
style_chinese_subtitle($subSC, GV_INK, true);

// Lede
$lede = &$colLeft['elements'][3]; // mpcbc-connect_lede
style_body($lede, GV_INK);

// Check if contact info block is already added under lede
$hasInfoBlock = false;
foreach ($colLeft['elements'] as $el) {
    if (($el['settings']['_cssid']['value'] ?? '') === 'mpcbc-connect_details_block') {
        $hasInfoBlock = true;
        break;
    }
}

if (!$hasInfoBlock) {
    $infoBlockId = 'e-flex-' . substr(md5('connect_details_block'), 0, 8);
    $addrId = substr(md5('connect_addr'), 0, 8);
    $phoneId = substr(md5('connect_phone'), 0, 8);
    $emailId = substr(md5('connect_email'), 0, 8);
    $hoursId = substr(md5('connect_hours'), 0, 8);

    $createParagraph = function($id, $cssid, $text) {
        $classId = 'e-p-' . $id;
        $p = [
            'id' => $id,
            'elType' => 'e-paragraph',
            'isInner' => true,
            'settings' => [
                '_cssid' => [
                    '$$type' => 'string',
                    'value' => $cssid
                ],
                'paragraph' => [
                    '$$type' => 'escaped-html',
                    'value' => $text
                ],
                'classes' => [
                    '$$type' => 'classes',
                    'value' => [$classId]
                ]
            ],
            'styles' => [
                $classId => [
                    'id' => $classId,
                    'label' => 'local',
                    'type' => 'class',
                    'variants' => [
                        [
                            'meta' => ['breakpoint' => 'desktop', 'state' => null],
                            'props' => [
                                'font-family' => p_global_font(GV_FONT_TEXT),
                                'font-size' => p_size(16),
                                'font-weight' => p_string('400'),
                                'line-height' => p_size(1.55, 'em'),
                                'color' => p_global_color(GV_INK),
                                'margin' => p_size(0)
                            ],
                            'custom_css' => null
                        ]
                    ]
                ]
            ]
        ];
        return $p;
    };

    $infoBlock = [
        'id' => substr(md5('connect_details_block'), 0, 8),
        'elType' => 'e-flexbox',
        'isInner' => true,
        'settings' => [
            '_cssid' => [
                '$$type' => 'string',
                'value' => 'mpcbc-connect_details_block'
            ],
            'classes' => [
                '$$type' => 'classes',
                'value' => [$infoBlockId]
            ]
        ],
        'styles' => [
            $infoBlockId => [
                'id' => $infoBlockId,
                'label' => 'local',
                'type' => 'class',
                'variants' => [
                    [
                        'meta' => ['breakpoint' => 'desktop', 'state' => null],
                        'props' => [
                            'display' => p_string('flex'),
                            'flex-direction' => p_string('column'),
                            'gap' => p_size(10),
                            'padding' => p_dimensions(20, 0, 0, 0),
                            'border-style' => p_string('solid'),
                            'border-width' => p_dimensions(1, 0, 0, 0),
                            'border-color' => p_global_color(GV_LINE),
                            'width' => p_size(100, '%')
                        ],
                        'custom_css' => null
                    ]
                ]
            ]
        ],
        'elements' => [
            $createParagraph($addrId, 'mpcbc-connect_detail_addr', '302 W. Commonwealth Ave., Alhambra, CA 91801'),
            $createParagraph($phoneId, 'mpcbc-connect_detail_phone', '(626) 289-1284'),
            $createParagraph($emailId, 'mpcbc-connect_detail_email', 'info@mpcbc.net'),
            $createParagraph($hoursId, 'mpcbc-connect_detail_hours', 'Office hours: [Days, times]')
        ]
    ];

    $colLeft['elements'][] = $infoBlock;
}

// Right card: White card, 0px radius, no shadow (6dd7313c)
$cardRight = &$inner['elements'][1];
reset_box($cardRight);
set_variant_props($cardRight, 'desktop', [
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(1.2),
            'flexShrink' => p_number(1),
            'flexBasis' => p_size(0, '%')
        ]
    ],
    'background' => p_bg_color(GV_WHITE),
    'border-style' => p_string('solid'),
    'border-width' => p_size(1),
    'border-color' => p_global_color(GV_LINE),
    'border-radius' => p_radius(0),
    'padding' => p_dimensions(36, 36, 36, 36),
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'width' => p_size(100, '%')
]);
set_variant_props($cardRight, 'tablet', [
    'padding' => p_dimensions(32, 28, 32, 28)
]);
set_variant_props($cardRight, 'mobile', [
    'padding' => p_dimensions(24, 18, 24, 18)
]);

// Tabs inside right card (7e27c309)
$tabs = &$cardRight['elements'][0];
ensure_class_id($tabs);
set_variant_props($tabs, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(24),
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);

// Tabs menu
$tabsMenu = &$tabs['elements'][0];
ensure_class_id($tabsMenu);
set_variant_props($tabsMenu, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'gap' => p_size(20),
    'width' => p_size(100, '%'),
    'padding' => p_dimensions(0, 0, 4, 0),
    'border-style' => p_string('solid'),
    'border-width' => p_dimensions(0, 0, 1, 0),
    'border-color' => p_global_color(GV_LINE)
]);

// Each tab in menu: 0px radius, Ink text, 3px Primary bottom border on active
foreach ($tabsMenu['elements'] as &$tab) {
    reset_box($tab);
    set_variant_props($tab, 'desktop', [
        'border-radius' => p_radius(0),
        'border-style' => p_string('none'),
        'padding' => p_dimensions(8, 12, 12, 12),
        'background' => p_bg_transparent(),
        'color' => p_global_color(GV_MUTED)
    ]);
    set_variant_props($tab, 'desktop', [
        'color' => p_global_color(GV_INK),
        'border-style' => p_string('solid'),
        'border-width' => p_dimensions(0, 0, 3, 0),
        'border-color' => p_global_color(GV_HARBOR),
        'border-radius' => p_radius(0)
    ], 'active');

    // Label flexbox
    if (isset($tab['elements'][0]['elements'])) {
        foreach ($tab['elements'][0]['elements'] as &$lbl) {
            ensure_class_id($lbl);
            set_variant_props($lbl, 'desktop', [
                'font-family' => p_global_font(GV_FONT_DISPLAY),
                'font-size' => p_size(18),
                'font-weight' => p_string('600'),
                'margin' => p_size(0)
            ]);
        }
        unset($lbl);
    }
}
unset($tab);

// Content area
$contentArea = &$tabs['elements'][1];
ensure_class_id($contentArea);
set_variant_props($contentArea, 'desktop', [
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
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
echo "Section 7 applied and saved successfully.\n";

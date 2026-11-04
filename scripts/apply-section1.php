<?php
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';
require_once __DIR__ . '/visual-pass-helpers.php';

$raw = get_post_meta(21, '_elementor_data', true);
$data = json_decode($raw, true);

// Section 1 is index 1
$sec = &$data[1];

// 1. Style outer section
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

// 2. Head row: intro line moves under heading
$headRow = &$inner['elements'][0]; // mpcbc-watch_head_row
ensure_class_id($headRow);
set_variant_props($headRow, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'align-items' => p_string('flex-start'),
    'gap' => p_size(12),
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);

// Heading group inside head row
$headGroup = &$headRow['elements'][0]; // mpcbc-watch_heading_group
ensure_class_id($headGroup);
set_variant_props($headGroup, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'align-items' => p_string('flex-start'),
    'gap' => p_size(6),
    'padding' => p_size(0)
]);

// H2 "This Sunday"
$h2 = &$headGroup['elements'][0];
style_display_heading($h2, GV_INK);

// Chinese subtitle TC & SC
$subTC = &$headGroup['elements'][1];
style_chinese_subtitle($subTC, GV_INK, false);
$subSC = &$headGroup['elements'][2];
style_chinese_subtitle($subSC, GV_INK, true);

// Intro line
$intro = &$headRow['elements'][1]; // mpcbc-watch_lede
style_body($intro, GV_INK);

// 3. Cards row: Cantonese featured (60% width), English & Mandarin stacked on right
$cardsRow = &$inner['elements'][1]; // mpcbc-watch_cards_row
ensure_class_id($cardsRow);
set_variant_props($cardsRow, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'align-items' => p_string('flex-start'),
    'gap' => p_size(48),
    'width' => p_size(100, '%'),
    'padding' => p_size(0)
]);
set_variant_props($cardsRow, 'tablet', [
    'flex-direction' => p_string('column'),
    'gap' => p_size(40)
]);
set_variant_props($cardsRow, 'mobile', [
    'flex-direction' => p_string('column'),
    'gap' => p_size(36)
]);

// Check if English and Mandarin are already wrapped in right-col
$cantoneseCard = null;
$englishCard = null;
$mandarinCard = null;

foreach ($cardsRow['elements'] as $idx => &$child) {
    $cid = $child['settings']['_cssid']['value'] ?? ($child['settings']['_cssid'] ?? '');
    if ($cid === 'mpcbc-watch_card_cantonese') {
        $cantoneseCard = $child;
    } elseif ($cid === 'mpcbc-watch_card_english') {
        $englishCard = $child;
    } elseif ($cid === 'mpcbc-watch_card_mandarin') {
        $mandarinCard = $child;
    } elseif ($cid === 'mpcbc-watch_right_col') {
        // already wrapped
        $englishCard = $child['elements'][0];
        $mandarinCard = $child['elements'][1];
    }
}
unset($child);

if (!$cantoneseCard || !$englishCard || !$mandarinCard) {
    throw new RuntimeException("Could not find all 3 cards in watch section");
}

// Reset Cantonese card
reset_box($cantoneseCard);
set_variant_props($cantoneseCard, 'desktop', [
    'width' => p_size(58, '%'),
    'max-width' => p_size(58, '%'),
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(0),
            'flexShrink' => p_number(0),
            'flexBasis' => p_size(58, '%')
        ]
    ],
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(20),
    'background' => p_bg_transparent(),
    'border-style' => p_string('none'),
    'border-width' => p_size(0),
    'padding' => p_size(0)
]);
set_variant_props($cantoneseCard, 'tablet', [
    'width' => p_size(100, '%'),
    'max-width' => p_size(100, '%'),
    'flex' => [
        '$$type' => 'flex',
        'value' => [
            'flexGrow' => p_number(1),
            'flexShrink' => p_number(1),
            'flexBasis' => p_size(100, '%')
        ]
    ]
]);
set_variant_props($cantoneseCard, 'mobile', [
    'width' => p_size(100, '%'),
    'max-width' => p_size(100, '%'),
    'gap' => p_size(16)
]);

// Cantonese thumbnail: full width of left column
$cantoThumb = &$cantoneseCard['elements'][0];
reset_box($cantoThumb);
set_variant_props($cantoThumb, 'desktop', [
    'width' => p_size(100, '%'),
    'height' => p_size('', 'auto'),
    'aspect-ratio' => p_string('16 / 9'),
    'object-fit' => p_string('cover'),
    'border-radius' => p_radius(0),
    'display' => p_string('block')
]);
set_variant_props($cantoThumb, 'tablet', [
    'width' => p_size(100, '%'),
    'aspect-ratio' => p_string('16 / 9')
]);
set_variant_props($cantoThumb, 'mobile', [
    'width' => p_size(100, '%'),
    'aspect-ratio' => p_string('16 / 9'),
    'display' => p_string('block')
]);

// Cantonese text container
$cantoText = &$cantoneseCard['elements'][1];
reset_box($cantoText);
set_variant_props($cantoText, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('column'),
    'gap' => p_size(10),
    'padding' => p_size(0),
    'background' => p_bg_transparent(),
    'border-style' => p_string('none'),
    'width' => p_size(100, '%')
]);

// Cantonese titles: H3 TC / SC
style_subtitle($cantoText['elements'][0], GV_INK, GV_FONT_CHINESE, '700');
style_subtitle($cantoText['elements'][1], GV_INK, GV_FONT_CHINESE_SC, '700');

// Cantonese meta
style_small_meta($cantoText['elements'][2], GV_MUTED);

// Cantonese desc
style_body($cantoText['elements'][3], GV_INK);

// Cantonese buttons
$cantoBtns = &$cantoText['elements'][4];
ensure_class_id($cantoBtns);
set_variant_props($cantoBtns, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'gap' => p_size(12),
    'align-items' => p_string('center'),
    'flex-wrap' => p_string('wrap'),
    'padding' => p_size(0)
]);
style_button($cantoBtns['elements'][0], true, GV_HARBOR, GV_WHITE);

// Cantonese archive button (flexbox group)
$cantoArchGroup = &$cantoBtns['elements'][1];
style_button($cantoArchGroup, false, null, GV_INK);
foreach ($cantoArchGroup['elements'] as &$pItem) {
    ensure_class_id($pItem);
    set_variant_props($pItem, 'desktop', [
        'font-family' => p_global_font(GV_FONT_TEXT),
        'font-size' => p_size(16),
        'font-weight' => p_string('600'),
        'color' => p_global_color(GV_INK),
        'margin' => p_size(0)
    ]);
}
unset($pItem);

// Right column wrapper for English & Mandarin
$rightColId = 'e-flex-' . substr(md5('watch_right_col'), 0, 8);
$rightCol = [
    'id' => substr(md5('watch_right_col'), 0, 8),
    'elType' => 'e-flexbox',
    'isInner' => true,
    'settings' => [
        '_cssid' => [
            '$$type' => 'string',
            'value' => 'mpcbc-watch_right_col'
        ],
        'classes' => [
            '$$type' => 'classes',
            'value' => [$rightColId]
        ]
    ],
    'styles' => [
        $rightColId => [
            'id' => $rightColId,
            'label' => 'local',
            'type' => 'class',
            'variants' => [
                [
                    'meta' => ['breakpoint' => 'desktop', 'state' => null],
                    'props' => [
                        'width' => p_size(100, '%'),
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
                        'gap' => p_size(36),
                        'padding' => p_size(0)
                    ],
                    'custom_css' => null
                ],
                [
                    'meta' => ['breakpoint' => 'tablet', 'state' => null],
                    'props' => [
                        'width' => p_size(100, '%'),
                        'gap' => p_size(32)
                    ],
                    'custom_css' => null
                ],
                [
                    'meta' => ['breakpoint' => 'mobile', 'state' => null],
                    'props' => [
                        'width' => p_size(100, '%'),
                        'gap' => p_size(28)
                    ],
                    'custom_css' => null
                ]
            ]
        ]
    ],
    'elements' => [$englishCard, $mandarinCard]
];

// Helper to style English & Mandarin side cards
$styleSideCard = function(&$card, $isMandarin = false) {
    reset_box($card);
    set_variant_props($card, 'desktop', [
        'display' => p_string('flex'),
        'flex-direction' => p_string('row'),
        'align-items' => p_string('flex-start'),
        'gap' => p_size(20),
        'width' => p_size(100, '%'),
        'padding' => p_size(0),
        'background' => p_bg_transparent(),
        'border-style' => p_string('none'),
        'border-width' => p_size(0)
    ]);
    set_variant_props($card, 'tablet', [
        'flex-direction' => p_string('row'),
        'gap' => p_size(20)
    ]);
    set_variant_props($card, 'mobile', [
        'flex-direction' => p_string('column'),
        'gap' => p_size(14)
    ]);

    // Thumbnail
    $thumb = &$card['elements'][0];
    reset_box($thumb);
    set_variant_props($thumb, 'desktop', [
        'width' => p_size(180),
        'flex' => [
            '$$type' => 'flex',
            'value' => [
                'flexGrow' => p_number(0),
                'flexShrink' => p_number(0),
                'flexBasis' => p_size(180)
            ]
        ],
        'aspect-ratio' => p_string('16 / 9'),
        'object-fit' => p_string('cover'),
        'border-radius' => p_radius(0),
        'display' => p_string('block')
    ]);
    set_variant_props($thumb, 'tablet', [
        'width' => p_size(180),
        'flex' => [
            '$$type' => 'flex',
            'value' => [
                'flexGrow' => p_number(0),
                'flexShrink' => p_number(0),
                'flexBasis' => p_size(180)
            ]
        ],
        'display' => p_string('block')
    ]);
    set_variant_props($thumb, 'mobile', [
        'width' => p_size(100, '%'),
        'aspect-ratio' => p_string('16 / 9'),
        'display' => p_string('block')
    ]);

    // Text container
    $text = &$card['elements'][1];
    reset_box($text);
    set_variant_props($text, 'desktop', [
        'display' => p_string('flex'),
        'flex-direction' => p_string('column'),
        'gap' => p_size(8),
        'flex' => [
            '$$type' => 'flex',
            'value' => [
                'flexGrow' => p_number(1),
                'flexShrink' => p_number(1),
                'flexBasis' => p_size(0, '%')
            ]
        ],
        'padding' => p_size(0),
        'background' => p_bg_transparent(),
        'border-style' => p_string('none')
    ]);

    if (!$isMandarin) {
        // English title
        style_subtitle($text['elements'][0], GV_INK, GV_FONT_DISPLAY, '700');
        // Meta
        style_small_meta($text['elements'][1], GV_MUTED);
        // Desc
        style_body($text['elements'][2], GV_INK);
        // Buttons
        $btns = &$text['elements'][3];
        ensure_class_id($btns);
        set_variant_props($btns, 'desktop', [
            'display' => p_string('flex'),
            'flex-direction' => p_string('row'),
            'gap' => p_size(12),
            'align-items' => p_string('center'),
            'flex-wrap' => p_string('wrap'),
            'padding' => p_size(0)
        ]);
        style_button($btns['elements'][0], true, GV_HARBOR, GV_WHITE);
        style_button($btns['elements'][1], false, null, GV_INK);
    } else {
        // Mandarin titles TC & SC
        style_subtitle($text['elements'][0], GV_INK, GV_FONT_CHINESE, '700');
        style_subtitle($text['elements'][1], GV_INK, GV_FONT_CHINESE_SC, '700');
        // Meta
        style_small_meta($text['elements'][2], GV_MUTED);
        // Desc
        style_body($text['elements'][3], GV_INK);
        // Buttons
        $btns = &$text['elements'][4];
        ensure_class_id($btns);
        set_variant_props($btns, 'desktop', [
            'display' => p_string('flex'),
            'flex-direction' => p_string('row'),
            'gap' => p_size(12),
            'align-items' => p_string('center'),
            'flex-wrap' => p_string('wrap'),
            'padding' => p_size(0)
        ]);
        style_button($btns['elements'][0], true, GV_HARBOR, GV_WHITE);
        $mandArchGroup = &$btns['elements'][1];
        style_button($mandArchGroup, false, null, GV_INK);
        foreach ($mandArchGroup['elements'] as &$pItem) {
            ensure_class_id($pItem);
            set_variant_props($pItem, 'desktop', [
                'font-family' => p_global_font(GV_FONT_TEXT),
                'font-size' => p_size(16),
                'font-weight' => p_string('600'),
                'color' => p_global_color(GV_INK),
                'margin' => p_size(0)
            ]);
        }
        unset($pItem);
    }
};

$styleSideCard($rightCol['elements'][0], false);
$styleSideCard($rightCol['elements'][1], true);

// Set cardsRow elements to [Cantonese, rightCol]
$cardsRow['elements'] = [$cantoneseCard, $rightCol];

// 4. Footer row
$footRow = &$inner['elements'][2]; // mpcbc-watch_foot_row
ensure_class_id($footRow);
set_variant_props($footRow, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'justify-content' => p_string('space-between'),
    'align-items' => p_string('center'),
    'gap' => p_size(24),
    'width' => p_size(100, '%'),
    'padding' => p_dimensions(28, 0, 0, 0),
    'border-style' => p_string('solid'),
    'border-width' => p_dimensions(1, 0, 0, 0),
    'border-color' => p_global_color(GV_LINE)
]);
set_variant_props($footRow, 'tablet', [
    'flex-direction' => p_string('column'),
    'align-items' => p_string('flex-start'),
    'gap' => p_size(16)
]);
set_variant_props($footRow, 'mobile', [
    'flex-direction' => p_string('column'),
    'align-items' => p_string('flex-start'),
    'gap' => p_size(16)
]);

style_body($footRow['elements'][0], GV_INK);

$footBtns = &$footRow['elements'][1];
ensure_class_id($footBtns);
set_variant_props($footBtns, 'desktop', [
    'display' => p_string('flex'),
    'flex-direction' => p_string('row'),
    'gap' => p_size(12),
    'align-items' => p_string('center'),
    'flex-wrap' => p_string('wrap'),
    'padding' => p_size(0)
]);
style_button($footBtns['elements'][0], true, GV_HARBOR, GV_WHITE);
style_button($footBtns['elements'][1], false, null, GV_INK);

// Save back to post 21
$admins = get_users(['role' => 'administrator', 'number' => 1]);
wp_set_current_user($admins[0]->ID);
$doc = Elementor\Plugin::$instance->documents->get(21);
$settings = get_post_meta(21, '_elementor_page_settings', true) ?: [];
if (!$doc->save(['elements' => $data, 'settings' => $settings])) {
    throw new RuntimeException("Save to post 21 failed");
}
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Section 1 applied and saved successfully.\n";

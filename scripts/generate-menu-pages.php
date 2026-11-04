<?php
$pages = [
    [
        'slug'    => 'visit',
        'title'   => 'Visit 初次來訪',
        'en'      => 'Visit',
        'zh'      => '初次來訪',
    ],
    [
        'slug'    => 'sundays',
        'title'   => 'Sundays 主日崇拜',
        'en'      => 'Sundays',
        'zh'      => '主日崇拜',
    ],
    [
        'slug'    => 'kids',
        'title'   => 'Kids & Youth 兒童及青少年',
        'en'      => 'Kids & Youth',
        'zh'      => '兒童及青少年',
    ],
    [
        'slug'    => 'watch',
        'title'   => 'Watch 線上崇拜',
        'en'      => 'Watch',
        'zh'      => '線上崇拜',
    ],
    [
        'slug'    => 'about',
        'title'   => 'About 關於我們',
        'en'      => 'About',
        'zh'      => '關於我們',
    ],
    [
        'slug'    => 'give',
        'title'   => 'Give 奉獻',
        'en'      => 'Give',
        'zh'      => '奉獻',
    ],
    [
        'slug'    => 'zh',
        'title'   => '中文',
        'en'      => 'Chinese Ministry',
        'zh'      => '中文 · 華人浸信會',
    ],
];

$dir = __DIR__ . '/../elementor';

foreach ( $pages as $p ) {
    $slug = $p['slug'];
    $data = [
        'version'       => '0.4',
        'title'         => $p['title'],
        'type'          => 'page',
        'page_settings' => [],
        'content'       => [
            [
                'id'       => $slug . '_root',
                'elType'   => 'container',
                'settings' => [
                    'content_width'         => 'boxed',
                    'boxed_width'           => [ 'unit' => 'px', 'size' => 900 ],
                    'min_height'            => [ 'unit' => 'vh', 'size' => 60 ],
                    'padding'               => [ 'unit' => 'px', 'top' => '120', 'right' => '32', 'bottom' => '120', 'left' => '32', 'isLinked' => false ],
                    'padding_mobile'        => [ 'unit' => 'px', 'top' => '64', 'right' => '20', 'bottom' => '64', 'left' => '20', 'isLinked' => false ],
                    'flex_direction'        => 'column',
                    'flex_justify_content'  => 'center',
                    'flex_align_items'      => 'center',
                    'flex_gap'              => [ 'unit' => 'px', 'size' => 24 ],
                ],
                'elements' => [
                    [
                        'id'         => $slug . '_title',
                        'elType'     => 'widget',
                        'widgetType' => 'heading',
                        'settings'   => [
                            'title'                   => $p['en'],
                            'header_size'             => 'h1',
                            'align'                   => 'center',
                            'title_color'             => '#16140F',
                            'typography_typography'   => 'custom',
                            'typography_font_family'  => 'Bricolage Grotesque',
                            'typography_font_size'    => [ 'unit' => 'px', 'size' => 64 ],
                            'typography_font_size_mobile' => [ 'unit' => 'px', 'size' => 44 ],
                            'typography_font_weight'  => '700',
                            '__globals__'             => [
                                'title_color' => 'globals/colors?id=text',
                            ],
                        ],
                        'isInner'    => false,
                        'elements'   => [],
                    ],
                    [
                        'id'         => $slug . '_zh',
                        'elType'     => 'widget',
                        'widgetType' => 'text-editor',
                        'settings'   => [
                            'editor' => '<p lang="zh-Hant" style="font-size:28px; line-height:1.5; color:#5C564D; text-align:center; font-family:\'Noto Sans TC\',sans-serif; margin:0;">' . htmlspecialchars($p['zh']) . '</p>',
                        ],
                        'isInner'    => false,
                        'elements'   => [],
                    ],
                    [
                        'id'         => $slug . '_desc',
                        'elType'     => 'widget',
                        'widgetType' => 'text-editor',
                        'settings'   => [
                            'editor' => '<p style="font-size:18px; line-height:1.6; color:#5C564D; text-align:center; max-width:48ch; margin:0;">[This section is currently being prepared as part of the MPCBC website update.]</p>',
                        ],
                        'isInner'    => false,
                        'elements'   => [],
                    ],
                    [
                        'id'         => $slug . '_btn',
                        'elType'     => 'widget',
                        'widgetType' => 'button',
                        'settings'   => [
                            'text'                   => 'Back to Home →',
                            'link'                   => [ 'url' => '/' ],
                            'button_text_color'      => '#ffffff',
                            'background_color'       => '#0E6B5C',
                            'border_radius'          => [ 'unit' => 'px', 'top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12, 'isLinked' => true ],
                            'typography_typography'  => 'custom',
                            'typography_font_weight' => '600',
                            'typography_font_size'   => [ 'unit' => 'px', 'size' => 16 ],
                            '__globals__'            => [
                                'background_color' => 'globals/colors?id=primary',
                            ],
                        ],
                        'isInner'    => false,
                        'elements'   => [],
                    ],
                ],
                'isInner'  => false,
            ],
        ],
    ];

    $filename = $dir . '/' . $slug . '-page.json';
    file_put_contents( $filename, json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    echo "Generated $filename\n";
}

<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

wp_set_current_user( 1 );

$media_map = json_decode( file_get_contents( 'C:/Projects/mpcbc/wordpress/scripts/media-map.json' ), true );

function &find_element_by_id(&$elements, $target_id) {
    foreach ($elements as &$el) {
        if (isset($el['id']) && $el['id'] === $target_id) {
            return $el;
        }
        if (!empty($el['elements'])) {
            $found =& find_element_by_id($el['elements'], $target_id);
            if ($found !== null) {
                return $found;
            }
        }
    }
    $null = null;
    return $null;
}

// =============================================================
// 1. WELCOME PAGE
// =============================================================
$w_json_path = 'C:/Projects/mpcbc/wordpress/elementor/welcome-canvas.json';
$welcome = json_decode( file_get_contents( $w_json_path ), true );

// 1.1 Clean logo placeholder -> change to Image widget
$logo_el =& find_element_by_id($welcome['content'], 'welcome_logo_placeholder');
if (!$logo_el) {
    $logo_el =& find_element_by_id($welcome['content'], 'welcome_logo');
}
if ($logo_el) {
    $logo_el['id'] = 'welcome_logo';
    $logo_el['elType'] = 'widget';
    $logo_el['widgetType'] = 'image';
    $logo_el['settings'] = [
        'image' => [
            'url' => $media_map['church-logo.png']['url'] ?? '',
            'id'  => $media_map['church-logo.png']['id'] ?? 78,
        ],
        'width' => [ 'unit' => 'px', 'size' => 140 ],
    ];
}

// 1.2 Clean welcome_sub_title
$sub_el =& find_element_by_id($welcome['content'], 'welcome_sub_title');
if ($sub_el) {
    $sub_el['settings'] = [
        'editor'                => '<p lang="zh-Hant">蒙特利公園華人浸信會</p>',
        'text_color'            => 'rgba(255,255,255,0.85)',
        'typography_typography' => 'custom',
        'typography_font_family'=> 'Noto Sans TC',
        'typography_font_size'  => [ 'unit' => 'px', 'size' => 44 ],
        'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.4 ],
    ];
}

file_put_contents( $w_json_path, json_encode( $welcome, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
update_post_meta( 17, '_elementor_data', wp_slash( json_encode( $welcome['content'] ) ) );
echo "Cleaned Welcome (Post 17).\n";

// =============================================================
// 2. CHINESE BAND
// =============================================================
$cb_path = 'C:/Projects/mpcbc/wordpress/elementor/home-chinese-band.json';
$cb = json_decode( file_get_contents( $cb_path ), true );

$cb_desc =& find_element_by_id($cb['content'], 'chinese_band_desc');
if ($cb_desc) {
    $cb_desc['settings'] = [
        'editor'                => '<p lang="zh-Hant">我們有中文資訊：崇拜時間、地址、兒童安排。</p>',
        'text_color'            => 'rgba(255,255,255,0.92)',
        'typography_typography' => 'custom',
        'typography_font_size'  => [ 'unit' => 'px', 'size' => 22 ],
        'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.7 ],
    ];
}

$cb_btn =& find_element_by_id($cb['content'], 'chinese_band_btn');
if ($cb_btn) {
    $cb_btn['settings'] = [
        'text'                  => '進入中文頁面 →',
        'link'                  => [ 'url' => '/zh/' ],
        'button_text_color'     => '#ffffff',
        'background_color'      => 'transparent',
        'border_border'         => 'solid',
        'border_color'          => '#ffffff',
        'border_width'          => [ 'unit' => 'px', 'top' => 2, 'right' => 2, 'bottom' => 2, 'left' => 2, 'isLinked' => true ],
        'border_radius'         => [ 'unit' => 'px', 'top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12, 'isLinked' => true ],
        'typography_typography' => 'custom',
        'typography_font_size'  => [ 'unit' => 'px', 'size' => 17 ],
        'typography_font_weight'=> '700',
        'padding'               => [ 'unit' => 'px', 'top' => 14, 'right' => 24, 'bottom' => 14, 'left' => 24, 'isLinked' => false ],
    ];
}

file_put_contents( $cb_path, json_encode( $cb, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Cleaned Chinese Band.\n";

// =============================================================
// 3. FIND YOUR PLACE
// =============================================================
$fyp_path = 'C:/Projects/mpcbc/wordpress/elementor/home-find-your-place.json';
$fyp = json_decode( file_get_contents( $fyp_path ), true );

// Clean desc
$fyp_desc =& find_element_by_id($fyp['content'], 'find_place_desc');
if ($fyp_desc) {
    $fyp_desc['settings'] = [
        'editor'                => '<p>Three services on one Sunday morning, and a place for your kids.</p>',
        'text_color'            => '#5C564D',
        '__globals__'           => [ 'text_color' => 'globals/colors?id=ink_soft' ],
        'typography_typography' => 'custom',
        'typography_font_size'  => [ 'unit' => 'px', 'size' => 20 ],
        'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.5 ],
    ];
}

// Clean grid settings if it has accidental editor
$grid =& find_element_by_id($fyp['content'], 'find_place_cards_grid');
if ($grid) {
    unset($grid['settings']['editor']);
    unset($grid['settings']['text_color']);
}

// Clean all 4 cards
$cards_info = [
    'en'    => [ 'meta' => 'In English', 'desc' => 'Honest teaching, good music, lunch after.', 'link_text' => 'See Sundays →', 'url' => '/sundays/', 'zh' => false ],
    'canto' => [ 'meta' => 'In Cantonese', 'desc' => '崇拜後一起飲茶傾偈。', 'link_text' => 'Learn more →', 'url' => '/zh/', 'zh' => true ],
    'manda' => [ 'meta' => 'In Mandarin', 'desc' => '新朋友很多，歡迎你。', 'link_text' => 'Learn more →', 'url' => '/zh/', 'zh' => true ],
    'kids'  => [ 'meta' => 'Ages 0–18', 'desc' => 'Safe, fun classes while you’re in the service.', 'link_text' => 'See Kids →', 'url' => '/kids/', 'zh' => false ],
];

foreach ($cards_info as $key => $info) {
    $meta =& find_element_by_id($fyp['content'], "card_{$key}_meta");
    if ($meta) {
        $meta['settings'] = [
            'editor'                => "<p>{$info['meta']}</p>",
            'text_color'            => '#ffffff',
            'typography_typography' => 'custom',
            'typography_font_size'  => [ 'unit' => 'px', 'size' => 16 ],
            'typography_font_weight'=> '600',
        ];
    }
    $desc =& find_element_by_id($fyp['content'], "card_{$key}_desc");
    if ($desc) {
        $desc_p = $info['zh'] ? "<p lang=\"zh-Hant\">{$info['desc']}</p>" : "<p>{$info['desc']}</p>";
        $desc['settings'] = [
            'editor'                => $desc_p,
            'text_color'            => 'rgba(255,255,255,0.9)',
            'typography_typography' => 'custom',
            'typography_font_size'  => [ 'unit' => 'px', 'size' => 16 ],
            'typography_line_height'=> [ 'unit' => 'em', 'size' => $info['zh'] ? 1.7 : 1.5 ],
        ];
    }
    $link =& find_element_by_id($fyp['content'], "card_{$key}_link");
    if ($link) {
        $link['settings'] = [
            'editor'                => "<p><a href=\"{$info['url']}\">{$info['link_text']}</a></p>",
            'text_color'            => '#ffffff',
            'typography_typography' => 'custom',
            'typography_font_size'  => [ 'unit' => 'px', 'size' => 16 ],
            'typography_font_weight'=> '600',
        ];
    }
}

file_put_contents( $fyp_path, json_encode( $fyp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Cleaned Find Your Place.\n";

// =============================================================
// 4. PASTOR & PRAYER
// =============================================================
$pp_path = 'C:/Projects/mpcbc/wordpress/elementor/home-pastor-prayer.json';
$pp = json_decode( file_get_contents( $pp_path ), true );

$p_img =& find_element_by_id($pp['content'], 'pastor_img_placeholder');
if (!$p_img) {
    $p_img =& find_element_by_id($pp['content'], 'pastor_portrait_img');
}
if ($p_img) {
    $p_img['id'] = 'pastor_portrait_img';
    $p_img['elType'] = 'widget';
    $p_img['widgetType'] = 'image';
    $p_img['settings'] = [
        'image'               => [
            'url' => $media_map['pastor-portrait.png']['url'] ?? '',
            'id'  => $media_map['pastor-portrait.png']['id'] ?? 80,
        ],
        'width'               => [ 'unit' => 'px', 'size' => 200 ],
        'height'              => [ 'unit' => 'px', 'size' => 240 ],
        'image_border_radius' => [
            'unit' => 'px', 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16, 'isLinked' => true
        ],
    ];
}

$p_author =& find_element_by_id($pp['content'], 'pastor_quote_author');
if ($p_author) {
    $p_author['settings'] = [
        'editor'                => '<p>[Pastor name], Senior Pastor</p>',
        'text_color'            => '#5C564D',
        '__globals__'           => [ 'text_color' => 'globals/colors?id=ink_soft' ],
        'typography_typography' => 'custom',
        'typography_font_size'  => [ 'unit' => 'px', 'size' => 18 ],
    ];
}

$pr_desc =& find_element_by_id($pp['content'], 'prayer_card_desc');
if ($pr_desc) {
    $pr_desc['settings'] = [
        'editor'                => '<p>Our pastors read every request and keep it private.</p>',
        'text_color'            => '#5C564D',
        '__globals__'           => [ 'text_color' => 'globals/colors?id=ink_soft' ],
        'typography_typography' => 'custom',
        'typography_font_size'  => [ 'unit' => 'px', 'size' => 17 ],
        'typography_line_height'=> [ 'unit' => 'em', 'size' => 1.5 ],
    ];
}

file_put_contents( $pp_path, json_encode( $pp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Cleaned Pastor & Prayer.\n";

// =============================================================
// 5. MERGE HOME AND UPDATE POST 21
// =============================================================
$home_sections = [
    'hero'            => 'C:/Projects/mpcbc/wordpress/elementor/home-hero.json',
    'times_card'      => 'C:/Projects/mpcbc/wordpress/elementor/home-times-card.json',
    'chinese_band'    => 'C:/Projects/mpcbc/wordpress/elementor/home-chinese-band.json',
    'find_your_place' => 'C:/Projects/mpcbc/wordpress/elementor/home-find-your-place.json',
    'pastor_prayer'   => 'C:/Projects/mpcbc/wordpress/elementor/home-pastor-prayer.json',
    'kids'            => 'C:/Projects/mpcbc/wordpress/elementor/home-kids.json',
    'events_messages' => 'C:/Projects/mpcbc/wordpress/elementor/home-events-messages.json',
    'stay_in_loop'    => 'C:/Projects/mpcbc/wordpress/elementor/home-stay-in-loop.json',
];

$merged_content = [];
foreach ($home_sections as $sec_name => $sec_file) {
    $sec_data = json_decode( file_get_contents( $sec_file ), true );
    if ( ! empty( $sec_data['content'][0] ) ) {
        $merged_content[] = $sec_data['content'][0];
    } else {
        echo "WARNING: Missing content in {$sec_name}\n";
    }
}

$home_full = [
    'version'       => '0.4',
    'title'         => 'MPCBC – Home',
    'type'          => 'page',
    'page_settings' => [],
    'content'       => $merged_content,
];

file_put_contents( 'C:/Projects/mpcbc/wordpress/elementor/home-full.json', json_encode( $home_full, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
update_post_meta( 21, '_elementor_data', wp_slash( json_encode( $merged_content ) ) );
echo "Merged and updated Home page (ID 21).\n";

// Clear cache
Elementor\Plugin::$instance->files_manager->clear_cache();
echo "Cleared Elementor cache.\n";

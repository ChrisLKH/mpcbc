<?php
if ( ! defined( 'ABSPATH' ) ) exit(1);
wp_set_current_user(1);

$elem_dir = __DIR__ . '/../elementor';

echo "=== 1. UPDATING 01 HERO (removing pause button) ===\n";
$hero = json_decode( file_get_contents( $elem_dir . '/home-hero.json' ), true );
// In hero_actions_wrap, keep only hero_btn_group, remove hero_btn_pause
if ( isset( $hero['content'][0]['elements'][3]['elements'] ) ) {
    $actions = &$hero['content'][0]['elements'][3]['elements'];
    $filtered = [];
    foreach ( $actions as $a ) {
        if ( $a['id'] !== 'hero_btn_pause' ) {
            $filtered[] = $a;
        }
    }
    $actions = $filtered;
}
file_put_contents( $elem_dir . '/home-hero.json', json_encode( $hero, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

echo "=== 2. UPDATING 02 SUNDAY TIMES CARD ===\n";
$times = json_decode( file_get_contents( $elem_dir . '/home-times-card.json' ), true );
$tc = &$times['content'][0]['elements'][0];
$tc['settings']['content_width'] = 'full';
$tc['settings']['flex_direction'] = 'row';
$tc['settings']['flex_direction_tablet'] = 'row';
$tc['settings']['flex_direction_mobile'] = 'row';
$tc['settings']['flex_wrap'] = 'wrap';
$tc['settings']['flex_justify_content'] = 'space-between';
$tc['settings']['flex_align_items'] = 'center';
$tc['settings']['flex_gap'] = [ 'column' => '8', 'row' => '16', 'unit' => 'px', 'isLinked' => false ];

// Columns: Header (18% desk, 100% tab, 100% mob)
$tc['elements'][0]['settings']['content_width'] = 'full';
$tc['elements'][0]['settings']['width'] = [ 'unit' => '%', 'size' => 18 ];
$tc['elements'][0]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$tc['elements'][0]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

// 4 Service Columns (Cantonese, English, Mandarin, Kids): 16% desk, 23% tab, 48% mob
for ( $i = 1; $i <= 4; $i++ ) {
    $col = &$tc['elements'][$i];
    $col['settings']['content_width'] = 'full';
    $col['settings']['width'] = [ 'unit' => '%', 'size' => 16 ];
    $col['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 23 ];
    $col['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 48 ];
}

// Links column: 14% desk, 100% tab, 100% mob
$tc['elements'][5]['settings']['content_width'] = 'full';
$tc['elements'][5]['settings']['width'] = [ 'unit' => '%', 'size' => 14 ];
$tc['elements'][5]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$tc['elements'][5]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

file_put_contents( $elem_dir . '/home-times-card.json', json_encode( $times, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

echo "=== 3. UPDATING 03 FIND YOUR PLACE ===\n";
$find = json_decode( file_get_contents( $elem_dir . '/home-find-your-place.json' ), true );
$froot = &$find['content'][0];
$froot['settings']['content_width'] = 'boxed';
$froot['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];

$fgrid = &$froot['elements'][1];
$fgrid['settings']['content_width'] = 'full';
$fgrid['settings']['flex_direction'] = 'row';
$fgrid['settings']['flex_direction_tablet'] = 'row';
$fgrid['settings']['flex_direction_mobile'] = 'column';
$fgrid['settings']['flex_wrap'] = 'wrap';
$fgrid['settings']['flex_justify_content'] = 'space-between';
$fgrid['settings']['flex_gap'] = [ 'column' => '18', 'row' => '18', 'unit' => 'px', 'isLinked' => true ];

// 4 child cards: 23.5% desk (4 across), 48% tab (2 across), 100% mob (1 across)
for ( $i = 0; $i < 4; $i++ ) {
    $card = &$fgrid['elements'][$i];
    $card['settings']['content_width'] = 'full';
    $card['settings']['width'] = [ 'unit' => '%', 'size' => 23.5 ];
    $card['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 48 ];
    $card['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];
    $card['settings']['min_height'] = [ 'unit' => 'px', 'size' => 560 ];
    $card['settings']['min_height_tablet'] = [ 'unit' => 'px', 'size' => 460 ];
    $card['settings']['min_height_mobile'] = [ 'unit' => 'px', 'size' => 380 ];
    
    // Card H3 typography
    $h3 = &$card['elements'][0];
    $h3['settings']['typography_typography'] = 'custom';
    $h3['settings']['typography_font_size'] = [ 'unit' => 'px', 'size' => 40 ];
    $h3['settings']['typography_font_size_tablet'] = [ 'unit' => 'px', 'size' => 32 ];
    $h3['settings']['typography_font_size_mobile'] = [ 'unit' => 'px', 'size' => 28 ];
}
file_put_contents( $elem_dir . '/home-find-your-place.json', json_encode( $find, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

echo "=== 4. UPDATING 04 CHINESE BAND ===\n";
$zh = json_decode( file_get_contents( $elem_dir . '/home-chinese-band.json' ), true );
$zh['content'][0]['elements'][0]['settings']['content_width'] = 'boxed';
$zh['content'][0]['elements'][0]['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$zh['content'][0]['elements'][0]['settings']['flex_direction'] = 'row';
$zh['content'][0]['elements'][0]['settings']['flex_direction_mobile'] = 'column';
$zh['content'][0]['elements'][0]['settings']['flex_justify_content'] = 'space-between';
$zh['content'][0]['elements'][0]['settings']['flex_align_items'] = 'center';
file_put_contents( $elem_dir . '/home-chinese-band.json', json_encode( $zh, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

echo "=== 5. UPDATING 05 KIDS SECTION ===\n";
$kids = json_decode( file_get_contents( $elem_dir . '/home-kids.json' ), true );
$kinner = &$kids['content'][0]['elements'][0];
$kinner['settings']['content_width'] = 'boxed';
$kinner['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$kinner['settings']['flex_direction'] = 'row';
$kinner['settings']['flex_direction_tablet'] = 'column';
$kinner['settings']['flex_direction_mobile'] = 'column';
$kinner['settings']['flex_justify_content'] = 'space-between';
$kinner['settings']['flex_align_items'] = 'center';

// Left col (content): 54% desk, 100% tab/mob
$kinner['elements'][0]['settings']['content_width'] = 'full';
$kinner['elements'][0]['settings']['width'] = [ 'unit' => '%', 'size' => 54 ];
$kinner['elements'][0]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$kinner['elements'][0]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

// 3 tiles in left col: 31% desk/tab, 100% mob
$tiles_row = &$kinner['elements'][0]['elements'][2];
$tiles_row['settings']['content_width'] = 'full';
$tiles_row['settings']['flex_direction'] = 'row';
$tiles_row['settings']['flex_direction_mobile'] = 'column';
$tiles_row['settings']['flex_wrap'] = 'wrap';
$tiles_row['settings']['flex_justify_content'] = 'space-between';
for ( $i = 0; $i < 3; $i++ ) {
    $t = &$tiles_row['elements'][$i];
    $t['settings']['content_width'] = 'full';
    $t['settings']['width'] = [ 'unit' => '%', 'size' => 31 ];
    $t['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 31 ];
    $t['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];
}

// Right col (photos): 42% desk, 100% tab/mob
$kinner['elements'][1]['settings']['content_width'] = 'full';
$kinner['elements'][1]['settings']['width'] = [ 'unit' => '%', 'size' => 42 ];
$kinner['elements'][1]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$kinner['elements'][1]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

file_put_contents( $elem_dir . '/home-kids.json', json_encode( $kids, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

echo "=== 6. UPDATING 06 PASTOR & PRAYER ===\n";
$pastor = json_decode( file_get_contents( $elem_dir . '/home-pastor-prayer.json' ), true );
$proot = &$pastor['content'][0];
$proot['settings']['content_width'] = 'boxed';
$proot['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$proot['settings']['flex_direction'] = 'row';
$proot['settings']['flex_direction_tablet'] = 'column';
$proot['settings']['flex_direction_mobile'] = 'column';
$proot['settings']['flex_justify_content'] = 'space-between';
$proot['settings']['flex_align_items'] = 'center';

// Left col (photo): 36% desk, 100% tab/mob
$proot['elements'][0]['settings']['content_width'] = 'full';
$proot['elements'][0]['settings']['width'] = [ 'unit' => '%', 'size' => 36 ];
$proot['elements'][0]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$proot['elements'][0]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

// Right col (quote): 60% desk, 100% tab/mob
$proot['elements'][1]['settings']['content_width'] = 'full';
$proot['elements'][1]['settings']['width'] = [ 'unit' => '%', 'size' => 60 ];
$proot['elements'][1]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$proot['elements'][1]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

file_put_contents( $elem_dir . '/home-pastor-prayer.json', json_encode( $pastor, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

echo "=== 7. UPDATING 07 EVENTS & MESSAGES ===\n";
$events = json_decode( file_get_contents( $elem_dir . '/home-events-messages.json' ), true );
$einner = &$events['content'][0]['elements'][0];
$einner['settings']['content_width'] = 'boxed';
$einner['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$einner['settings']['flex_direction'] = 'row';
$einner['settings']['flex_direction_tablet'] = 'column';
$einner['settings']['flex_direction_mobile'] = 'column';
$einner['settings']['flex_justify_content'] = 'space-between';

// Col 1 (events): 47% desk, 100% tab/mob
$einner['elements'][0]['settings']['content_width'] = 'full';
$einner['elements'][0]['settings']['width'] = [ 'unit' => '%', 'size' => 47 ];
$einner['elements'][0]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$einner['elements'][0]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

// Col 2 (messages): 47% desk, 100% tab/mob
$einner['elements'][1]['settings']['content_width'] = 'full';
$einner['elements'][1]['settings']['width'] = [ 'unit' => '%', 'size' => 47 ];
$einner['elements'][1]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$einner['elements'][1]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

file_put_contents( $elem_dir . '/home-events-messages.json', json_encode( $events, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

echo "=== 8. UPDATING 08 STAY IN THE LOOP ===\n";
$loop = json_decode( file_get_contents( $elem_dir . '/home-stay-in-loop.json' ), true );
$lroot = &$loop['content'][0];
$lroot['settings']['content_width'] = 'boxed';
$lroot['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$lroot['settings']['flex_direction'] = 'row';
$lroot['settings']['flex_direction_tablet'] = 'column';
$lroot['settings']['flex_direction_mobile'] = 'column';
$lroot['settings']['flex_justify_content'] = 'space-between';
$lroot['settings']['flex_align_items'] = 'center';

// Left col (info): 48% desk, 100% tab/mob
$lroot['elements'][0]['settings']['content_width'] = 'full';
$lroot['elements'][0]['settings']['width'] = [ 'unit' => '%', 'size' => 48 ];
$lroot['elements'][0]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$lroot['elements'][0]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

// Right col (form): 45% desk, 100% tab/mob
$lroot['elements'][1]['settings']['content_width'] = 'full';
$lroot['elements'][1]['settings']['width'] = [ 'unit' => '%', 'size' => 45 ];
$lroot['elements'][1]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$lroot['elements'][1]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

file_put_contents( $elem_dir . '/home-stay-in-loop.json', json_encode( $loop, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

echo "=== 9. UPDATING FOOTER TEMPLATE ===\n";
$footer = json_decode( file_get_contents( $elem_dir . '/footer-template.json' ), true );
$fgrid = &$footer['content'][0]['elements'][0];
$fgrid['settings']['content_width'] = 'boxed';
$fgrid['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$fgrid['settings']['flex_direction'] = 'row';
$fgrid['settings']['flex_direction_tablet'] = 'row';
$fgrid['settings']['flex_direction_mobile'] = 'column';
$fgrid['settings']['flex_wrap'] = 'wrap';
$fgrid['settings']['flex_justify_content'] = 'space-between';

// Col 1: 32% desk, 45% tab, 100% mob
$fgrid['elements'][0]['settings']['content_width'] = 'full';
$fgrid['elements'][0]['settings']['width'] = [ 'unit' => '%', 'size' => 32 ];
$fgrid['elements'][0]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 45 ];
$fgrid['elements'][0]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

// Col 2: 20% desk, 45% tab, 100% mob
$fgrid['elements'][1]['settings']['content_width'] = 'full';
$fgrid['elements'][1]['settings']['width'] = [ 'unit' => '%', 'size' => 20 ];
$fgrid['elements'][1]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 45 ];
$fgrid['elements'][1]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

// Col 3: 20% desk, 45% tab, 100% mob
$fgrid['elements'][2]['settings']['content_width'] = 'full';
$fgrid['elements'][2]['settings']['width'] = [ 'unit' => '%', 'size' => 20 ];
$fgrid['elements'][2]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 45 ];
$fgrid['elements'][2]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

// Col 4: 18% desk, 45% tab, 100% mob
$fgrid['elements'][3]['settings']['content_width'] = 'full';
$fgrid['elements'][3]['settings']['width'] = [ 'unit' => '%', 'size' => 18 ];
$fgrid['elements'][3]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 45 ];
$fgrid['elements'][3]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

file_put_contents( $elem_dir . '/footer-template.json', json_encode( $footer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

echo "=== 10. UPDATING PRAYER PAGE ===\n";
$prayer = json_decode( file_get_contents( $elem_dir . '/prayer-page.json' ), true );
$proot = &$prayer['content'][0];
$proot['settings']['content_width'] = 'boxed';
$proot['settings']['boxed_width'] = [ 'unit' => 'px', 'size' => 1328 ];
$proot['settings']['flex_direction'] = 'row';
$proot['settings']['flex_direction_tablet'] = 'column';
$proot['settings']['flex_direction_mobile'] = 'column';
$proot['settings']['flex_justify_content'] = 'space-between';
$proot['settings']['flex_align_items'] = 'flex-start';

// Left col: 46% desk, 100% tab/mob
$proot['elements'][0]['settings']['content_width'] = 'full';
$proot['elements'][0]['settings']['width'] = [ 'unit' => '%', 'size' => 46 ];
$proot['elements'][0]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$proot['elements'][0]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

// Right col: 50% desk, 100% tab/mob
$proot['elements'][1]['settings']['content_width'] = 'full';
$proot['elements'][1]['settings']['width'] = [ 'unit' => '%', 'size' => 50 ];
$proot['elements'][1]['settings']['width_tablet'] = [ 'unit' => '%', 'size' => 100 ];
$proot['elements'][1]['settings']['width_mobile'] = [ 'unit' => '%', 'size' => 100 ];

file_put_contents( $elem_dir . '/prayer-page.json', json_encode( $prayer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

echo "=== 11. UPDATING WELCOME PAGE (removing pause button, fixing strip) ===\n";
$welcome = json_decode( file_get_contents( $elem_dir . '/welcome-canvas.json' ), true );
// Remove welcome_btn_pause from top bar
if ( isset( $welcome['content'][0]['elements'][0]['elements'] ) ) {
    $tbar = &$welcome['content'][0]['elements'][0]['elements'];
    $filtered = [];
    foreach ( $tbar as $tb ) {
        if ( $tb['id'] !== 'welcome_btn_pause' ) {
            $filtered[] = $tb;
        }
    }
    $tbar = $filtered;
}
// Strip row layout
$strip = &$welcome['content'][0]['elements'][2];
$strip['settings']['content_width'] = 'full';
$strip['settings']['flex_direction'] = 'row';
$strip['settings']['flex_direction_mobile'] = 'column';
$strip['settings']['flex_justify_content'] = 'space-between';
$strip['settings']['flex_align_items'] = 'center';

file_put_contents( $elem_dir . '/welcome-canvas.json', json_encode( $welcome, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );

echo "All layout keys updated successfully!\n";

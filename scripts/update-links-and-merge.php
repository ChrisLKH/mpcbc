<?php
$elem_dir = __DIR__ . '/../elementor';

// 1. home-hero.json
$hero = file_get_contents( $elem_dir . '/home-hero.json' );
$hero = str_replace( '"url": "/coming-soon/"', '"url": "/visit/"', $hero ); // first is hero_btn_plan
// now watch button:
$hero_json = json_decode( $hero, true );
$hero_json['content'][0]['elements'][3]['elements'][0]['elements'][1]['settings']['link']['url'] = '/watch/';
file_put_contents( $elem_dir . '/home-hero.json', json_encode( $hero_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Updated home-hero.json\n";

// 2. home-times-card.json
$times = file_get_contents( $elem_dir . '/home-times-card.json' );
$times = str_replace( '<a href=\"/coming-soon/\" style=\"display:inline-flex; align-items:center; gap:6px; background:#D92D27; color:#fff; font-weight:700; font-size:12px; padding:2px 10px; border-radius:999px; text-decoration:none;\"><span style=\"width:6px; height:6px; border-radius:50%; background:#fff;\"></span>Live now · Watch</a>', '<a href=\"/watch/\" style=\"display:inline-flex; align-items:center; gap:6px; background:#D92D27; color:#fff; font-weight:700; font-size:12px; padding:2px 10px; border-radius:999px; text-decoration:none;\"><span style=\"width:6px; height:6px; border-radius:50%; background:#fff;\"></span>Live now · Watch</a>', $times );
file_put_contents( $elem_dir . '/home-times-card.json', $times );
echo "Updated home-times-card.json\n";

// 3. home-find-your-place.json
$find = file_get_contents( $elem_dir . '/home-find-your-place.json' );
$find = str_replace( '<a href=\"/coming-soon/\" style=\"color:#ffffff; font-weight:600; font-size:16px; text-decoration:none;\">See Sundays →</a>', '<a href=\"/sundays/\" style=\"color:#ffffff; font-weight:600; font-size:16px; text-decoration:none;\">See Sundays →</a>', $find );
$find = str_replace( '<a href=\"/coming-soon/\" style=\"color:#ffffff; font-weight:600; font-size:16px; text-decoration:none;\">Learn more →</a>', '<a href=\"/zh/\" style=\"color:#ffffff; font-weight:600; font-size:16px; text-decoration:none;\">Learn more →</a>', $find );
$find = str_replace( '<a href=\"#kids\" style=\"color:#ffffff; font-weight:600; font-size:16px; text-decoration:none;\">See Kids →</a>', '<a href=\"/kids/\" style=\"color:#ffffff; font-weight:600; font-size:16px; text-decoration:none;\">See Kids →</a>', $find );
file_put_contents( $elem_dir . '/home-find-your-place.json', $find );
echo "Updated home-find-your-place.json\n";

// 4. home-chinese-band.json
$cband = file_get_contents( $elem_dir . '/home-chinese-band.json' );
$cband = str_replace( '"url": "/coming-soon/"', '"url": "/zh/"', $cband );
file_put_contents( $elem_dir . '/home-chinese-band.json', $cband );
echo "Updated home-chinese-band.json\n";

// 5. home-kids.json
$kids = file_get_contents( $elem_dir . '/home-kids.json' );
$kids = str_replace( '"url": "/coming-soon/"', '"url": "/kids/"', $kids );
file_put_contents( $elem_dir . '/home-kids.json', $kids );
echo "Updated home-kids.json\n";

// 6. home-events-messages.json
$events = file_get_contents( $elem_dir . '/home-events-messages.json' );
// Replace messages links to /watch/, leave events links as /coming-soon/
$events_json = json_decode( $events, true );
$messages_col = &$events_json['content'][0]['elements'][1]['elements'][1];
// widget 1 is latest_messages_list
$msg_html = &$messages_col['elements'][1]['settings']['editor'];
$msg_html = str_replace( 'href="/coming-soon/"', 'href="/watch/"', $msg_html );
// widget 2 is latest_messages_link
$link_html = &$messages_col['elements'][2]['settings']['editor'];
$link_html = str_replace( 'href="/coming-soon/"', 'href="/watch/"', $link_html );

file_put_contents( $elem_dir . '/home-events-messages.json', json_encode( $events_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Updated home-events-messages.json\n";

// Re-run merge-home.php
require __DIR__ . '/merge-home.php';

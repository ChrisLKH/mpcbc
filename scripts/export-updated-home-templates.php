<?php
$_SERVER['SCRIPT_FILENAME'] = 'C:/Users/chris/Local Sites/mpcbc/app/public/index.php';
require_once 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-load.php';

$elem_dir = 'C:/Projects/mpcbc/wordpress/elementor';
$raw = get_post_meta( 21, '_elementor_data', true );
$data = json_decode( $raw, true );

// 1. Export home-full.json
$full = [
    'version'       => '0.4',
    'title'         => 'MPCBC – Home',
    'type'          => 'page',
    'page_settings' => [],
    'content'       => $data,
];
file_put_contents( $elem_dir . '/home-full.json', json_encode( $full, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Exported home-full.json with " . count( $data ) . " sections.\n";

// 2. Export home-hero.json (section 0)
$hero_sec = $data[0];
$hero_tmpl = [
    'version'       => '0.4',
    'title'         => 'MPCBC – Home Hero',
    'type'          => 'section',
    'page_settings' => [],
    'content'       => [ $hero_sec ],
];
file_put_contents( $elem_dir . '/home-hero.json', json_encode( $hero_tmpl, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Exported home-hero.json.\n";

// 3. Export home-times-card.json (section 1)
$times_sec = $data[1];
$times_tmpl = [
    'version'       => '0.4',
    'title'         => 'MPCBC – Sunday Times Card',
    'type'          => 'section',
    'page_settings' => [],
    'content'       => [ $times_sec ],
];
file_put_contents( $elem_dir . '/home-times-card.json', json_encode( $times_tmpl, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Exported home-times-card.json.\n";

// 4. Export home-live-section.json (section 2)
$live_sec = $data[2];
$live_tmpl = [
    'version'       => '0.4',
    'title'         => 'MPCBC – Front-Page Live Player',
    'type'          => 'section',
    'page_settings' => [],
    'content'       => [ $live_sec ],
];
file_put_contents( $elem_dir . '/home-live-section.json', json_encode( $live_tmpl, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Exported home-live-section.json.\n";

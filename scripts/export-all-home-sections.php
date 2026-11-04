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

// Map of sections to individual files
$map = [
    0 => [ 'file' => 'home-hero.json',            'title' => 'MPCBC – Home Hero' ],
    1 => [ 'file' => 'home-times-card.json',       'title' => 'MPCBC – Sunday Times Card' ],
    2 => [ 'file' => 'home-live-section.json',     'title' => 'MPCBC – Watch Live & Latest Messages' ],
    3 => [ 'file' => 'home-find-your-place.json',  'title' => 'MPCBC – Find Your Place' ],
    4 => [ 'file' => 'home-kids.json',             'title' => 'MPCBC – Kids & Youth' ],
    5 => [ 'file' => 'home-pastor-prayer.json',    'title' => 'MPCBC – Pastor & Prayer' ],
    6 => [ 'file' => 'home-events-messages.json',  'title' => 'MPCBC – Coming Up & Stay in the Loop' ],
    7 => [ 'file' => 'home-chinese-band.json',     'title' => 'MPCBC – Chinese Strip' ],
];

foreach ( $map as $idx => $info ) {
    if ( isset( $data[$idx] ) ) {
        $tmpl = [
            'version'       => '0.4',
            'title'         => $info['title'],
            'type'          => 'section',
            'page_settings' => [],
            'content'       => [ $data[$idx] ],
        ];
        file_put_contents( $elem_dir . '/' . $info['file'], json_encode( $tmpl, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
        echo "Exported {$info['file']}.\n";
    }
}

<?php
$sections = [
    'home-hero.json',
    'home-times-card.json',
    'home-find-your-place.json',
    'home-chinese-band.json',
    'home-kids.json',
    'home-pastor-prayer.json',
    'home-events-messages.json',
    'home-stay-in-loop.json',
];

$all_content = [];
$elem_dir = __DIR__ . '/../elementor';

foreach ( $sections as $file ) {
    $path = $elem_dir . '/' . $file;
    $data = json_decode( file_get_contents( $path ), true );
    if ( ! $data || ! isset( $data['content'] ) ) {
        echo "Error reading $file: " . json_last_error_msg() . "\n";
        continue;
    }
    foreach ( $data['content'] as $c ) {
        $all_content[] = $c;
    }
}

$full = [
    'version'       => '0.4',
    'title'         => 'MPCBC – Home',
    'type'          => 'page',
    'page_settings' => [],
    'content'       => $all_content,
];

$target = $elem_dir . '/home-full.json';
file_put_contents( $target, json_encode( $full, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Merged " . count($all_content) . " section containers into home-full.json successfully.\n";

// Verify it decodes cleanly
$verify = json_decode( file_get_contents( $target ), true );
echo "Verification: " . ( $verify ? "Valid JSON with " . count($verify['content']) . " containers" : "INVALID JSON" ) . "\n";

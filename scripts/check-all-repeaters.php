<?php
$dir = __DIR__ . '/../elementor';
$files = glob( $dir . '/*.json' );

function check_repeaters( $elements, $filename ) {
    foreach ( $elements as $el ) {
        $widget = $el['widgetType'] ?? '';
        $settings = $el['settings'] ?? [];
        foreach ( $settings as $k => $v ) {
            if ( in_array( $k, ['icon_list', 'tabs', 'slides', 'eael_fancy_text_strings', 'social_icon_list'] ) ) {
                if ( ! is_array( $v ) ) {
                    echo "ERROR in $filename (widget: $widget): setting '$k' is not an array! Value: " . print_r($v, true) . "\n";
                } else {
                    echo "OK in " . basename($filename) . ": widget '$widget' setting '$k' is an array with " . count($v) . " items.\n";
                }
            }
        }
        if ( ! empty( $el['elements'] ) ) {
            check_repeaters( $el['elements'], $filename );
        }
    }
}

foreach ( $files as $file ) {
    $json = json_decode( file_get_contents( $file ), true );
    if ( ! $json ) continue;
    $content = $json['content'] ?? [];
    check_repeaters( $content, $file );
}

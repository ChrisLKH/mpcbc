<?php
$data = json_decode( file_get_contents( __DIR__ . '/../elementor/home-full.json' ), true );
$content = $data['content'];

function find_invalid( $elements, $path = '' ) {
    foreach ( $elements as $idx => $el ) {
        $current_path = "$path > [$idx]";
        if ( ! is_array( $el ) ) {
            echo "NON-ARRAY element at $current_path: " . print_r($el, true) . "\n";
            continue;
        }
        if ( ! isset( $el['elType'] ) ) {
            echo "MISSING elType at $current_path: " . json_encode($el) . "\n";
        }
        if ( isset( $el['elements'] ) ) {
            if ( ! is_array( $el['elements'] ) ) {
                echo "ELEMENTS is not an array at $current_path: " . print_r($el['elements'], true) . "\n";
            } else {
                find_invalid( $el['elements'], $current_path . " (" . ($el['id'] ?? '') . ")" );
            }
        }
    }
}

find_invalid( $content, 'root' );
echo "Done checking home-full.json\n";

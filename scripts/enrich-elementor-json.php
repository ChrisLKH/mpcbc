<?php
$elementor_dir = __DIR__ . '/../elementor';

$files = glob( $elementor_dir . '/*.json' );

function enrich_element( &$el ) {
    if ( ! isset( $el['isInner'] ) ) {
        $el['isInner'] = false;
    }
    if ( ! isset( $el['elements'] ) ) {
        $el['elements'] = [];
    }

    if ( ! isset( $el['settings'] ) ) {
        $el['settings'] = [];
    }

    $globals = isset( $el['settings']['__globals__'] ) ? $el['settings']['__globals__'] : [];

    // Map background_color
    if ( isset( $el['settings']['background_color'] ) ) {
        $c = strtoupper( $el['settings']['background_color'] );
        if ( $c === '#0E6B5C' ) {
            $globals['background_color'] = 'globals/colors?id=primary';
        } elseif ( $c === '#F4B942' ) {
            $globals['background_color'] = 'globals/colors?id=accent';
        } elseif ( $c === '#12110E' ) {
            $globals['background_color'] = 'globals/colors?id=secondary';
        } elseif ( $c === '#FAF7F2' ) {
            $globals['background_color'] = 'globals/colors?id=paper';
        } elseif ( $c === '#EDE6DA' ) {
            $globals['background_color'] = 'globals/colors?id=stone';
        }
    }

    // Map title_color
    if ( isset( $el['settings']['title_color'] ) ) {
        $c = strtoupper( $el['settings']['title_color'] );
        if ( $c === '#0E6B5C' ) {
            $globals['title_color'] = 'globals/colors?id=primary';
        } elseif ( $c === '#16140F' ) {
            $globals['title_color'] = 'globals/colors?id=text';
        } elseif ( $c === '#12110E' ) {
            $globals['title_color'] = 'globals/colors?id=secondary';
        }
    }

    if ( ! empty( $globals ) ) {
        $el['settings']['__globals__'] = $globals;
    }

    if ( ! empty( $el['elements'] ) ) {
        foreach ( $el['elements'] as &$child ) {
            enrich_element( $child );
        }
    }
}

foreach ( $files as $f ) {
    if ( basename( $f ) === 'real-exported-sample.json' ) continue;

    $json = json_decode( file_get_contents( $f ), true );
    if ( ! $json || ! isset( $json['content'] ) ) continue;

    if ( ! isset( $json['page_settings'] ) ) {
        $json['page_settings'] = [];
    }
    if ( ! isset( $json['version'] ) ) {
        $json['version'] = '0.4';
    }

    foreach ( $json['content'] as &$item ) {
        enrich_element( $item );
    }

    file_put_contents( $f, json_encode( $json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
    echo "Enriched " . basename( $f ) . "\n";
}

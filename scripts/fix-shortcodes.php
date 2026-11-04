<?php
$files = [
    __DIR__ . '/../elementor/prayer-page.json',
    __DIR__ . '/../elementor/home-stay-in-loop.json',
    __DIR__ . '/../elementor/home-full.json'
];

foreach ( $files as $f ) {
    $c = file_get_contents( $f );
    $c = str_replace( '[fluentform id=\\"1\\"]', '[fluentform id=\\"3\\"]', $c );
    $c = str_replace( '[fluentform id=\\"2\\"]', '[fluentform id=\\"4\\"]', $c );
    $c = str_replace( '[fluentform id="1"]', '[fluentform id="3"]', $c );
    $c = str_replace( '[fluentform id="2"]', '[fluentform id="4"]', $c );
    file_put_contents( $f, $c );
}
echo "Replaced shortcodes in JSON files successfully\n";

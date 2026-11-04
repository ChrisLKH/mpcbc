<?php
/**
 * Generate placeholders using GD in PHP
 */
$outputDir = 'C:/Projects/mpcbc/wordpress-design-b/placeholders';
if ( ! file_exists( $outputDir ) ) {
    mkdir( $outputDir, 0777, true );
}

$images = [
    [ 'name' => 'pastor-portrait.png', 'w' => 500, 'h' => 500, 'bg' => [248, 243, 244], 'fg' => [107, 85, 96], 'lines' => ['pastor portrait', 'square crop'] ],
    [ 'name' => 'pastor-cantonese.png', 'w' => 480, 'h' => 600, 'bg' => [34, 20, 25], 'fg' => [255, 255, 255], 'lines' => ['Photo of the Cantonese', 'congregation pastor'] ],
    [ 'name' => 'pastor-english.png', 'w' => 480, 'h' => 600, 'bg' => [34, 20, 25], 'fg' => [255, 255, 255], 'lines' => ['Photo of the English', 'congregation pastor'] ],
    [ 'name' => 'pastor-mandarin.png', 'w' => 480, 'h' => 600, 'bg' => [34, 20, 25], 'fg' => [255, 255, 255], 'lines' => ['Photo of the Mandarin', 'congregation pastor'] ],
    [ 'name' => 'thumb-cantonese.png', 'w' => 640, 'h' => 360, 'bg' => [27, 16, 20], 'fg' => [255, 255, 255], 'lines' => ['YOUTUBE · SCHEDULED', '', 'Cantonese Channel'] ],
    [ 'name' => 'thumb-english.png', 'w' => 640, 'h' => 360, 'bg' => [27, 16, 20], 'fg' => [255, 255, 255], 'lines' => ['YOUTUBE · LIVE NOW', '', 'English Channel'] ],
    [ 'name' => 'thumb-mandarin.png', 'w' => 640, 'h' => 360, 'bg' => [27, 16, 20], 'fg' => [255, 255, 255], 'lines' => ['YOUTUBE · LAST RECORDING', '', 'Mandarin Channel'] ],
    [ 'name' => 'kids-room.png', 'w' => 640, 'h' => 360, 'bg' => [255, 255, 255], 'fg' => [107, 85, 96], 'lines' => ['Sunday school room, wide shot', 'children and teacher'] ],
    [ 'name' => 'kids-craft.png', 'w' => 400, 'h' => 400, 'bg' => [255, 255, 255], 'fg' => [107, 85, 96], 'lines' => ['craft table'] ],
    [ 'name' => 'kids-nursery.png', 'w' => 400, 'h' => 400, 'bg' => [255, 255, 255], 'fg' => [107, 85, 96], 'lines' => ['nursery corner'] ],
];

foreach ( $images as $img ) {
    $im = imagecreatetruecolor( $img['w'], $img['h'] );
    $bgCol = imagecolorallocate( $im, $img['bg'][0], $img['bg'][1], $img['bg'][2] );
    $fgCol = imagecolorallocate( $im, $img['fg'][0], $img['fg'][1], $img['fg'][2] );
    $borderCol = imagecolorallocate( $im, 227, 211, 216 );

    imagefilledrectangle( $im, 0, 0, $img['w'], $img['h'], $bgCol );
    imagerectangle( $im, 0, 0, $img['w'] - 1, $img['h'] - 1, $borderCol );

    $font = 5; // Built-in font
    $fontHeight = imagefontheight( $font );
    $fontWidth = imagefontwidth( $font );
    $totalHeight = count( $img['lines'] ) * ( $fontHeight + 8 );
    $startY = ( $img['h'] - $totalHeight ) / 2;

    foreach ( $img['lines'] as $i => $line ) {
        $textWidth = strlen( $line ) * $fontWidth;
        $x = ( $img['w'] - $textWidth ) / 2;
        $y = $startY + ( $i * ( $fontHeight + 8 ) );
        imagestring( $im, $font, (int)$x, (int)$y, $line, $fgCol );
    }

    $savePath = $outputDir . '/' . $img['name'];
    imagepng( $im, $savePath );
    imagedestroy( $im );
    echo "Generated: " . $img['name'] . "\n";
}

<?php
$font_cjk = 'C:/Windows/Fonts/msyh.ttc';
$font_en = 'C:/Windows/Fonts/segoeui.ttf';

function draw_centered($im, $size, $font, $text, $y, $color, $w) {
    $box = imagettfbbox($size, 0, $font, $text);
    $text_w = $box[2] - $box[0];
    $x = ($w - $text_w) / 2;
    imagettftext($im, $size, 0, $x, $y, $color, $font, $text);
}

$items = [
    [
        'file'  => 'live-thumb-english.png',
        'title' => 'Live Stream · English Service',
        'sub'   => 'Sundays 11:00 AM',
        'note'  => '16:9 Labelled Placeholder Thumbnail',
    ],
    [
        'file'  => 'live-thumb-chinese.png',
        'title' => 'Live Stream · 中文直播',
        'sub'   => '粵語 9:30 AM · 國語 11:30 AM',
        'note'  => '16:9 Labelled Placeholder Thumbnail',
    ],
];

foreach ($items as $item) {
    $w = 1280;
    $h = 720;
    $im = imagecreatetruecolor($w, $h);

    $bg = imagecolorallocate($im, 18, 17, 14); // #12110E
    imagefilledrectangle($im, 0, 0, $w, $h, $bg);

    // Subtle grid dots
    $dot = imagecolorallocate($im, 38, 36, 32);
    for ($x = 0; $x < $w; $x += 24) {
        for ($y = 0; $y < $h; $y += 24) {
            imagesetpixel($im, $x, $y, $dot);
        }
    }

    // Border
    $border = imagecolorallocate($im, 55, 52, 45);
    imagerectangle($im, 2, 2, $w - 3, $h - 3, $border);

    $white = imagecolorallocate($im, 240, 240, 240);
    $sub_color = imagecolorallocate($im, 180, 175, 165);
    $note_color = imagecolorallocate($im, 120, 115, 105);

    draw_centered($im, 28, $font_cjk, $item['title'], 310, $white, $w);
    draw_centered($im, 20, $font_cjk, $item['sub'], 370, $sub_color, $w);
    draw_centered($im, 14, $font_en, $item['note'], 430, $note_color, $w);

    $dest1 = 'C:/Projects/mpcbc/wordpress/placeholders/' . $item['file'];
    $dest2 = 'C:/Users/chris/Local Sites/mpcbc/app/public/wp-content/uploads/2026/10/' . $item['file'];

    imagepng($im, $dest1);
    imagepng($im, $dest2);
    imagedestroy($im);

    echo "Generated crisp thumbnail: {$item['file']}\n";
}
